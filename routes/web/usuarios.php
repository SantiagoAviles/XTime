<?php

use Illuminate\Support\Facades\Route;

// Acceso: solo Administrador o permiso assign_roles
Route::middleware(['auth', 'role_or_permission:Administrador|assign_roles'])
    ->prefix('seguridad')
    ->name('seguridad.')
    ->group(function (): void {
        // TODO: Implementar gestión de usuarios y roles en Sprint 1 - Bloque 5.
        // Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        // Route::get('/roles', [RolController::class, 'index'])->name('roles.index');
    });
