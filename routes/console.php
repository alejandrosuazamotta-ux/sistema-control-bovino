<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Programar generación de alertas diarias a las 6:00 AM
Schedule::job(new \App\Jobs\GenerarAlertasDiariasJob)
    ->dailyAt('06:00')
    ->name('generar-alertas-diarias')
    ->withoutOverlapping();
