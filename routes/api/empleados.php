<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1/empleados')->name('api.empleados.')->group(function (): void {
    // Endpoints API reservados para empleados y sincronizaciones.
});
