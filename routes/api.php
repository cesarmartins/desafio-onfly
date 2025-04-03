<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TravelRequestController;

Route::get('/teste-api', function () {
    return response()->json(['msg' => 'Chamada da API Funcionando!']);
});

//Route::middleware('auth:api')->group(function () {
    Route::get('travel-requests', [TravelRequestController::class, 'index']);
    Route::get('travel-requests/{id}', [TravelRequestController::class, 'show']);
    Route::post('travel-requests', [TravelRequestController::class, 'store']);
    Route::patch('travel-requests/{id}/status', [TravelRequestController::class, 'updateStatus']);
//});
