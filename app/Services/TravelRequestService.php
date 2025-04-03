<?php

namespace App\Services;

use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TravelRequestService
{
    public function filter(Request $request)
    {
        Log::info('[TravelRequestService@filter] Filtros recebidos:', $request->all());

        $query = TravelRequest::query();

        if ($request->status) $query->where('status', $request->status);
        if ($request->destination) $query->where('destination', 'like', "%{$request->destination}%");

        if ($request->from_date && $request->to_date) {
            $query->where(function ($q) use ($request) {
                $q->whereBetween('departure_date', [$request->from_date, $request->to_date])
                    ->orWhereBetween('return_date', [$request->from_date, $request->to_date]);
            });
        }

        return $query->get();
    }

    public function get(string $id): TravelRequest
    {
        return TravelRequest::findOrFail($id);
    }

    public function create(array $data): TravelRequest
    {
        $data['id'] = (string) Str::uuid();
        $data['user_id'] = auth()->id();
        $data['status'] = 'solicitado';
        return TravelRequest::create($data);
    }

    public function updateStatus(string $id, string $status): TravelRequest
    {
        $travel = TravelRequest::findOrFail($id);

        if ($status === 'cancelado' && $travel->status !== 'aprovado') {
            abort(422, 'Somente pedidos aprovados podem ser cancelados.');
        }

        $travel->status = $status;

        try {
            $user = User::find($travel->user_id);
            $email = $user ? $user->email : 'desconhecido@onfly.dev';
            Log::info("[NOTIFICAÇÃO FAKE] Pedido #{$travel->id} foi {$status} → {$email}");
            $travel->notified = true;
        } catch (\Exception $e) {
            Log::warning("[NOTIFICAÇÃO FALHOU] #{$travel->id}: {$e->getMessage()}");
            $travel->notified = false;
        }

        $travel->save();
        return $travel;
    }
}
