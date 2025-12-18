<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\PruebaSanitariaResultadoPositivo;
use App\Listeners\BloquearOrdenoPorPruebaSanitaria;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar Event Listeners para Pruebas Sanitarias
        Event::listen(
            PruebaSanitariaResultadoPositivo::class,
            BloquearOrdenoPorPruebaSanitaria::class
        );
    }
}
