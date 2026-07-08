<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return auth()->user()->hasRole('Empleado')
        ? redirect()->route('autogestion.dashboard')
        : redirect()->route('dashboard');
})->name('home');

require __DIR__.'/auth.php';
require __DIR__.'/web/dashboard.php';
require __DIR__.'/web/areas.php';
require __DIR__.'/web/asistencias.php';
require __DIR__.'/web/horarios.php';
require __DIR__.'/web/turnos.php';
require __DIR__.'/web/empleados.php';
require __DIR__.'/web/reportes.php';
require __DIR__.'/web/autogestion.php';
require __DIR__.'/web/justificaciones.php';
require __DIR__.'/web/usuarios.php';
require __DIR__.'/web/auditoria.php';
require __DIR__.'/web/integraciones.php';
