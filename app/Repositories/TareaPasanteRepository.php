<?php

namespace App\Repositories;

use App\Models\TareaPasante;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TareaPasanteRepository
{
    public function paginateWithFilters(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = TareaPasante::with(['pasante', 'asignador', 'aprobador']);

        if (isset($filters['user_id']) && $filters['user_id']) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['asignada_por']) && $filters['asignada_por']) {
            $query->where('asignada_por', $filters['asignada_por']);
        }

        if (isset($filters['prioridad']) && $filters['prioridad']) {
            $query->where('prioridad', $filters['prioridad']);
        }

        if (isset($filters['estado']) && $filters['estado']) {
            $query->where('estado', $filters['estado']);
        }

        if (isset($filters['aprobada']) && $filters['aprobada'] !== '') {
            $query->where('aprobada', $filters['aprobada']);
        }

        return $query->orderBy('fecha_asignacion', 'desc')->paginate($perPage);
    }

    public function findById(string $id): ?TareaPasante
    {
        return TareaPasante::with(['pasante', 'asignador', 'aprobador'])->find($id);
    }

    public function create(array $data): TareaPasante
    {
        return TareaPasante::create($data);
    }

    public function update(TareaPasante $tarea, array $data): bool
    {
        return $tarea->update($data);
    }

    public function delete(TareaPasante $tarea): bool
    {
        return $tarea->delete();
    }

    public function getByPasante(int $userId, array $filters = []): Collection
    {
        $query = TareaPasante::where('user_id', $userId);

        if (isset($filters['estado']) && $filters['estado']) {
            $query->where('estado', $filters['estado']);
        }

        return $query->orderBy('fecha_asignacion', 'desc')->get();
    }

    public function getVencidas(int $userId = null): Collection
    {
        $query = TareaPasante::where('fecha_limite', '<', now())
            ->where('estado', '!=', 'Completada');
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->get();
    }

    public function getPendientesAprobacion(): Collection
    {
        return TareaPasante::with(['pasante'])
            ->where('aprobada', false)
            ->where('estado', 'Completada')
            ->orderBy('fecha_completada', 'desc')
            ->get();
    }

    public function getEstadisticas(int $userId = null): array
    {
        $query = TareaPasante::query();
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        return [
            'total' => $query->count(),
            'completadas' => (clone $query)->where('estado', 'Completada')->count(),
            'pendientes' => (clone $query)->where('estado', 'Pendiente')->count(),
            'en_progreso' => (clone $query)->where('estado', 'En Progreso')->count(),
            'vencidas' => (clone $query)->vencidas()->count(),
            'aprobadas' => (clone $query)->where('aprobada', true)->count(),
            'por_prioridad' => (clone $query)->selectRaw('prioridad, COUNT(*) as total')
                ->groupBy('prioridad')
                ->pluck('total', 'prioridad')
                ->toArray()
        ];
    }
}

