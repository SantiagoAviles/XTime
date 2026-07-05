<?php

use App\Http\Controllers\Api\MarcacionController;
use Illuminate\Support\Facades\Route;

// Endpoint público para el kiosko (validación por QR/DNI del empleado).
// Throttle agresivo para mitigar abuso.
Route::middleware('throttle:60,1')->group(function () {
    Route::post('/asistencias/marcar', [MarcacionController::class, 'registrar'])
        ->name('api.asistencias.marcar');
});
