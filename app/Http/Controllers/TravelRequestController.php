<?php
namespace App\Http\Controllers;

use App\Models\TravelRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class TravelRequestController extends Controller
{
    // Listar todos os pedidos, com filtro por status
    public function index(Request $request)
    {
        $query = TravelRequest::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('destination')) {
            $query->where('destination', 'like', '%' . $request->destination . '%');
        }

        if ($request->has(['from_date', 'to_date'])) {
            $query->where(function ($q) use ($request) {
                $q->whereBetween('departure_date', [$request->from_date, $request->to_date])
                    ->orWhereBetween('return_date', [$request->from_date, $request->to_date]);
            });
        }
        return response()->json($query->get());
    }

    // Mostrar um pedido específico
    public function show($id)
    {
        try {
            $request = TravelRequest::findOrFail($id);
            return response()->json($request);
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar viagem: ' . $e->getMessage());
            return response()->json([
                'error' => 'Erro ao buscar pedido de viagem.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    // Criar novo pedido de viagem
    public function store(Request $request)
    {
        $data = $request->validate([
            'requester_name' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:departure_date',
        ]);

        //$data['user_id'] = Auth::id();
        $data['user_id'] = $request->get('user_id');

        $data['status'] = 'solicitado';

        $travel = TravelRequest::create($data);

        return response()->json($travel, 201);
    }

    // Atualizar status (apenas se não for o solicitante)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:aprovado,cancelado'
        ]);

        $travel = TravelRequest::findOrFail($id);

        if ($request->status === 'cancelado' && $travel->status !== 'aprovado') {
            return response()->json([
                'error' => 'Somente pedidos aprovados podem ser cancelados.'], 422);
        }

        $travel->status = $request->status;

        try {
            $user = User::find($travel->user_id);
            $email = $user ? $user->email : 'desconhecido@onfly.dev';

            // simulação da notificação
            Log::info("[NOTIFICAÇÃO FAKE] O pedido de viagem #{$travel->id} foi {$travel->status} e uma notificação foi enviada para {$email}");

            $travel->notified = true;
        } catch (\Exception $e) {
            Log::warning("[NOTIFICAÇÃO FALHOU] Não foi possível notificar sobre o pedido #{$travel->id}: {$e->getMessage()}");
            $travel->notified = false;
        }

        $travel->save();
        return response()->json($travel);
    }
}
