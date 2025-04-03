<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreTravelRequest;
use App\Http\Requests\UpdateTravelStatusRequest;
use App\Http\Resources\TravelRequestResource;
use App\Services\TravelRequestService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Microserviço de Viagens Corporativas",
 *     description="API para gerenciamento de pedidos de viagem",
 *     @OA\Contact(
 *         email="cesar@onfly.dev"
 *     )
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Token de acesso via Sanctum",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="sanctum"
 * )
 */
class TravelRequestController extends Controller
{
    protected TravelRequestService $service;

    public function __construct(TravelRequestService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *     path="/api/travel-requests",
     *     summary="Listar todos os pedidos de viagem",
     *     tags={"Travel Requests"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="status", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="destination", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="from_date", in="query", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="to_date", in="query", required=false, @OA\Schema(type="string", format="date")),
     *     @OA\Response(response=200, description="Lista de pedidos")
     * )
     */
    public function index(Request $request)
    {
        Log::debug("Controller Index");
        $travels = $this->service->filter($request);
        return TravelRequestResource::collection($travels);
    }

    /**
     * @OA\Get(
     *     path="/api/travel-requests/{id}",
     *     summary="Consultar pedido de viagem por ID",
     *     tags={"Travel Requests"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Detalhes do pedido")
     * )
     */
    public function show(string $id)
    {
        Log::debug("Controller Show");
        return new TravelRequestResource($this->service->get($id));
    }

    /**
     * @OA\Post(
     *     path="/api/travel-requests",
     *     summary="Criar um novo pedido de viagem",
     *     tags={"Travel Requests"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"requester_name", "destination", "departure_date", "return_date"},
     *             @OA\Property(property="requester_name", type="string"),
     *             @OA\Property(property="destination", type="string"),
     *             @OA\Property(property="departure_date", type="string", format="date"),
     *             @OA\Property(property="return_date", type="string", format="date")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Pedido criado com sucesso")
     * )
     */
    public function store(StoreTravelRequest $request)
    {
        Log::debug("Controller Store");
        $travel = $this->service->create($request->validated());
        return new TravelRequestResource($travel);
    }

    /**
     * @OA\Patch(
     *     path="/api/travel-requests/{id}/status",
     *     summary="Atualizar status do pedido de viagem",
     *     tags={"Travel Requests"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"aprovado", "cancelado"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Status atualizado com sucesso")
     * )
     */
    public function updateStatus(UpdateTravelStatusRequest $request, string $id)
    {
        try {
            Log::debug("Controller updateStatus");
            $travel = $this->service->updateStatus($id, $request->validated('status'));
            return new TravelRequestResource($travel);
        } catch (\Exception $e) {
            throw new HttpResponseException(response()->json([
                'message' => 'Não foi possível atualizar o status.',
                'error' => $e->getMessage(),
            ], 422));
        }
    }
}
