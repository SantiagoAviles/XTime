<?php

use App\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'permission:access_dashboard'])->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});
