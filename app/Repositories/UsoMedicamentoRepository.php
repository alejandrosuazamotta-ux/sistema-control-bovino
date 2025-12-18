<?php

namespace App\Repositories;

use App\Models\UsoMedicamento;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UsoMedicamentoRepository
{
    /**
     * Obtener todos los usos con relaciones
     */
    public function allWithRelations(): Collection
    {
        return UsoMedicamento::with(['medicamento', 'vaca', 'personal', 'retiro'])->get();
    }

    /**
     * Obtener usos paginados con filtros
     */
    public function paginateWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = UsoMedicamento::with(['medicamento', 'vaca', 'personal', 'retiro']);

        // Búsqueda por código de vaca
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('vaca', function($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por vaca
        if (isset($filters['id_vaca']) && !empty($filters['id_vaca'])) {
            $query->where('id_vaca', $filters['id_vaca']);
        }

        // Filtro por medicamento
        if (isset($filters['id_medicamento']) && !empty($filters['id_medicamento'])) {
            $query->where('id_medicamento', $filters['id_medicamento']);
        }

        // Filtro por fecha inicio
        if (isset($filters['fecha_inicio']) && !empty($filters['fecha_inicio'])) {
            $query->where('fecha_aplicacion', '>=', $filters['fecha_inicio']);
        }

        // Filtro por fecha fin
        if (isset($filters['fecha_fin']) && !empty($filters['fecha_fin'])) {
            $query->where('fecha_aplicacion', '<=', $filters['fecha_fin']);
        }

        // Filtro por user_id (para pasantes)
        if (isset($filters['user_id']) && !empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query->orderBy('fecha_aplicacion', 'desc')->paginate($perPage);
    }

    /**
     * Obtener un uso por ID
     */
    public function findById(string $id): ?UsoMedicamento
    {
        return UsoMedicamento::with(['medicamento', 'vaca', 'personal', 'retiro'])->find($id);
    }

    /**
     * Obtener un uso por ID con todas sus relaciones
     */
    public function findWithRelations(string $id): ?UsoMedicamento
    {
        return UsoMedicamento::with(['medicamento', 'vaca', 'personal', 'retiro'])->find($id);
    }

    /**
     * Crear un nuevo uso
     */
    public function create(array $data): UsoMedicamento
    {
        return UsoMedicamento::create($data);
    }

    /**
     * Actualizar un uso
     */
    public function update(UsoMedicamento $uso, array $data): bool
    {
        return $uso->update($data);
    }

    /**
     * Eliminar un uso
     */
    public function delete(UsoMedicamento $uso): bool
    {
        return $uso->delete();
    }

    /**
     * Obtener usos de una vaca
     */
    public function getPorVaca(int $vacaId): Collection
    {
        return UsoMedicamento::with(['medicamento', 'retiro'])
            ->where('id_vaca', $vacaId)
            ->orderBy('fecha_aplicacion', 'desc')
            ->get();
    }

    /**
     * Obtener usos recientes
     */
    public function getRecientes(int $limit = 10): Collection
    {
        return UsoMedicamento::with(['medicamento', 'vaca'])
            ->orderBy('fecha_aplicacion', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener usos filtrados (sin paginación)
     */
    public function getFiltered(array $filters = []): Collection
    {
        $query = UsoMedicamento::with(['medicamento', 'vaca', 'personal', 'retiro']);

        // Búsqueda por código de vaca
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('vaca', function($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por vaca
        if (isset($filters['id_vaca']) && !empty($filters['id_vaca'])) {
            $query->where('id_vaca', $filters['id_vaca']);
        }

        // Filtro por medicamento
        if (isset($filters['id_medicamento']) && !empty($filters['id_medicamento'])) {
            $query->where('id_medicamento', $filters['id_medicamento']);
        }

        // Filtro por fecha inicio
        if (isset($filters['fecha_inicio']) && !empty($filters['fecha_inicio'])) {
            $query->where('fecha_aplicacion', '>=', $filters['fecha_inicio']);
        }

        // Filtro por fecha fin
        if (isset($filters['fecha_fin']) && !empty($filters['fecha_fin'])) {
            $query->where('fecha_aplicacion', '<=', $filters['fecha_fin']);
        }

        return $query->orderBy('fecha_aplicacion', 'desc')->get();
    }

    /**
     * Obtener datos para gráfica de uso por medicamento
     */
    public function getDatosGraficaUsoPorMedicamento(int $limit = 10): array
    {
        $datos = UsoMedicamento::with('medicamento')
            ->selectRaw('id_medicamento, COUNT(*) as total')
            ->groupBy('id_medicamento')
            ->orderBy('total', 'desc')
            ->limit($limit)
            ->get();

        $labels = [];
        $data = [];

        foreach ($datos as $item) {
            $labels[] = $item->medicamento->nombre ?? 'N/A';
            $data[] = $item->total;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Obtener datos para gráfica de aplicaciones por mes
     */
    public function getDatosGraficaAplicacionesPorMes(int $meses = 12): array
    {
        $fechaInicio = \Carbon\Carbon::now()->subMonths($meses)->startOfMonth();
        
        $datos = UsoMedicamento::where('fecha_aplicacion', '>=', $fechaInicio)
            ->selectRaw('DATE_FORMAT(fecha_aplicacion, "%Y-%m") as mes, COUNT(*) as total')
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        return [
            'labels' => $datos->pluck('mes')->toArray(),
            'data' => $datos->pluck('total')->toArray(),
        ];
    }

    /**
     * Obtener datos para gráfica de top vacas con más aplicaciones
     */
    public function getDatosGraficaTopVacas(int $limit = 10): array
    {
        $datos = UsoMedicamento::with('vaca')
            ->selectRaw('id_vaca, COUNT(*) as total')
            ->groupBy('id_vaca')
            ->orderBy('total', 'desc')
            ->limit($limit)
            ->get();

        $labels = [];
        $data = [];

        foreach ($datos as $item) {
            $labels[] = $item->vaca->codigo ?? 'N/A';
            $data[] = $item->total;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}

