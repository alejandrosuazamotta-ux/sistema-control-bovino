<?php

namespace App\Http\Controllers\Pasante;

use App\Http\Controllers\Controller;
use App\Services\ReporteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReporteController extends Controller
{
    protected ReporteService $reporteService;

    public function __construct(ReporteService $reporteService)
    {
        $this->reporteService = $reporteService;
    }

    /**
     * Mostrar índice de reportes
     * 
     * Autorización: Admin, Supervisor y Pasante pueden ver reportes
     */
    public function index()
    {
        $policy = new \App\Policies\ReportePolicy();
        if (!$policy->viewAny(auth()->user())) {
            abort(403);
        }
        return view('pasante.reportes.index');
    }

    /**
     * Reporte de producción (solo lectura)
     * 
     * Autorización: Admin, Supervisor y Pasante pueden ver reportes
     */
    public function produccion(Request $request)
    {
        $policy = new \App\Policies\ReportePolicy();
        if (!$policy->viewProduccion(auth()->user())) {
            abort(403);
        }

        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->reporteService->getReporteProduccion($fechaInicio, $fechaFin);

        return view('pasante.reportes.produccion', compact('datos', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Reporte reproductivo (solo lectura)
     * 
     * Autorización: Admin, Supervisor y Pasante pueden ver reportes
     */
    public function reproductivo(Request $request)
    {
        $policy = new \App\Policies\ReportePolicy();
        if (!$policy->viewReproductivo(auth()->user())) {
            abort(403);
        }

        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->reporteService->getReporteReproductivo($fechaInicio, $fechaFin);

        return view('pasante.reportes.reproductivo', compact('datos', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Reporte sanitario (solo lectura)
     * 
     * Autorización: Admin, Supervisor y Pasante pueden ver reportes
     */
    public function sanitario(Request $request)
    {
        $policy = new \App\Policies\ReportePolicy();
        if (!$policy->viewSanitario(auth()->user())) {
            abort(403);
        }

        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->reporteService->getReporteSanitario($fechaInicio, $fechaFin);

        return view('pasante.reportes.sanitario', compact('datos', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Reporte de mortalidad (solo lectura)
     * 
     * Autorización: Admin, Supervisor y Pasante pueden ver reportes
     */
    public function mortalidad(Request $request)
    {
        $policy = new \App\Policies\ReportePolicy();
        if (!$policy->viewMortalidad(auth()->user())) {
            abort(403);
        }

        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->reporteService->getReporteMortalidad($fechaInicio, $fechaFin);

        return view('pasante.reportes.mortalidad', compact('datos', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Reporte de medicamentos (solo lectura)
     * 
     * Autorización: Admin, Supervisor y Pasante pueden ver reportes
     */
    public function medicamentos(Request $request)
    {
        $policy = new \App\Policies\ReportePolicy();
        if (!$policy->viewMedicamentos(auth()->user())) {
            abort(403);
        }

        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $datos = $this->reporteService->getReporteMedicamentos($fechaInicio, $fechaFin);

        return view('pasante.reportes.medicamentos', compact('datos', 'fechaInicio', 'fechaFin'));
    }
}

