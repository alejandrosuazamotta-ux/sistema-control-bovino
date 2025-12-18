<?php

namespace App\Repositories;

use App\Models\ApoyoReproductivoPasante;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ApoyoReproductivoPasanteRepository
{
    public function paginateWithFilters(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = ApoyoReproductivoPasante::with(['pasante', 'vaca', 'aprobador']);

        if (isset($filters['user_id']) && $filters['user_id']) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['id_vaca']) && $filters['id_vaca']) {
            $query->where('id_vaca', $filters['id_vaca']);
        }

        if (isset($filters['tipo_actividad']) && $filters['tipo_actividad']) {
            $query->where('tipo_actividad', $filters['tipo_actividad']);
        }

        if (isset($filters['fecha_inicio']) && $filters['fecha_inicio']) {
            $query->where('fecha_actividad', '>=', $filters['fecha_inicio']);
        }

        if (isset($filters['fecha_fin']) && $filters['fecha_fin']) {
            $query->where('fecha_actividad', '<=', $filters['fecha_fin']);
        }

        if (isset($filters['aprobada']) && $filters['aprobada'] !== '') {
            $query->where('aprobada', $filters['aprobada']);
        }

        return $query->orderBy('fecha_actividad', 'desc')->paginate($perPage);
    }

    public function findById(string $id): ?ApoyoReproductivoPasante
    {
        return ApoyoReproductivoPasante::with(['pasante', 'vaca', 'aprobador'])->find($id);
    }

    public function create(array $data): ApoyoReproductivoPasante
    {
        return ApoyoReproductivoPasante::create($data);
    }

    public function update(ApoyoReproductivoPasante $apoyo, array $data): bool
    {
        return $apoyo->update($data);
    }

    public function delete(ApoyoReproductivoPasante $apoyo): bool
    {
        return $apoyo->delete();
    }

    public function getByPasante(int $userId, array $filters = []): Collection
    {
        $query = ApoyoReproductivoPasante::where('user_id', $userId);

        if (isset($filters['tipo_actividad']) && $filters['tipo_actividad']) {
            $query->where('tipo_actividad', $filters['tipo_actividad']);
        }

        return $query->orderBy('fecha_actividad', 'desc')->get();
    }

    public function getPendientesAprobacion(): Collection
    {
        return ApoyoReproductivoPasante::with(['pasante', 'vaca'])
            ->where('aprobada', false)
            ->orderBy('fecha_actividad', 'desc')
            ->get();
    }

    public function getEstadisticas(int $userId = null): array
    {
        $query = ApoyoReproductivoPasante::query();
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        return [
            'total' => $query->count(),
            'aprobadas' => (clone $query)->where('aprobada', true)->count(),
            'pendientes' => (clone $query)->where('aprobada', false)->count(),
            'por_tipo' => (clone $query)->selectRaw('tipo_actividad, COUNT(*) as total')
                ->groupBy('tipo_actividad')
                ->pluck('total', 'tipo_actividad')
                ->toArray()
        ];
    }
}

