<?php

namespace App\Repositories;

use App\Models\Alimentacion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AlimentacionRepository
{
    /**
     * Obtener todas las alimentaciones con relaciones
     */
    public function allWithRelations(): Collection
    {
        return Alimentacion::with(['vaca', 'personal'])->get();
    }

    /**
     * Obtener alimentaciones paginadas con filtros
     */
    public function paginateWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Alimentacion::with(['vaca', 'personal']);

        // Búsqueda por código de vaca
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('vaca', function($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por tipo de alimento
        if (isset($filters['tipo_alimento']) && !empty($filters['tipo_alimento'])) {
            $query->where('tipo_alimento', $filters['tipo_alimento']);
        }

        // Filtro por fecha inicio
        if (isset($filters['fecha_inicio']) && !empty($filters['fecha_inicio'])) {
            $query->where('fecha', '>=', $filters['fecha_inicio']);
        }

        // Filtro por fecha fin
        if (isset($filters['fecha_fin']) && !empty($filters['fecha_fin'])) {
            $query->where('fecha', '<=', $filters['fecha_fin']);
        }

        // Filtro por vaca específica
        if (isset($filters['id_vaca']) && !empty($filters['id_vaca'])) {
            $query->where('id_vaca', $filters['id_vaca']);
        }

        // Filtro por personal
        if (isset($filters['id_personal']) && !empty($filters['id_personal'])) {
            $query->where('id_personal', $filters['id_personal']);
        }

        return $query->orderBy('fecha', 'desc')->paginate($perPage);
    }

    /**
     * Obtener una alimentación por ID con relaciones
     */
    public function findWithRelations(string $id): ?Alimentacion
    {
        return Alimentacion::with(['vaca', 'personal'])->find($id);
    }

    /**
     * Obtener alimentación por ID
     */
    public function findById(string $id): ?Alimentacion
    {
        return Alimentacion::find($id);
    }

    /**
     * Crear una nueva alimentación
     */
    public function create(array $data): Alimentacion
    {
        return Alimentacion::create($data);
    }

    /**
     * Actualizar una alimentación
     */
    public function update(Alimentacion $alimentacion, array $data): bool
    {
        return $alimentacion->update($data);
    }

    /**
     * Eliminar una alimentación
     */
    public function delete(Alimentacion $alimentacion): bool
    {
        return $alimentacion->delete();
    }

    /**
     * Obtener estadísticas de alimentación
     */
    public function getEstadisticas(array $filters = []): array
    {
        $query = Alimentacion::query();

        if (isset($filters['fecha_inicio']) && !empty($filters['fecha_inicio'])) {
            $query->where('fecha', '>=', $filters['fecha_inicio']);
        }

        if (isset($filters['fecha_fin']) && !empty($filters['fecha_fin'])) {
            $query->where('fecha', '<=', $filters['fecha_fin']);
        }

        return [
            'total_registros' => $query->count(),
            'total_cantidad' => $query->sum('cantidad'),
            'promedio_cantidad' => $query->avg('cantidad') ?? 0,
            'por_tipo_alimento' => $query->selectRaw('tipo_alimento, SUM(cantidad) as total')
                ->groupBy('tipo_alimento')
                ->pluck('total', 'tipo_alimento')
                ->toArray(),
        ];
    }

    /**
     * Obtener datos para gráfica de alimentación por tipo
     */
    public function getDatosGraficaPorTipoAlimento(): array
    {
        $datos = Alimentacion::selectRaw('tipo_alimento, SUM(cantidad) as total')
            ->groupBy('tipo_alimento')
            ->get();

        return [
            'labels' => $datos->pluck('tipo_alimento')->toArray(),
            'data' => $datos->pluck('total')->map(fn($val) => round($val, 2))->toArray(),
        ];
    }

    /**
     * Obtener datos para gráfica de consumo por mes
     */
    public function getDatosGraficaConsumoPorMes(int $meses = 12): array
    {
        $fechaInicio = \Carbon\Carbon::now()->subMonths($meses)->startOfMonth();
        
        $datos = Alimentacion::where('fecha', '>=', $fechaInicio)
            ->selectRaw('DATE_FORMAT(fecha, "%Y-%m") as mes, SUM(cantidad) as total')
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        return [
            'labels' => $datos->pluck('mes')->toArray(),
            'data' => $datos->pluck('total')->map(fn($val) => round($val, 2))->toArray(),
        ];
    }

    /**
     * Obtener datos para gráfica de top vacas por consumo
     * 
     * Optimizado: Usa eager loading para evitar N+1 queries
     * Carga solo los campos necesarios de la relación vaca
     */
    public function getDatosGraficaTopVacas(int $limit = 10): array
    {
        // Obtener datos agregados con eager loading optimizado
        // Usamos select específico para cargar solo id_vaca y codigo de la relación
        $datos = Alimentacion::with(['vaca:id_vaca,codigo'])
            ->selectRaw('id_vaca, SUM(cantidad) as total')
            ->groupBy('id_vaca')
            ->orderBy('total', 'desc')
            ->limit($limit)
            ->get();

        $labels = [];
        $data = [];

        foreach ($datos as $item) {
            // La relación vaca ya está cargada con eager loading, no hay N+1
            $labels[] = $item->vaca->codigo ?? 'N/A';
            $data[] = round($item->total, 2);
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}

