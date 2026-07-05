<?php

use App\Http\Controllers\Web\AuditoriaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role_or_permission:Administrador|view_activity_log'])
    ->prefix('auditoria')->name('auditoria.')
    ->group(function (): void {
        Route::get('/', [AuditoriaController::class, 'index'])->name('index');
    });
