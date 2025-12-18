<?php

namespace App\Repositories;

use App\Models\ActividadPasante;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ActividadPasanteRepository
{
    public function paginateWithFilters(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = ActividadPasante::with(['pasante', 'aprobador']);

        if (isset($filters['user_id']) && $filters['user_id']) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['tipo_actividad']) && $filters['tipo_actividad']) {
            $query->where('tipo_actividad', $filters['tipo_actividad']);
        }

        if (isset($filters['estado']) && $filters['estado']) {
            $query->where('estado', $filters['estado']);
        }

        if (isset($filters['aprobada']) && $filters['aprobada'] !== '') {
            $query->where('aprobada', $filters['aprobada']);
        }

        if (isset($filters['fecha_inicio']) && $filters['fecha_inicio']) {
            $query->where('fecha_actividad', '>=', $filters['fecha_inicio']);
        }

        if (isset($filters['fecha_fin']) && $filters['fecha_fin']) {
            $query->where('fecha_actividad', '<=', $filters['fecha_fin']);
        }

        return $query->orderBy('fecha_actividad', 'desc')->paginate($perPage);
    }

    public function findById(string $id): ?ActividadPasante
    {
        return ActividadPasante::with(['pasante', 'aprobador'])->find($id);
    }

    public function create(array $data): ActividadPasante
    {
        return ActividadPasante::create($data);
    }

    public function update(ActividadPasante $actividad, array $data): bool
    {
        return $actividad->update($data);
    }

    public function delete(ActividadPasante $actividad): bool
    {
        return $actividad->delete();
    }

    public function getByPasante(int $userId, array $filters = []): Collection
    {
        $query = ActividadPasante::where('user_id', $userId);

        if (isset($filters['estado']) && $filters['estado']) {
            $query->where('estado', $filters['estado']);
        }

        return $query->orderBy('fecha_actividad', 'desc')->get();
    }

    public function getPendientesAprobacion(): Collection
    {
        return ActividadPasante::with(['pasante'])
            ->where('aprobada', false)
            ->where('estado', 'Completada')
            ->orderBy('fecha_actividad', 'desc')
            ->get();
    }

    public function getEstadisticas(int $userId = null): array
    {
        $query = ActividadPasante::query();
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        return [
            'total' => $query->count(),
            'completadas' => (clone $query)->where('estado', 'Completada')->count(),
            'pendientes' => (clone $query)->where('estado', 'Pendiente')->count(),
            'aprobadas' => (clone $query)->where('aprobada', true)->count(),
            'por_tipo' => (clone $query)->selectRaw('tipo_actividad, COUNT(*) as total')
                ->groupBy('tipo_actividad')
                ->pluck('total', 'tipo_actividad')
                ->toArray()
        ];
    }
}

