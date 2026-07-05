<?php

use App\Http\Controllers\Web\FeriadoController;
use App\Http\Controllers\Web\ReglaHoraExtraController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role_or_permission:Administrador|RRHH|manage_feriados|manage_reglas_horas_extra'])
    ->group(function (): void {
        Route::prefix('feriados')->name('feriados.')->group(function () {
            Route::get('/',             [FeriadoController::class, 'index'])->name('index');
            Route::post('/',            [FeriadoController::class, 'store'])->name('store');
            Route::put('/{feriado}',    [FeriadoController::class, 'update'])->name('update');
            Route::delete('/{feriado}', [FeriadoController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('reglas-horas-extra')->name('reglas-horas-extra.')->group(function () {
            Route::get('/',          [ReglaHoraExtraController::class, 'index'])->name('index');
            Route::post('/',         [ReglaHoraExtraController::class, 'store'])->name('store');
            Route::put('/{regla}',   [ReglaHoraExtraController::class, 'update'])->name('update');
            Route::delete('/{regla}',[ReglaHoraExtraController::class, 'destroy'])->name('destroy');
        });
    });
