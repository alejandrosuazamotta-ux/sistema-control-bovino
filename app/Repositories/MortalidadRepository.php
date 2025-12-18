<?php

namespace App\Repositories;

use App\Models\Mortalidad;
use App\Models\Vaca;
use App\Models\Cria;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MortalidadRepository
{
    /**
     * Obtener todas las mortalidades con relaciones
     */
    public function allWithRelations(): Collection
    {
        return Mortalidad::with('animal')->get();
    }

    /**
     * Obtener mortalidades paginadas con filtros
     */
    public function paginateWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Mortalidad::with([
            'animal',
            'animal.vacaMadre' => function($query) {
                // Solo cargar vacaMadre si el animal es una Cria
                $query->select('id_vaca', 'codigo', 'raza');
            }
        ]);

        // Búsqueda por código de animal
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('causa', 'LIKE', "%{$search}%")
                  ->orWhere('acta', 'LIKE', "%{$search}%")
                  ->orWhereHasMorph('animal', [Vaca::class], function($q2) use ($search) {
                      $q2->where('codigo', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHasMorph('animal', [Cria::class], function($q2) use ($search) {
                      $q2->where('nombre_cria', 'LIKE', "%{$search}%")
                         ->orWhere('sinigan', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filtro por tipo de animal
        if (isset($filters['animal_type']) && !empty($filters['animal_type'])) {
            $query->porTipoAnimal($filters['animal_type']);
        }

        // Filtro por clasificación
        if (isset($filters['clasificacion']) && !empty($filters['clasificacion'])) {
            $query->porClasificacion($filters['clasificacion']);
        }

        // Filtro por fecha inicio
        if (isset($filters['fecha_inicio']) && !empty($filters['fecha_inicio'])) {
            $query->where('fecha', '>=', $filters['fecha_inicio']);
        }

        // Filtro por fecha fin
        if (isset($filters['fecha_fin']) && !empty($filters['fecha_fin'])) {
            $query->where('fecha', '<=', $filters['fecha_fin']);
        }

        return $query->orderBy('fecha', 'desc')->paginate($perPage);
    }

    /**
     * Obtener una mortalidad por ID con todas sus relaciones
     */
    public function findWithRelations(string $id): ?Mortalidad
    {
        return Mortalidad::with([
            'animal',
            'animal.vacaMadre' => function($query) {
                // Solo cargar vacaMadre si el animal es una Cria
                $query->select('id_vaca', 'codigo', 'raza');
            }
        ])->find($id);
    }

    /**
     * Obtener una mortalidad por ID
     */
    public function findById(string $id): ?Mortalidad
    {
        return Mortalidad::find($id);
    }

    /**
     * Crear una nueva mortalidad
     */
    public function create(array $data): Mortalidad
    {
        return Mortalidad::create($data);
    }

    /**
     * Actualizar una mortalidad
     */
    public function update(Mortalidad $mortalidad, array $data): bool
    {
        return $mortalidad->update($data);
    }

    /**
     * Eliminar una mortalidad
     */
    public function delete(Mortalidad $mortalidad): bool
    {
        return $mortalidad->delete();
    }

    /**
     * Obtener mortalidades recientes
     */
    public function getRecientes(int $dias = 30): Collection
    {
        return Mortalidad::with('animal')
            ->recientes($dias)
            ->orderBy('fecha', 'desc')
            ->get();
    }

    /**
     * Obtener estadísticas de mortalidad
     */
    public function getEstadisticas(): array
    {
        return [
            'total_mortalidad' => Mortalidad::count(),
            'mortalidad_este_mes' => Mortalidad::whereMonth('fecha', now()->month)
                ->whereYear('fecha', now()->year)
                ->count(),
            'mortalidad_por_clasificacion' => Mortalidad::selectRaw('clasificacion, COUNT(*) as total')
                ->groupBy('clasificacion')
                ->get(),
            'mortalidad_por_tipo' => [
                'vacas' => Mortalidad::porTipoAnimal(Vaca::class)->count(),
                'crias' => Mortalidad::porTipoAnimal(Cria::class)->count(),
            ],
            'mortalidad_ultimos_30_dias' => Mortalidad::recientes(30)->count(),
        ];
    }

    /**
     * Obtener datos para gráfica de mortalidad por mes
     *
     * @param int $meses
     * @return array
     */
    public function getDatosGraficaMortalidadPorMes(int $meses = 12): array
    {
        $fechaInicio = now()->subMonths($meses)->startOfMonth();
        
        $datos = Mortalidad::selectRaw('DATE_FORMAT(fecha, "%Y-%m") as mes, COUNT(*) as total')
            ->where('fecha', '>=', $fechaInicio)
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        return [
            'labels' => $datos->pluck('mes')->toArray(),
            'data' => $datos->pluck('total')->toArray(),
        ];
    }

    /**
     * Obtener datos para gráfica de mortalidad por tipo de animal
     *
     * @return array
     */
    public function getDatosGraficaPorTipoAnimal(): array
    {
        $vacas = Mortalidad::porTipoAnimal(Vaca::class)->count();
        $crias = Mortalidad::porTipoAnimal(Cria::class)->count();

        return [
            'labels' => ['Vacas', 'Crías'],
            'data' => [$vacas, $crias],
        ];
    }

    /**
     * Obtener datos para gráfica de mortalidad por clasificación
     *
     * @return array
     */
    public function getDatosGraficaPorClasificacion(): array
    {
        $datos = Mortalidad::selectRaw('clasificacion, COUNT(*) as total')
            ->groupBy('clasificacion')
            ->get();

        return [
            'labels' => $datos->pluck('clasificacion')->toArray(),
            'data' => $datos->pluck('total')->toArray(),
        ];
    }

    /**
     * Obtener mortalidades por rango de fechas
     */
    public function getPorRangoFechas(string $fechaInicio, string $fechaFin): Collection
    {
        return Mortalidad::with('animal')
            ->porRangoFechas($fechaInicio, $fechaFin)
            ->orderBy('fecha', 'desc')
            ->get();
    }
}

