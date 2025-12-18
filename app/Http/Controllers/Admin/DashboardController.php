<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Personal;
use App\Models\Vaca;
use App\Models\Potrero;
use App\Models\ProduccionLechera;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        // Estadísticas básicas
        $totalPersonal = Personal::count();
        $totalVacas = Vaca::count();
        $totalPotreros = Potrero::count();
        
        // Producción del día
        $produccionHoy = ProduccionLechera::whereDate('fecha', today())->sum('cantidad_leche');
        
        // Personal por rol
        $personalPorRol = Personal::selectRaw('rol, count(*) as total')
            ->groupBy('rol')
            ->get();
        
        // Últimas vacas registradas (optimizado con eager loading)
        $ultimasVacas = Vaca::with(['potrero'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Último personal registrado
        $ultimoPersonal = Personal::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Datos para gráficas
        $produccionDiaria = $this->dashboardService->getProduccionDiaria(30);
        $produccionMensual = $this->dashboardService->getProduccionMensual(12);
        $vacasPorEstado = $this->dashboardService->getVacasPorEstado();
        $produccionPorPotrero = $this->dashboardService->getProduccionPorPotrero();
        $rankingVacas = $this->dashboardService->getRankingVacasProductivas(10);
        $estadoReproductivo = $this->dashboardService->getEstadoReproductivo();
        $estadisticas = $this->dashboardService->getEstadisticasGenerales();
        $alertas = $this->dashboardService->getAlertas();
        
        return view('admin.dashboard', compact(
            'totalPersonal',
            'totalVacas', 
            'totalPotreros',
            'produccionHoy',
            'personalPorRol',
            'ultimasVacas',
            'ultimoPersonal',
            'produccionDiaria',
            'produccionMensual',
            'vacasPorEstado',
            'produccionPorPotrero',
            'rankingVacas',
            'estadoReproductivo',
            'estadisticas',
            'alertas'
        ));
    }
} 