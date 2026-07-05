<?php

use App\Http\Controllers\Web\TurnoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('turnos')
    ->name('turnos.')
    ->group(function (): void {
        // Rutas estáticas primero: deben registrarse antes que '/{turno}' para no
        // que Laravel intente resolverlas como un id de turno (ej. "crear") y 404.
        Route::middleware('role_or_permission:Administrador|RRHH|view_turnos|manage_turnos')
            ->get('/', [TurnoController::class, 'index'])->name('index');

        Route::middleware('role_or_permission:Administrador|RRHH|manage_turnos')->group(function () {
            Route::get('/crear',    [TurnoController::class, 'create'])->name('create');
            Route::post('/',        [TurnoController::class, 'store'])->name('store');
            Route::post('/asignar', [TurnoController::class, 'asignar'])->name('asignar');
        });

        Route::middleware('role_or_permission:Administrador|RRHH|view_turnos|manage_turnos')
            ->get('/{turno}', [TurnoController::class, 'show'])->name('show');

        Route::middleware('role_or_permission:Administrador|RRHH|manage_turnos')->group(function () {
            Route::get('/{turno}/editar', [TurnoController::class, 'edit'])->name('edit');
            Route::put('/{turno}',        [TurnoController::class, 'update'])->name('update');
            Route::delete('/{turno}',     [TurnoController::class, 'destroy'])->name('destroy');
        });
    });
