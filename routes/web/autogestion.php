<?php

use App\Http\Controllers\Web\AutogestionController;
use App\Http\Controllers\Web\AutogestionJustificacionController;
use Illuminate\Support\Facades\Route;

// Portal del empleado (solo accede el dueño de los datos)
Route::middleware(['auth', 'role_or_permission:Empleado|Administrador|RRHH|view_self_portal'])
    ->prefix('autogestion')
    ->name('autogestion.')
    ->group(function (): void {
        Route::get('/',            [AutogestionController::class, 'dashboard'])->name('dashboard');
        Route::get('/historial',   [AutogestionController::class, 'historial'])->name('historial');
        Route::get('/reporte.pdf', [AutogestionController::class, 'reportePersonalPdf'])->name('reporte');

        Route::prefix('justificaciones')->name('justificaciones.')->group(function () {
            Route::get('/',      [AutogestionJustificacionController::class, 'index'])->name('index');
            Route::get('/nueva', [AutogestionJustificacionController::class, 'create'])->name('create');
            Route::post('/',     [AutogestionJustificacionController::class, 'store'])->name('store');
        });
    });
