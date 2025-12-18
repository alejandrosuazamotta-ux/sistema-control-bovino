<?php

namespace App\Repositories;

use App\Models\Salud;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SaludRepository
{
    /**
     * Obtener todos los registros de salud con relaciones
     */
    public function allWithRelations(): Collection
    {
        return Salud::with(['vaca', 'personal'])->get();
    }

    /**
     * Obtener registros paginados con filtros
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginateWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Salud::with(['vaca', 'personal']);

        // Búsqueda por código de vaca
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('vaca', function($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por tipo de registro
        if (isset($filters['tipo_registro']) && !empty($filters['tipo_registro'])) {
            $query->where('tipo_registro', $filters['tipo_registro']);
        }

        // Filtro por tipo de prueba
        if (isset($filters['tipo_prueba']) && !empty($filters['tipo_prueba'])) {
            $query->where('tipo_prueba', $filters['tipo_prueba']);
        }

        // Filtro por resultado
        if (isset($filters['resultado']) && !empty($filters['resultado'])) {
            $query->where('resultado', $filters['resultado']);
        }

        // Filtro por restricción de ordeño
        if (isset($filters['restriccion_ordeño'])) {
            $query->where('restriccion_ordeño', $filters['restriccion_ordeño']);
        }

        // Filtro por inhabilitada
        if (isset($filters['inhabilitada'])) {
            $query->where('inhabilitada', $filters['inhabilitada']);
        }

        // Filtro por fecha inicio
        if (isset($filters['fecha_inicio']) && !empty($filters['fecha_inicio'])) {
            $query->where('fecha', '>=', $filters['fecha_inicio']);
        }

        // Filtro por fecha fin
        if (isset($filters['fecha_fin']) && !empty($filters['fecha_fin'])) {
            $query->where('fecha', '<=', $filters['fecha_fin']);
        }

        // Filtro por vaca específica
        if (isset($filters['id_vaca']) && !empty($filters['id_vaca'])) {
            $query->where('id_vaca', $filters['id_vaca']);
        }

        return $query->orderBy('fecha', 'desc')->paginate($perPage);
    }

    /**
     * Obtener un registro por ID con relaciones
     *
     * @param string $id
     * @return Salud|null
     */
    public function findWithRelations(string $id): ?Salud
    {
        return Salud::with(['vaca', 'personal'])->find($id);
    }

    /**
     * Obtener un registro por ID
     *
     * @param string $id
     * @return Salud|null
     */
    public function findById(string $id): ?Salud
    {
        return Salud::find($id);
    }

    /**
     * Crear un nuevo registro
     *
     * @param array $data
     * @return Salud
     */
    public function create(array $data): Salud
    {
        return Salud::create($data);
    }

    /**
     * Actualizar un registro
     *
     * @param Salud $salud
     * @param array $data
     * @return bool
     */
    public function update(Salud $salud, array $data): bool
    {
        return $salud->update($data);
    }

    /**
     * Eliminar un registro
     *
     * @param Salud $salud
     * @return bool
     */
    public function delete(Salud $salud): bool
    {
        return $salud->delete();
    }

    /**
     * Obtener vacas con restricción de ordeño activa
     *
     * @param string|null $fecha
     * @return Collection
     */
    public function getVacasConRestriccionOrdeño(?string $fecha = null): Collection
    {
        $query = Salud::with('vaca')
            ->where('restriccion_ordeño', true)
            ->where('resultado', 'Positivo');

        if ($fecha) {
            $query->where('fecha', '<=', $fecha)
                ->where(function($q) use ($fecha) {
                    $q->whereNull('fecha_resultado')
                      ->orWhere('fecha_resultado', '>=', $fecha);
                });
        }

        return $query->get();
    }

    /**
     * Obtener vacas inhabilitadas
     *
     * @return Collection
     */
    public function getVacasInhabilitadas(): Collection
    {
        return Salud::with('vaca')
            ->where('inhabilitada', true)
            ->whereIn('tipo_prueba', ['Brucelosis', 'Tuberculosis'])
            ->where('resultado', 'Positivo')
            ->get();
    }

    /**
     * Verificar si una vaca tiene restricción de ordeño activa
     * 
     * UNIFICACIÓN: Consulta tanto Salud como PruebaSanitaria para una sola fuente de verdad
     *
     * @param int $vacaId
     * @param string|null $fecha
     * @return bool
     */
    public function tieneRestriccionOrdeñoActiva(int $vacaId, ?string $fecha = null): bool
    {
        // Consultar en Salud (módulo antiguo)
        $tieneEnSalud = Salud::where('id_vaca', $vacaId)
            ->where('restriccion_ordeño', true)
            ->where('resultado', 'Positivo')
            ->where(function($q) use ($fecha) {
                if ($fecha) {
                    $q->where('fecha', '<=', $fecha)
                      ->where(function($q2) use ($fecha) {
                          $q2->whereNull('fecha_resultado')
                             ->orWhere('fecha_resultado', '>=', $fecha);
                      });
                } else {
                    $q->where(function($q2) {
                        $q2->whereNull('fecha_resultado')
                           ->orWhere('fecha_resultado', '>=', now());
                    });
                }
            })
            ->exists();

        if ($tieneEnSalud) {
            return true;
        }

        // Consultar en PruebaSanitaria (módulo nuevo) - FUENTE DE VERDAD PRINCIPAL
        $tieneEnPruebaSanitaria = \App\Models\PruebaSanitaria::where('id_vaca', $vacaId)
            ->where('restriccion_ordeño', true)
            ->where('resultado', 'Positivo')
            ->where('cerrada', false) // Solo pruebas abiertas
            ->where(function($q) use ($fecha) {
                if ($fecha) {
                    $q->where('fecha_prueba', '<=', $fecha)
                      ->where(function($q2) use ($fecha) {
                          $q2->whereNull('fecha_resultado')
                             ->orWhere('fecha_resultado', '>=', $fecha);
                      });
                } else {
                    $q->where(function($q2) {
                        $q2->whereNull('fecha_resultado')
                           ->orWhere('fecha_resultado', '>=', now());
                    });
                }
            })
            ->exists();

        return $tieneEnPruebaSanitaria;
    }

    /**
     * Verificar si una vaca está inhabilitada
     * 
     * UNIFICACIÓN: Consulta tanto Salud como PruebaSanitaria para una sola fuente de verdad
     *
     * @param int $vacaId
     * @return bool
     */
    public function estaVacaInhabilitada(int $vacaId): bool
    {
        // Consultar en Salud (módulo antiguo)
        $inhabilitadaEnSalud = Salud::where('id_vaca', $vacaId)
            ->where('inhabilitada', true)
            ->whereIn('tipo_prueba', ['Brucelosis', 'Tuberculosis'])
            ->where('resultado', 'Positivo')
            ->exists();

        if ($inhabilitadaEnSalud) {
            return true;
        }

        // Consultar en PruebaSanitaria (módulo nuevo) - FUENTE DE VERDAD PRINCIPAL
        $inhabilitadaEnPruebaSanitaria = \App\Models\PruebaSanitaria::where('id_vaca', $vacaId)
            ->where('inhabilitada', true)
            ->whereIn('tipo_prueba', ['Brucelosis', 'Tuberculosis'])
            ->where('resultado', 'Positivo')
            ->where('cerrada', false) // Solo pruebas abiertas
            ->exists();

        return $inhabilitadaEnPruebaSanitaria;
    }

    /**
     * Obtener estadísticas de salud
     *
     * @return array
     */
    public function getEstadisticas(): array
    {
        return [
            'total_registros' => Salud::count(),
            'pruebas_mastitis' => Salud::where('tipo_prueba', 'Mastitis')->count(),
            'pruebas_brucelosis' => Salud::where('tipo_prueba', 'Brucelosis')->count(),
            'pruebas_tuberculosis' => Salud::where('tipo_prueba', 'Tuberculosis')->count(),
            'resultados_positivos' => Salud::where('resultado', 'Positivo')->count(),
            'resultados_negativos' => Salud::where('resultado', 'Negativo')->count(),
            'resultados_pendientes' => Salud::where('resultado', 'Pendiente')->count(),
            'vacas_con_restriccion' => Salud::where('restriccion_ordeño', true)->distinct('id_vaca')->count(),
            'vacas_inhabilitadas' => Salud::where('inhabilitada', true)->distinct('id_vaca')->count(),
            'pruebas_por_tipo' => Salud::selectRaw('tipo_prueba, COUNT(*) as total')
                ->whereNotNull('tipo_prueba')
                ->groupBy('tipo_prueba')
                ->get()
                ->keyBy('tipo_prueba')
                ->toArray(),
            'pruebas_por_resultado' => Salud::selectRaw('resultado, COUNT(*) as total')
                ->whereNotNull('resultado')
                ->groupBy('resultado')
                ->get()
                ->keyBy('resultado')
                ->toArray(),
            'pruebas_por_mes' => Salud::selectRaw('YEAR(fecha) as año, MONTH(fecha) as mes, COUNT(*) as total')
                ->groupBy('año', 'mes')
                ->orderBy('año', 'desc')
                ->orderBy('mes', 'desc')
                ->limit(12)
                ->get(),
        ];
    }

    /**
     * Obtener datos para gráfica de pruebas por tipo
     *
     * @return array
     */
    public function getDatosGraficaPorTipo(): array
    {
        $datos = Salud::selectRaw('tipo_prueba, COUNT(*) as total')
            ->whereNotNull('tipo_prueba')
            ->groupBy('tipo_prueba')
            ->get();

        return [
            'labels' => $datos->pluck('tipo_prueba')->toArray(),
            'data' => $datos->pluck('total')->toArray(),
        ];
    }

    /**
     * Obtener datos para gráfica de resultados
     *
     * @return array
     */
    public function getDatosGraficaResultados(): array
    {
        $datos = Salud::selectRaw('resultado, COUNT(*) as total')
            ->whereNotNull('resultado')
            ->groupBy('resultado')
            ->get();

        return [
            'labels' => $datos->pluck('resultado')->toArray(),
            'data' => $datos->pluck('total')->toArray(),
        ];
    }

    /**
     * Obtener datos para gráfica de pruebas por mes
     *
     * @param int $meses
     * @return array
     */
    public function getDatosGraficaPorMes(int $meses = 12): array
    {
        $fechaInicio = now()->subMonths($meses)->startOfMonth();
        
        $datos = Salud::selectRaw('DATE_FORMAT(fecha, "%Y-%m") as mes, COUNT(*) as total')
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
     * Obtener pruebas de mastitis positivas recientes
     *
     * @param int $dias
     * @return Collection
     */
    /**
     * Obtener mastitis positivas recientes
     * 
     * UNIFICACIÓN: Consulta tanto Salud como PruebaSanitaria
     *
     * @param int $dias
     * @return Collection
     */
    public function getMastitisPositivasRecientes(int $dias = 30): Collection
    {
        return Salud::with('vaca')
            ->where('tipo_prueba', 'Mastitis')
            ->where('resultado', 'Positivo')
            ->where('fecha', '>=', now()->subDays($dias))
            ->orderBy('fecha', 'desc')
            ->get();
    }
}


