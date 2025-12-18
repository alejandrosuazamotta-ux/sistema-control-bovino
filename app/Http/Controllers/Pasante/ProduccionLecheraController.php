<?php

namespace App\Http\Controllers\Pasante;

use App\Http\Controllers\Controller;
use App\Services\ProduccionLecheraService;
use App\Repositories\ProduccionLecheraRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\ProduccionLechera;

class ProduccionLecheraController extends Controller
{
    protected ProduccionLecheraService $produccionLecheraService;
    protected ProduccionLecheraRepository $produccionRepository;

    public function __construct(
        ProduccionLecheraService $produccionLecheraService,
        ProduccionLecheraRepository $produccionRepository
    ) {
        $this->produccionLecheraService = $produccionLecheraService;
        $this->produccionRepository = $produccionRepository;
    }

    /**
     * Dashboard de Producción Lechera para Pasante
     * Muestra gráficas y estadísticas de producción
     */
    public function dashboard(Request $request)
    {
        // Autorización: Pasante puede ver
        Gate::authorize('viewAny', ProduccionLechera::class);

        // Obtener estadísticas generales
        $estadisticas = $this->produccionLecheraService->getEstadisticas();

        // Datos para gráficas (últimos 30 días)
        $produccionDiaria = $this->produccionRepository->getProduccionDiaria(30);
        $produccionPorTurno = $this->produccionRepository->getProduccionPorTurno(30);
        $produccionPorDestino = $this->produccionRepository->getProduccionPorDestino(30);
        $produccionMensual = $this->produccionRepository->getProduccionMensualGrafica(12);
        
        // Top 10 vacas más productivas (últimos 30 días)
        $topVacas = $this->produccionRepository->getTopVacasProductivas(10, 30);

        // Producción del mes actual
        $produccionHoy = $this->produccionRepository->getTotalProduccionMesActual();

        return view('pasante.produccion_lechera.dashboard', compact(
            'estadisticas',
            'produccionDiaria',
            'produccionPorTurno',
            'produccionPorDestino',
            'produccionMensual',
            'topVacas',
            'produccionHoy'
        ));
    }
}

