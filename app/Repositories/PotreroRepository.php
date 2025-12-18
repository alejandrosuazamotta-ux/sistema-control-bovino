<?php

namespace App\Repositories;

use App\Models\Potrero;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PotreroRepository
{
    /**
     * Obtener todos los potreros con relaciones
     */
    public function allWithRelations(): Collection
    {
        return Potrero::with('vacas')->get();
    }

    /**
     * Obtener potreros paginados con filtros
     */
    public function paginateWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Potrero::withCount('vacas');

        // Búsqueda por nombre o ubicación
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('ubicacion', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por capacidad
        if (isset($filters['capacidad']) && !empty($filters['capacidad'])) {
            $capacidad = $filters['capacidad'];
            switch ($capacidad) {
                case '1-10':
                    $query->whereBetween('capacidad', [1, 10]);
                    break;
                case '11-20':
                    $query->whereBetween('capacidad', [11, 20]);
                    break;
                case '21-50':
                    $query->whereBetween('capacidad', [21, 50]);
                    break;
                case '50+':
                    $query->where('capacidad', '>', 50);
                    break;
            }
        }

        return $query->orderBy('nombre', 'asc')->paginate($perPage);
    }

    /**
     * Obtener un potrero por ID
     */
    public function findById(string $id): ?Potrero
    {
        return Potrero::with('vacas')->find($id);
    }

    /**
     * Crear un nuevo potrero
     */
    public function create(array $data): Potrero
    {
        return Potrero::create($data);
    }

    /**
     * Actualizar un potrero
     */
    public function update(Potrero $potrero, array $data): bool
    {
        return $potrero->update($data);
    }

    /**
     * Eliminar un potrero
     */
    public function delete(Potrero $potrero): bool
    {
        return $potrero->delete();
    }

    /**
     * Obtener datos para gráfica de potreros por capacidad
     */
    public function getDatosGraficaPorCapacidad(): array
    {
        $datos = Potrero::selectRaw('
            CASE 
                WHEN capacidad BETWEEN 1 AND 10 THEN "1-10"
                WHEN capacidad BETWEEN 11 AND 20 THEN "11-20"
                WHEN capacidad BETWEEN 21 AND 50 THEN "21-50"
                WHEN capacidad > 50 THEN "50+"
                ELSE "Sin capacidad"
            END as rango,
            COUNT(*) as total
        ')
        ->groupBy('rango')
        ->get();

        return [
            'labels' => $datos->pluck('rango')->toArray(),
            'data' => $datos->pluck('total')->toArray(),
        ];
    }

    /**
     * Obtener datos para gráfica de ocupación por potrero
     */
    public function getDatosGraficaOcupacionPorPotrero(int $limit = 10): array
    {
        $datos = Potrero::withCount('vacas')
            ->orderBy('vacas_count', 'desc')
            ->limit($limit)
            ->get();

        return [
            'labels' => $datos->pluck('nombre')->toArray(),
            'data' => $datos->pluck('vacas_count')->toArray(),
        ];
    }

    /**
     * Obtener datos para gráfica de uso de potreros por mes
     */
    public function getDatosGraficaUsoPorMes(int $meses = 12): array
    {
        $fechaInicio = \Carbon\Carbon::now()->subMonths($meses)->startOfMonth();
        
        $datos = \App\Models\AsignacionPotrero::where('fecha_asignacion', '>=', $fechaInicio)
            ->selectRaw('DATE_FORMAT(fecha_asignacion, "%Y-%m") as mes, COUNT(*) as total')
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        return [
            'labels' => $datos->pluck('mes')->toArray(),
            'data' => $datos->pluck('total')->toArray(),
        ];
    }
}

