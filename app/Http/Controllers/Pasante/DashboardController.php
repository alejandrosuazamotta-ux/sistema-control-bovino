<?php

namespace App\Http\Controllers\Pasante;

use App\Http\Controllers\Controller;
use App\Services\ActividadPasanteService;
use App\Services\TareaPasanteService;
use App\Services\ApoyoOrdeñoPasanteService;
use App\Services\ApoyoReproductivoPasanteService;
use App\Services\RotacionPotrerosPasanteService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected ActividadPasanteService $actividadService;
    protected TareaPasanteService $tareaService;
    protected ApoyoOrdeñoPasanteService $apoyoOrdeñoService;
    protected ApoyoReproductivoPasanteService $apoyoReproductivoService;
    protected RotacionPotrerosPasanteService $rotacionService;

    public function __construct(
        ActividadPasanteService $actividadService,
        TareaPasanteService $tareaService,
        ApoyoOrdeñoPasanteService $apoyoOrdeñoService,
        ApoyoReproductivoPasanteService $apoyoReproductivoService,
        RotacionPotrerosPasanteService $rotacionService
    ) {
        $this->actividadService = $actividadService;
        $this->tareaService = $tareaService;
        $this->apoyoOrdeñoService = $apoyoOrdeñoService;
        $this->apoyoReproductivoService = $apoyoReproductivoService;
        $this->rotacionService = $rotacionService;
    }

    public function index()
    {
        $userId = auth()->id();

        // Estadísticas generales
        $estadisticasActividades = $this->actividadService->getEstadisticas($userId);
        $estadisticasTareas = $this->tareaService->getEstadisticas($userId);
        $estadisticasOrdeño = $this->apoyoOrdeñoService->getEstadisticas($userId);
        $estadisticasReproductivo = $this->apoyoReproductivoService->getEstadisticas($userId);
        $estadisticasRotacion = $this->rotacionService->getEstadisticas($userId);

        // Datos para gráficas
        $datosGraficas = [
            'actividades_por_tipo' => $estadisticasActividades['por_tipo'] ?? [],
            'tareas_por_prioridad' => $estadisticasTareas['por_prioridad'] ?? [],
            'ordeno_por_turno' => $estadisticasOrdeño['por_turno'] ?? [],
            'reproductivo_por_tipo' => $estadisticasReproductivo['por_tipo'] ?? [],
        ];

        // Actividades recientes
        $actividadesRecientes = $this->actividadService->getPaginated(['user_id' => $userId], 5);
        $tareasPendientes = $this->tareaService->getPaginated(['user_id' => $userId, 'estado' => 'Pendiente'], 5);
        $tareasVencidas = $this->tareaService->getPaginated(['user_id' => $userId], 5)->filter(function($tarea) {
            return $tarea->esta_vencida ?? false;
        });

        return view('pasante.dashboard', compact(
            'estadisticasActividades',
            'estadisticasTareas',
            'estadisticasOrdeño',
            'estadisticasReproductivo',
            'estadisticasRotacion',
            'datosGraficas',
            'actividadesRecientes',
            'tareasPendientes',
            'tareasVencidas'
        ));
    }
}
