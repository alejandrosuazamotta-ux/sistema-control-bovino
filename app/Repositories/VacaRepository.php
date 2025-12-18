<?php

namespace App\Repositories;

use App\Models\Vaca;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class VacaRepository
{
    /**
     * Obtener todas las vacas con sus relaciones
     */
    public function allWithPotrero(): Collection
    {
        return Vaca::with('potrero')->get();
    }

    /**
     * Obtener vacas paginadas con potrero y filtros
     */
    public function paginateWithFilters(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Vaca::with('potrero');

        // Búsqueda por código o raza
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%")
                  ->orWhere('raza', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por estado de salud
        if (isset($filters['estado_salud']) && !empty($filters['estado_salud'])) {
            $query->where('estado_salud', $filters['estado_salud']);
        }

        // Filtro por estado reproductivo
        if (isset($filters['estado_reproductivo']) && !empty($filters['estado_reproductivo'])) {
            $query->where('estado_reproductivo', $filters['estado_reproductivo']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Obtener una vaca por ID con todas sus relaciones
     */
    public function findWithRelations(string $id): ?Vaca
    {
        return Vaca::with([
            'potrero',
            'crias',
            'registrosReproductivos',
            'produccionLechera',
            'salud',
            'alimentacion'
        ])->find($id);
    }

    /**
     * Crear una nueva vaca
     */
    public function create(array $data): Vaca
    {
        return Vaca::create($data);
    }

    /**
     * Actualizar una vaca
     */
    public function update(Vaca $vaca, array $data): bool
    {
        return $vaca->update($data);
    }

    /**
     * Eliminar una vaca
     */
    public function delete(Vaca $vaca): bool
    {
        return $vaca->delete();
    }

    /**
     * Obtener vaca por ID
     */
    public function findById(string $id): ?Vaca
    {
        return Vaca::find($id);
    }

    /**
     * Verificar capacidad de potrero usando withCount para evitar N+1
     */
    public function checkPotreroCapacity(int $potreroId): array
    {
        $potrero = \App\Models\Potrero::withCount('vacas')->find($potreroId);
        
        if (!$potrero) {
            return ['available' => false, 'message' => 'Potrero no encontrado'];
        }

        $ocupacion = $potrero->vacas_count;
        $disponible = $potrero->capacidad - $ocupacion;

        return [
            'available' => $disponible > 0,
            'ocupacion' => $ocupacion,
            'capacidad' => $potrero->capacidad,
            'disponible' => $disponible,
            'message' => $disponible > 0 
                ? "Hay {$disponible} espacios disponibles" 
                : "El potrero está lleno ({$ocupacion}/{$potrero->capacidad})"
        ];
    }

    /**
     * Obtener últimas vacas registradas
     */
    public function getLatest(int $limit = 5): Collection
    {
        return Vaca::with('potrero')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}

