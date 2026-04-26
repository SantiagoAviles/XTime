<?php

use Illuminate\Support\Facades\Route;

// Acceso: Administrador, RRHH o permiso manage_areas
Route::middleware(['auth', 'role_or_permission:Administrador|RRHH|manage_areas'])
    ->prefix('areas')
    ->name('areas.')
    ->group(function (): void {
        // TODO: Implementar gestión de áreas en Sprint 1 - Bloque 4.
        // Route::get('/', [AreaController::class, 'index'])->name('index');
        // Route::get('/crear', [AreaController::class, 'create'])->name('create');
        // Route::post('/', [AreaController::class, 'store'])->name('store');
        // Route::get('/{area}/editar', [AreaController::class, 'edit'])->name('edit');
        // Route::put('/{area}', [AreaController::class, 'update'])->name('update');
        // Route::delete('/{area}', [AreaController::class, 'destroy'])->name('destroy');
    });
