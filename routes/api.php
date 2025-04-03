<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TravelRequestController;
use App\Http\Controllers\AuthController;

Route::get('/teste-api', function () {
    return response()->json(['msg' => 'Chamada da API Funcionando!']);
});

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('travel-requests', [TravelRequestController::class, 'index']);
    Route::get('travel-requests/{id}', [TravelRequestController::class, 'show']);
    Route::post('travel-requests', [TravelRequestController::class, 'store']);
    Route::patch('travel-requests/{id}/status', [TravelRequestController::class, 'updateStatus']);
});
