<?php

use App\Http\Controllers\Web\ReporteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role_or_permission:Administrador|RRHH|Jefe de Operaciones|Supervisor|view_reports|view_reports_own_area'])
    ->prefix('reportes')->name('reportes.')
    ->group(function (): void {
        Route::get('/asistencia',       [ReporteController::class, 'asistencia'])->name('asistencia');
        Route::get('/asistencia.pdf',   [ReporteController::class, 'asistenciaPdf'])->name('asistencia.pdf');
        Route::get('/asistencia.xlsx',  [ReporteController::class, 'asistenciaExcel'])->name('asistencia.excel');
        Route::get('/horas-extra',      [ReporteController::class, 'horasExtra'])->name('horas-extra');
        Route::get('/incidencias',      [ReporteController::class, 'incidencias'])->name('incidencias');
    });
