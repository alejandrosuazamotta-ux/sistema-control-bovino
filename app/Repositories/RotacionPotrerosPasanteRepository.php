<?php

namespace App\Repositories;

use App\Models\RotacionPotrerosPasante;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RotacionPotrerosPasanteRepository
{
    public function paginateWithFilters(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = RotacionPotrerosPasante::with(['pasante', 'potreroOrigen', 'potreroDestino', 'vaca', 'aprobador']);

        if (isset($filters['user_id']) && $filters['user_id']) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['id_potrero_origen']) && $filters['id_potrero_origen']) {
            $query->where('id_potrero_origen', $filters['id_potrero_origen']);
        }

        if (isset($filters['id_potrero_destino']) && $filters['id_potrero_destino']) {
            $query->where('id_potrero_destino', $filters['id_potrero_destino']);
        }

        if (isset($filters['id_vaca']) && $filters['id_vaca']) {
            $query->where('id_vaca', $filters['id_vaca']);
        }

        if (isset($filters['fecha_inicio']) && $filters['fecha_inicio']) {
            $query->where('fecha_rotacion', '>=', $filters['fecha_inicio']);
        }

        if (isset($filters['fecha_fin']) && $filters['fecha_fin']) {
            $query->where('fecha_rotacion', '<=', $filters['fecha_fin']);
        }

        if (isset($filters['aprobada']) && $filters['aprobada'] !== '') {
            $query->where('aprobada', $filters['aprobada']);
        }

        return $query->orderBy('fecha_rotacion', 'desc')->paginate($perPage);
    }

    public function findById(string $id): ?RotacionPotrerosPasante
    {
        return RotacionPotrerosPasante::with(['pasante', 'potreroOrigen', 'potreroDestino', 'vaca', 'aprobador'])->find($id);
    }

    public function create(array $data): RotacionPotrerosPasante
    {
        return RotacionPotrerosPasante::create($data);
    }

    public function update(RotacionPotrerosPasante $rotacion, array $data): bool
    {
        return $rotacion->update($data);
    }

    public function delete(RotacionPotrerosPasante $rotacion): bool
    {
        return $rotacion->delete();
    }

    public function getByPasante(int $userId, array $filters = []): Collection
    {
        $query = RotacionPotrerosPasante::where('user_id', $userId);

        if (isset($filters['fecha_inicio']) && $filters['fecha_inicio']) {
            $query->where('fecha_rotacion', '>=', $filters['fecha_inicio']);
        }

        if (isset($filters['fecha_fin']) && $filters['fecha_fin']) {
            $query->where('fecha_rotacion', '<=', $filters['fecha_fin']);
        }

        return $query->orderBy('fecha_rotacion', 'desc')->get();
    }

    public function getPendientesAprobacion(): Collection
    {
        return RotacionPotrerosPasante::with(['pasante', 'vaca', 'potreroOrigen', 'potreroDestino'])
            ->where('aprobada', false)
            ->orderBy('fecha_rotacion', 'desc')
            ->get();
    }

    public function getEstadisticas(int $userId = null): array
    {
        $query = RotacionPotrerosPasante::query();
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        return [
            'total' => $query->count(),
            'aprobadas' => (clone $query)->where('aprobada', true)->count(),
            'pendientes' => (clone $query)->where('aprobada', false)->count(),
        ];
    }
}

