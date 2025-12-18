<?php

namespace App\Repositories;

use App\Models\AsignacionPotrero;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AsignacionPotreroRepository
{
    /**
     * Obtener todas las asignaciones con relaciones
     */
    public function allWithRelations(): Collection
    {
        return AsignacionPotrero::with(['vaca', 'potrero'])->get();
    }

    /**
     * Obtener asignaciones paginadas con filtros
     */
    public function paginateWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = AsignacionPotrero::with(['vaca', 'potrero']);

        // Búsqueda por código de vaca
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('vaca', function($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por potrero
        if (isset($filters['id_potrero']) && !empty($filters['id_potrero'])) {
            $query->where('id_potrero', $filters['id_potrero']);
        }

        // Filtro por vaca
        if (isset($filters['id_vaca']) && !empty($filters['id_vaca'])) {
            $query->where('id_vaca', $filters['id_vaca']);
        }

        // Filtro por fecha inicio
        if (isset($filters['fecha_inicio']) && !empty($filters['fecha_inicio'])) {
            $query->where('fecha_asignacion', '>=', $filters['fecha_inicio']);
        }

        // Filtro por fecha fin
        if (isset($filters['fecha_fin']) && !empty($filters['fecha_fin'])) {
            $query->where('fecha_asignacion', '<=', $filters['fecha_fin']);
        }

        return $query->orderBy('fecha_asignacion', 'desc')->paginate($perPage);
    }

    /**
     * Obtener una asignación por ID
     */
    public function findById(string $id): ?AsignacionPotrero
    {
        return AsignacionPotrero::with(['vaca', 'potrero'])->find($id);
    }

    /**
     * Crear una nueva asignación
     */
    public function create(array $data): AsignacionPotrero
    {
        return AsignacionPotrero::create($data);
    }

    /**
     * Actualizar una asignación
     */
    public function update(AsignacionPotrero $asignacion, array $data): bool
    {
        return $asignacion->update($data);
    }

    /**
     * Eliminar una asignación
     */
    public function delete(AsignacionPotrero $asignacion): bool
    {
        return $asignacion->delete();
    }

    /**
     * Obtener datos para gráfica de asignaciones por potrero
     */
    public function getDatosGraficaAsignacionesPorPotrero(int $limit = 10): array
    {
        $datos = AsignacionPotrero::with('potrero')
            ->selectRaw('id_potrero, COUNT(*) as total')
            ->groupBy('id_potrero')
            ->orderBy('total', 'desc')
            ->limit($limit)
            ->get();

        $labels = [];
        $data = [];

        foreach ($datos as $item) {
            $labels[] = $item->potrero->nombre ?? 'N/A';
            $data[] = $item->total;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Obtener datos para gráfica de rotaciones por mes
     */
    public function getDatosGraficaRotacionesPorMes(int $meses = 12): array
    {
        $fechaInicio = \Carbon\Carbon::now()->subMonths($meses)->startOfMonth();
        
        $datos = AsignacionPotrero::where('fecha_asignacion', '>=', $fechaInicio)
            ->selectRaw('DATE_FORMAT(fecha_asignacion, "%Y-%m") as mes, COUNT(*) as total')
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        return [
            'labels' => $datos->pluck('mes')->toArray(),
            'data' => $datos->pluck('total')->toArray(),
        ];
    }

    /**
     * Obtener datos para gráfica de potreros más usados
     */
    public function getDatosGraficaPotrerosMasUsados(int $limit = 10): array
    {
        $datos = AsignacionPotrero::with('potrero')
            ->selectRaw('id_potrero, COUNT(DISTINCT id_vaca) as total_vacas')
            ->groupBy('id_potrero')
            ->orderBy('total_vacas', 'desc')
            ->limit($limit)
            ->get();

        $labels = [];
        $data = [];

        foreach ($datos as $item) {
            $labels[] = $item->potrero->nombre ?? 'N/A';
            $data[] = $item->total_vacas;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Obtener datos para gráfica de carga UGG por potrero
     */
    public function getDatosGraficaCargaUGG(int $limit = 10): array
    {
        $datos = AsignacionPotrero::with('potrero')
            ->selectRaw('id_potrero, AVG(carga_ugg) as carga_promedio')
            ->whereNotNull('carga_ugg')
            ->groupBy('id_potrero')
            ->orderBy('carga_promedio', 'desc')
            ->limit($limit)
            ->get();

        $labels = [];
        $data = [];

        foreach ($datos as $item) {
            $labels[] = $item->potrero->nombre ?? 'N/A';
            $data[] = round($item->carga_promedio, 2);
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Obtener datos para gráfica de días de descanso por potrero
     */
    public function getDatosGraficaDiasDescanso(int $limit = 10): array
    {
        $datos = AsignacionPotrero::with('potrero')
            ->selectRaw('id_potrero, AVG(dias_descanso) as dias_promedio')
            ->whereNotNull('dias_descanso')
            ->where('dias_descanso', '>', 0)
            ->groupBy('id_potrero')
            ->orderBy('dias_promedio', 'desc')
            ->limit($limit)
            ->get();

        $labels = [];
        $data = [];

        foreach ($datos as $item) {
            $labels[] = $item->potrero->nombre ?? 'N/A';
            $data[] = round($item->dias_promedio, 0);
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Obtener datos para gráfica de uso por potrero (días de ocupación)
     */
    public function getDatosGraficaUsoPorPotrero(int $limit = 10): array
    {
        $datos = AsignacionPotrero::with('potrero')
            ->selectRaw('id_potrero, SUM(dias_estancia) as total_dias_ocupacion')
            ->whereNotNull('dias_estancia')
            ->groupBy('id_potrero')
            ->orderBy('total_dias_ocupacion', 'desc')
            ->limit($limit)
            ->get();

        $labels = [];
        $data = [];

        foreach ($datos as $item) {
            $labels[] = $item->potrero->nombre ?? 'N/A';
            $data[] = $item->total_dias_ocupacion;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}

