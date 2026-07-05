<?php

use App\Http\Controllers\Web\KioscoController;
use App\Http\Controllers\Web\MarcacionManualController;
use App\Http\Controllers\Web\PanelAsistenciaController;
use App\Http\Controllers\Web\QrEmpleadoController;
use Illuminate\Support\Facades\Route;

Route::prefix('asistencias')->name('asistencias.')->group(function (): void {
    // Kiosko sin login — autenticación de marca por QR/DNI del empleado.
    Route::get('/kiosko', [KioscoController::class, 'vista'])->name('kiosko');

    Route::middleware(['auth'])->group(function () {
        // Panel tiempo real
        Route::middleware('role_or_permission:Administrador|RRHH|Jefe de Operaciones|Supervisor|view_attendance_panel|view_attendance_own_area')
            ->group(function () {
                Route::get('/panel',      [PanelAsistenciaController::class, 'index'])->name('panel');
                Route::get('/panel.json', [PanelAsistenciaController::class, 'json'])->name('panel.json');
            });

        // QR del empleado
        Route::middleware('role_or_permission:Administrador|RRHH')->group(function () {
            Route::get('/empleados/{empleado}/qr',     [QrEmpleadoController::class, 'imprimible'])->name('empleados.qr');
            Route::get('/empleados/{empleado}/qr.svg', [QrEmpleadoController::class, 'svg'])->name('empleados.qr.svg');
        });

        // Marcación manual (solo RRHH/Admin)
        Route::middleware('role_or_permission:Administrador|RRHH|mark_attendance_manual')
            ->prefix('manual')->name('manual.')->group(function () {
                Route::get('/',  [MarcacionManualController::class, 'create'])->name('create');
                Route::post('/', [MarcacionManualController::class, 'store'])->name('store');
            });
    });
});
