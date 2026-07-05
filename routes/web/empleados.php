<?php

use App\Http\Controllers\Web\EmpleadoController;
use Illuminate\Support\Facades\Route;

// Acceso: Administrador, RRHH o permisos de empleados
Route::middleware(['auth', 'role_or_permission:Administrador|RRHH|view_employees'])
    ->prefix('empleados')
    ->name('empleados.')
    ->group(function (): void {
        Route::get('/',                  [EmpleadoController::class, 'index'])->name('index');
        Route::get('/crear',             [EmpleadoController::class, 'create'])->name('create')
            ->middleware('role_or_permission:Administrador|RRHH|create_employees');
        Route::post('/',                 [EmpleadoController::class, 'store'])->name('store')
            ->middleware('role_or_permission:Administrador|RRHH|create_employees');
        Route::get('/{empleado}',        [EmpleadoController::class, 'show'])->name('show')->withTrashed();
        Route::get('/{empleado}/editar', [EmpleadoController::class, 'edit'])->name('edit')
            ->middleware('role_or_permission:Administrador|RRHH|edit_employees');
        Route::put('/{empleado}',        [EmpleadoController::class, 'update'])->name('update')
            ->middleware('role_or_permission:Administrador|RRHH|edit_employees');
        Route::delete('/{empleado}',     [EmpleadoController::class, 'destroy'])->name('destroy')
            ->middleware('role_or_permission:Administrador|RRHH|delete_employees');
        Route::post('/{empleado}/restaurar', [EmpleadoController::class, 'restore'])->name('restore')
            ->middleware('role_or_permission:Administrador|RRHH|delete_employees');
    });
