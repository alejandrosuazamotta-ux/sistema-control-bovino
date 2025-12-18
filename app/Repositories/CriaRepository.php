<?php

namespace App\Repositories;

use App\Models\Cria;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CriaRepository
{
    /**
     * Obtener todas las crías con relaciones
     */
    public function allWithRelations(): Collection
    {
        return Cria::with('vacaMadre')->get();
    }

    /**
     * Obtener crías paginadas con filtros
     */
    public function paginateWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Cria::with('vacaMadre');

        // Búsqueda por código de vaca madre o nombre de cría
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('nombre_cria', 'LIKE', "%{$search}%")
                  ->orWhere('sinigan', 'LIKE', "%{$search}%")
                  ->orWhereHas('vacaMadre', function($q2) use ($search) {
                      $q2->where('codigo', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filtro por sexo
        if (isset($filters['sexo']) && !empty($filters['sexo'])) {
            $query->porSexo($filters['sexo']);
        }

        // Filtro por método de concepción
        if (isset($filters['concepcion']) && !empty($filters['concepcion'])) {
            $query->porConcepcion($filters['concepcion']);
        }

        // Filtro por estado de destete
        if (isset($filters['estado_destete']) && !empty($filters['estado_destete'])) {
            $query->where('estado_destete', $filters['estado_destete']);
        }

        // Filtro por fecha inicio
        if (isset($filters['fecha_inicio']) && !empty($filters['fecha_inicio'])) {
            $query->where('fecha_nacimiento', '>=', $filters['fecha_inicio']);
        }

        // Filtro por fecha fin
        if (isset($filters['fecha_fin']) && !empty($filters['fecha_fin'])) {
            $query->where('fecha_nacimiento', '<=', $filters['fecha_fin']);
        }

        // Filtro por vaca madre específica
        if (isset($filters['id_vaca_madre']) && !empty($filters['id_vaca_madre'])) {
            $query->where('id_vaca_madre', $filters['id_vaca_madre']);
        }

        return $query->orderBy('fecha_nacimiento', 'desc')->paginate($perPage);
    }

    /**
     * Obtener una cría por ID con todas sus relaciones
     */
    public function findWithRelations(string $id): ?Cria
    {
        return Cria::with('vacaMadre')->find($id);
    }

    /**
     * Obtener una cría por ID
     */
    public function findById(string $id): ?Cria
    {
        return Cria::find($id);
    }

    /**
     * Crear una nueva cría
     */
    public function create(array $data): Cria
    {
        return Cria::create($data);
    }

    /**
     * Actualizar una cría
     */
    public function update(Cria $cria, array $data): bool
    {
        return $cria->update($data);
    }

    /**
     * Eliminar una cría
     */
    public function delete(Cria $cria): bool
    {
        return $cria->delete();
    }

    /**
     * Obtener crías de una vaca madre
     */
    public function getPorVacaMadre(int $vacaId): Collection
    {
        return Cria::with('vacaMadre')
            ->where('id_vaca_madre', $vacaId)
            ->orderBy('fecha_nacimiento', 'desc')
            ->get();
    }

    /**
     * Obtener crías próximas al destete (50-70 días)
     */
    public function getProximasAlDestete(): Collection
    {
        $fechaInicio = now()->subDays(70);
        $fechaFin = now()->subDays(50);
        
        return Cria::with('vacaMadre')
            ->noDestetadas()
            ->where('fecha_nacimiento', '>=', $fechaInicio)
            ->where('fecha_nacimiento', '<=', $fechaFin)
            ->orderBy('fecha_nacimiento', 'asc')
            ->get();
    }

    /**
     * Obtener estadísticas de crías
     */
    public function getEstadisticas(): array
    {
        return [
            'total_crias' => Cria::count(),
            'crias_macho' => Cria::porSexo('Macho')->count(),
            'crias_hembra' => Cria::porSexo('Hembra')->count(),
            'crias_destetadas' => Cria::destetadas()->count(),
            'crias_no_destetadas' => Cria::noDestetadas()->count(),
            'crias_este_mes' => Cria::whereMonth('fecha_nacimiento', now()->month)
                ->whereYear('fecha_nacimiento', now()->year)
                ->count(),
            'promedio_peso' => Cria::avg('peso') ?? 0,
            'crias_por_concepcion' => Cria::selectRaw('concepcion, COUNT(*) as total')
                ->whereNotNull('concepcion')
                ->groupBy('concepcion')
                ->get(),
        ];
    }

    /**
     * Obtener datos para gráfica de nacimientos por mes
     *
     * @param int $meses
     * @return array
     */
    public function getDatosGraficaNacimientosPorMes(int $meses = 12): array
    {
        $fechaInicio = now()->subMonths($meses)->startOfMonth();
        
        $datos = Cria::selectRaw('DATE_FORMAT(fecha_nacimiento, "%Y-%m") as mes, COUNT(*) as total')
            ->where('fecha_nacimiento', '>=', $fechaInicio)
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        return [
            'labels' => $datos->pluck('mes')->toArray(),
            'data' => $datos->pluck('total')->toArray(),
        ];
    }

    /**
     * Obtener datos para gráfica de crías por sexo
     *
     * @return array
     */
    public function getDatosGraficaPorSexo(): array
    {
        $datos = Cria::selectRaw('sexo, COUNT(*) as total')
            ->whereNotNull('sexo')
            ->groupBy('sexo')
            ->get();

        return [
            'labels' => $datos->pluck('sexo')->toArray(),
            'data' => $datos->pluck('total')->toArray(),
        ];
    }

    /**
     * Obtener datos para gráfica de crías por método de concepción
     *
     * @return array
     */
    public function getDatosGraficaPorConcepcion(): array
    {
        $datos = Cria::selectRaw('concepcion, COUNT(*) as total')
            ->whereNotNull('concepcion')
            ->groupBy('concepcion')
            ->get();

        return [
            'labels' => $datos->pluck('concepcion')->toArray(),
            'data' => $datos->pluck('total')->toArray(),
        ];
    }

    /**
     * Obtener crías por rango de edad
     */
    public function getPorRangoEdad(int $edadMinDias, int $edadMaxDias): Collection
    {
        $fechaInicio = now()->subDays($edadMaxDias);
        $fechaFin = now()->subDays($edadMinDias);
        
        return Cria::with('vacaMadre')
            ->whereBetween('fecha_nacimiento', [$fechaInicio, $fechaFin])
            ->orderBy('fecha_nacimiento', 'desc')
            ->get();
    }
}

