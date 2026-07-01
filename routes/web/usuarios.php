<?php

use App\Http\Controllers\Web\UsuarioController;
use Illuminate\Support\Facades\Route;

// Acceso: Administrador o permiso assign_roles
Route::middleware(['auth', 'role_or_permission:Administrador|assign_roles'])
    ->prefix('seguridad')
    ->name('seguridad.')
    ->group(function (): void {
        Route::get('/', fn () => redirect()->route('seguridad.usuarios.index'))->name('index');

        Route::prefix('usuarios')->name('usuarios.')->group(function () {
            Route::get('/',               [UsuarioController::class, 'index'])->name('index');
            Route::get('/{usuario}',      [UsuarioController::class, 'edit'])->name('edit');
            Route::put('/{usuario}',      [UsuarioController::class, 'update'])->name('update');
        });
    });
