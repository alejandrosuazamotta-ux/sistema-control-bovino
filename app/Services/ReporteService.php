<?php

namespace App\Services;

use App\Repositories\ReporteRepository;
use Carbon\Carbon;

class ReporteService
{
    protected ReporteRepository $repository;

    public function __construct(ReporteRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Obtener datos para reporte de producción
     *
     * @param string|null $fechaInicio
     * @param string|null $fechaFin
     * @return array
     */
    public function getReporteProduccion(?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $fechaInicio = $fechaInicio ?? now()->startOfMonth()->format('Y-m-d');
        $fechaFin = $fechaFin ?? now()->format('Y-m-d');

        $datos = $this->repository->getDatosReporteProduccion($fechaInicio, $fechaFin);

        // Preparar datos para gráficas
        $datos['graficas'] = [
            'produccion_diaria' => [
                'labels' => $datos['produccion_diaria']->pluck('dia')->map(fn($d) => Carbon::parse($d)->format('d/m'))->toArray(),
                'data' => $datos['produccion_diaria']->pluck('total_leche')->toArray(),
            ],
            'produccion_mensual' => [
                'labels' => $datos['produccion_mensual']->map(fn($m) => Carbon::create($m->año, $m->mes, 1)->format('M/Y'))->toArray(),
                'data' => $datos['produccion_mensual']->pluck('total_leche')->toArray(),
            ],
            'produccion_por_turno' => [
                'labels' => $datos['produccion_por_turno']->pluck('turno')->toArray(),
                'data' => $datos['produccion_por_turno']->pluck('total_leche')->toArray(),
            ],
            'produccion_por_destino' => [
                'labels' => $datos['produccion_por_destino']->pluck('destino')->toArray(),
                'data' => $datos['produccion_por_destino']->pluck('total_leche')->toArray(),
            ],
        ];

        return $datos;
    }

    /**
     * Obtener datos para reporte reproductivo
     *
     * @param string|null $fechaInicio
     * @param string|null $fechaFin
     * @return array
     */
    public function getReporteReproductivo(?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $fechaInicio = $fechaInicio ?? now()->startOfMonth()->format('Y-m-d');
        $fechaFin = $fechaFin ?? now()->format('Y-m-d');

        $datos = $this->repository->getDatosReporteReproductivo($fechaInicio, $fechaFin);

        // Preparar datos para gráficas
        $datos['graficas'] = [
            'eventos_por_tipo' => [
                'labels' => $datos['eventos_por_tipo']->pluck('tipo_evento')->toArray(),
                'data' => $datos['eventos_por_tipo']->pluck('total')->toArray(),
            ],
            'palpaciones_por_resultado' => [
                'labels' => $datos['palpaciones_por_resultado']->pluck('resultado_palpacion')->toArray(),
                'data' => $datos['palpaciones_por_resultado']->pluck('total')->toArray(),
            ],
            'partos_por_mes' => [
                'labels' => $datos['partos_por_mes']->map(fn($p) => Carbon::create($p->año, $p->mes, 1)->format('M/Y'))->toArray(),
                'data' => $datos['partos_por_mes']->pluck('total')->toArray(),
            ],
        ];

        return $datos;
    }

    /**
     * Obtener datos para reporte sanitario
     *
     * @param string|null $fechaInicio
     * @param string|null $fechaFin
     * @return array
     */
    public function getReporteSanitario(?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $fechaInicio = $fechaInicio ?? now()->startOfMonth()->format('Y-m-d');
        $fechaFin = $fechaFin ?? now()->format('Y-m-d');

        $datos = $this->repository->getDatosReporteSanitario($fechaInicio, $fechaFin);

        // Preparar datos para gráficas
        $datos['graficas'] = [
            'registros_por_tipo' => [
                'labels' => $datos['registros_por_tipo']->pluck('tipo_registro')->toArray(),
                'data' => $datos['registros_por_tipo']->pluck('total')->toArray(),
            ],
            'pruebas_por_resultado' => [
                'labels' => $datos['pruebas_por_resultado']->pluck('resultado')->toArray(),
                'data' => $datos['pruebas_por_resultado']->pluck('total')->toArray(),
            ],
            'mastitis_por_severidad' => [
                'labels' => $datos['mastitis_por_severidad']->pluck('severidad')->toArray(),
                'data' => $datos['mastitis_por_severidad']->pluck('total')->toArray(),
            ],
            'registros_por_mes' => [
                'labels' => $datos['registros_por_mes']->map(fn($r) => Carbon::create($r->año, $r->mes, 1)->format('M/Y'))->toArray(),
                'data' => $datos['registros_por_mes']->pluck('total')->toArray(),
            ],
        ];

        return $datos;
    }

    /**
     * Obtener datos para reporte de mortalidad
     *
     * @param string|null $fechaInicio
     * @param string|null $fechaFin
     * @return array
     */
    public function getReporteMortalidad(?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $fechaInicio = $fechaInicio ?? now()->startOfMonth()->format('Y-m-d');
        $fechaFin = $fechaFin ?? now()->format('Y-m-d');

        $datos = $this->repository->getDatosReporteMortalidad($fechaInicio, $fechaFin);

        // Preparar datos para gráficas
        $datos['graficas'] = [
            'muertes_por_tipo' => [
                'labels' => $datos['muertes_por_tipo']->map(fn($m) => $m->animal_type === 'App\Models\Vaca' ? 'Vaca' : 'Cría')->toArray(),
                'data' => $datos['muertes_por_tipo']->pluck('total')->toArray(),
            ],
            'muertes_por_clasificacion' => [
                'labels' => $datos['muertes_por_clasificacion']->pluck('clasificacion')->toArray(),
                'data' => $datos['muertes_por_clasificacion']->pluck('total')->toArray(),
            ],
            'muertes_por_causa' => [
                'labels' => $datos['muertes_por_causa']->pluck('causa')->toArray(),
                'data' => $datos['muertes_por_causa']->pluck('total')->toArray(),
            ],
            'muertes_por_mes' => [
                'labels' => $datos['muertes_por_mes']->map(fn($m) => Carbon::create($m->año, $m->mes, 1)->format('M/Y'))->toArray(),
                'data' => $datos['muertes_por_mes']->pluck('total')->toArray(),
            ],
        ];

        return $datos;
    }

    /**
     * Obtener datos para reporte de medicamentos
     *
     * @param string|null $fechaInicio
     * @param string|null $fechaFin
     * @return array
     */
    public function getReporteMedicamentos(?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $fechaInicio = $fechaInicio ?? now()->startOfMonth()->format('Y-m-d');
        $fechaFin = $fechaFin ?? now()->format('Y-m-d');

        $datos = $this->repository->getDatosReporteMedicamentos($fechaInicio, $fechaFin);

        // Preparar datos para gráficas
        $datos['graficas'] = [
            'usos_por_medicamento' => [
                'labels' => $datos['usos_por_medicamento']->map(fn($u) => $u->medicamento->nombre ?? 'N/A')->toArray(),
                'data' => $datos['usos_por_medicamento']->pluck('total_usos')->toArray(),
            ],
            'usos_por_tipo' => [
                'labels' => $datos['usos_por_tipo']->pluck('tipo')->toArray(),
                'data' => $datos['usos_por_tipo']->pluck('total_usos')->toArray(),
            ],
            'usos_por_mes' => [
                'labels' => $datos['usos_por_mes']->map(fn($u) => Carbon::create($u->año, $u->mes, 1)->format('M/Y'))->toArray(),
                'data' => $datos['usos_por_mes']->pluck('total')->toArray(),
            ],
        ];

        return $datos;
    }
}

