<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    /** @phpstan-ignore-next-line */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Generar ausencias al cierre del día (23:55 hora servidor)
Schedule::command('altermec:ausencias-diarias')
    ->dailyAt('23:55')
    ->timezone('America/Lima')
    ->withoutOverlapping();

// Backup diario de BD a las 02:00
Schedule::command('altermec:backup-db')
    ->dailyAt('02:00')
    ->timezone('America/Lima')
    ->withoutOverlapping();
