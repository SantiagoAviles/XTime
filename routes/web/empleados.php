<?php

use Illuminate\Support\Facades\Route;

// Acceso: Administrador, RRHH o permiso view_employees / create_employees / edit_employees / delete_employees
Route::middleware(['auth', 'role_or_permission:Administrador|RRHH|view_employees'])
    ->prefix('empleados')
    ->name('empleados.')
    ->group(function (): void {
        // TODO: Implementar CRUD completo de empleados en Sprint 1 - Bloque 4.
        // Route::get('/', [EmpleadoController::class, 'index'])->name('index');
        // Route::get('/crear', [EmpleadoController::class, 'create'])->name('create');
        // Route::post('/', [EmpleadoController::class, 'store'])->name('store');
        // Route::get('/{empleado}/editar', [EmpleadoController::class, 'edit'])->name('edit');
        // Route::put('/{empleado}', [EmpleadoController::class, 'update'])->name('update');
        // Route::delete('/{empleado}', [EmpleadoController::class, 'destroy'])->name('destroy');
    });
