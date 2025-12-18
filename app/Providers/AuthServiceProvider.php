<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Vaca;
use App\Models\Cria;
use App\Models\RegistroReproductivo;
use App\Models\Salud;
use App\Models\Mortalidad;
use App\Models\Medicamento;
use App\Models\UsoMedicamento;
use App\Models\Retiro;
use App\Models\ProduccionLechera;
use App\Models\PruebaSanitaria;
use App\Models\InventarioBodega;
use App\Models\Notificacion;
use App\Models\ActividadPasante;
use App\Models\ApoyoOrdeñoPasante;
use App\Models\ApoyoReproductivoPasante;
use App\Models\RotacionPotrerosPasante;
use App\Models\TareaPasante;
use App\Policies\VacaPolicy;
use App\Policies\CriaPolicy;
use App\Policies\RegistroReproductivoPolicy;
use App\Policies\SaludPolicy;
use App\Policies\MortalidadPolicy;
use App\Policies\MedicamentoPolicy;
use App\Policies\UsoMedicamentoPolicy;
use App\Policies\ProduccionLecheraPolicy;
use App\Policies\PruebaSanitariaPolicy;
use App\Policies\InventarioBodegaPolicy;
use App\Policies\NotificacionPolicy;
use App\Policies\ActividadPasantePolicy;
use App\Policies\ApoyoOrdeñoPasantePolicy;
use App\Policies\ApoyoReproductivoPasantePolicy;
use App\Policies\RotacionPotrerosPasantePolicy;
use App\Policies\TareaPasantePolicy;

/**
 * AuthServiceProvider
 * 
 * Registra explícitamente todas las Policies del sistema para garantizar
 * que las autorizaciones funcionen correctamente en Laravel 12.
 * 
 * Compatible con Laravel 12 y el sistema de autorización de Spatie Permission.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Modelos principales
        Vaca::class => VacaPolicy::class,
        Cria::class => CriaPolicy::class,
        RegistroReproductivo::class => RegistroReproductivoPolicy::class,
        Salud::class => SaludPolicy::class,
        Mortalidad::class => MortalidadPolicy::class,
        Medicamento::class => MedicamentoPolicy::class,
        UsoMedicamento::class => UsoMedicamentoPolicy::class,
        // Retiro no tiene Policy - se maneja por middleware de rutas
        ProduccionLechera::class => ProduccionLecheraPolicy::class,
        PruebaSanitaria::class => PruebaSanitariaPolicy::class,
        InventarioBodega::class => InventarioBodegaPolicy::class,
        Notificacion::class => NotificacionPolicy::class,
        
        // Modelos de Pasante
        ActividadPasante::class => ActividadPasantePolicy::class,
        ApoyoOrdeñoPasante::class => ApoyoOrdeñoPasantePolicy::class,
        ApoyoReproductivoPasante::class => ApoyoReproductivoPasantePolicy::class,
        RotacionPotrerosPasante::class => RotacionPotrerosPasantePolicy::class,
        TareaPasante::class => TareaPasantePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Registrar Policies explícitamente
        $this->registerPolicies();

        // Registrar Policy para Reportes (no tiene modelo asociado)
        // Se maneja manualmente en ReporteController
    }
}

