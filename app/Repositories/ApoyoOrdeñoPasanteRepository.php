<?php

namespace App\Repositories;

use App\Models\ApoyoOrdeñoPasante;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ApoyoOrdeñoPasanteRepository
{
    public function paginateWithFilters(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = ApoyoOrdeñoPasante::with(['pasante', 'vaca', 'aprobador']);

        if (isset($filters['user_id']) && $filters['user_id']) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['id_vaca']) && $filters['id_vaca']) {
            $query->where('id_vaca', $filters['id_vaca']);
        }

        if (isset($filters['turno']) && $filters['turno']) {
            $query->where('turno', $filters['turno']);
        }

        if (isset($filters['fecha_inicio']) && $filters['fecha_inicio']) {
            $query->where('fecha_ordeno', '>=', $filters['fecha_inicio']);
        }

        if (isset($filters['fecha_fin']) && $filters['fecha_fin']) {
            $query->where('fecha_ordeno', '<=', $filters['fecha_fin']);
        }

        if (isset($filters['aprobada']) && $filters['aprobada'] !== '') {
            $query->where('aprobada', $filters['aprobada']);
        }

        return $query->orderBy('fecha_ordeno', 'desc')->paginate($perPage);
    }

    public function findById(string $id): ?ApoyoOrdeñoPasante
    {
        return ApoyoOrdeñoPasante::with(['pasante', 'vaca', 'aprobador'])->find($id);
    }

    public function create(array $data): ApoyoOrdeñoPasante
    {
        return ApoyoOrdeñoPasante::create($data);
    }

    public function update(ApoyoOrdeñoPasante $apoyo, array $data): bool
    {
        return $apoyo->update($data);
    }

    public function delete(ApoyoOrdeñoPasante $apoyo): bool
    {
        return $apoyo->delete();
    }

    public function getByPasante(int $userId, array $filters = []): Collection
    {
        $query = ApoyoOrdeñoPasante::where('user_id', $userId);

        if (isset($filters['fecha_inicio']) && $filters['fecha_inicio']) {
            $query->where('fecha_ordeno', '>=', $filters['fecha_inicio']);
        }

        if (isset($filters['fecha_fin']) && $filters['fecha_fin']) {
            $query->where('fecha_ordeno', '<=', $filters['fecha_fin']);
        }

        return $query->orderBy('fecha_ordeno', 'desc')->get();
    }

    public function getPendientesAprobacion(): Collection
    {
        return ApoyoOrdeñoPasante::with(['pasante', 'vaca'])
            ->where('aprobada', false)
            ->orderBy('fecha_ordeno', 'desc')
            ->get();
    }

    public function getEstadisticas(int $userId = null): array
    {
        $query = ApoyoOrdeñoPasante::query();
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        return [
            'total' => $query->count(),
            'aprobadas' => (clone $query)->where('aprobada', true)->count(),
            'pendientes' => (clone $query)->where('aprobada', false)->count(),
            'total_leche' => (clone $query)->sum('cantidad_leche'),
            'por_turno' => (clone $query)->selectRaw('turno, COUNT(*) as total, SUM(cantidad_leche) as total_leche')
                ->groupBy('turno')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->turno => ['total' => $item->total, 'total_leche' => $item->total_leche]];
                })
                ->toArray()
        ];
    }
}

