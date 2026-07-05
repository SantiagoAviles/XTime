<?php

use App\Http\Controllers\Web\JustificacionController;
use Illuminate\Support\Facades\Route;

// Bandeja de revisión de justificaciones (Supervisor / RRHH / Administrador)
Route::middleware(['auth', 'role_or_permission:Administrador|RRHH|Supervisor|approve_justifications|manage_justifications'])
    ->prefix('justificaciones')->name('justificaciones.')
    ->group(function (): void {
        Route::get('/',                          [JustificacionController::class, 'index'])->name('index');
        Route::post('/{justificacion}/aprobar',  [JustificacionController::class, 'aprobar'])->name('aprobar');
        Route::post('/{justificacion}/rechazar', [JustificacionController::class, 'rechazar'])->name('rechazar');
    });
