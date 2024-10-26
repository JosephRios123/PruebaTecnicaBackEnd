<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FlightController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/airports', [FlightController::class, 'obtenerAeropuertos']);
Route::post('/flights', [FlightController::class, 'obtenerVuelos']);
Route::post('/reserve', [FlightController::class, 'guardarReservas']);
Route::get('/obtenerReservas', [FlightController::class, 'obtenerReservas']);
