<?php

namespace App\Repositories;

use App\Models\RegistroReproductivo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class RegistroReproductivoRepository
{
    /**
     * Obtener registros paginados con filtros
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = RegistroReproductivo::with(['vaca', 'personal']);

        // Búsqueda por código de vaca
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('vaca', function($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por tipo de evento
        if (isset($filters['tipo_evento']) && !empty($filters['tipo_evento'])) {
            $query->porTipoEvento($filters['tipo_evento']);
        }

        // Filtro por fecha inicio
        if (isset($filters['fecha_inicio']) && !empty($filters['fecha_inicio'])) {
            $query->where('fecha_evento', '>=', $filters['fecha_inicio']);
        }

        // Filtro por fecha fin
        if (isset($filters['fecha_fin']) && !empty($filters['fecha_fin'])) {
            $query->where('fecha_evento', '<=', $filters['fecha_fin']);
        }

        // Filtro por vaca específica
        if (isset($filters['id_vaca']) && !empty($filters['id_vaca'])) {
            $query->porVaca($filters['id_vaca']);
        }

        // Filtro por resultado de palpación
        if (isset($filters['resultado_palpacion']) && !empty($filters['resultado_palpacion'])) {
            $query->where('resultado_palpacion', $filters['resultado_palpacion']);
        }

        return $query->orderBy('fecha_evento', 'desc')->paginate($perPage);
    }

    /**
     * Obtener un registro por ID con todas sus relaciones
     *
     * @param string $id
     * @return RegistroReproductivo|null
     */
    public function findWithRelations(string $id): ?RegistroReproductivo
    {
        return RegistroReproductivo::with(['vaca', 'personal'])->find($id);
    }

    /**
     * Obtener un registro por ID
     *
     * @param string $id
     * @return RegistroReproductivo|null
     */
    public function findById(string $id): ?RegistroReproductivo
    {
        return RegistroReproductivo::find($id);
    }

    /**
     * Crear un nuevo registro
     *
     * @param array $data
     * @return RegistroReproductivo
     */
    public function create(array $data): RegistroReproductivo
    {
        return RegistroReproductivo::create($data);
    }

    /**
     * Actualizar un registro
     *
     * @param RegistroReproductivo $registro
     * @param array $data
     * @return bool
     */
    public function update(RegistroReproductivo $registro, array $data): bool
    {
        return $registro->update($data);
    }

    /**
     * Eliminar un registro
     *
     * @param RegistroReproductivo $registro
     * @return bool
     */
    public function delete(RegistroReproductivo $registro): bool
    {
        return $registro->delete();
    }

    /**
     * Obtener el último parto de una vaca
     *
     * @param int $vacaId
     * @return RegistroReproductivo|null
     */
    public function getUltimoParto(int $vacaId): ?RegistroReproductivo
    {
        return RegistroReproductivo::porVaca($vacaId)
            ->porTipoEvento('Parto')
            ->orderBy('fecha_evento', 'desc')
            ->first();
    }

    /**
     * Obtener la última palpación de una vaca
     *
     * @param int $vacaId
     * @return RegistroReproductivo|null
     */
    public function getUltimaPalpacion(int $vacaId): ?RegistroReproductivo
    {
        return RegistroReproductivo::porVaca($vacaId)
            ->porTipoEvento('Palpación')
            ->orderBy('fecha_evento', 'desc')
            ->first();
    }

    /**
     * Obtener vacas próximas al parto (21 días)
     *
     * @param int $dias
     * @return Collection
     */
    public function getVacasProximasAlParto(int $dias = 21): Collection
    {
        $fechaLimite = now()->addDays($dias);
        
        return RegistroReproductivo::with(['vaca'])
            ->palpacionesPreñadas()
            ->whereNotNull('fecha_probable_parto')
            ->where('fecha_probable_parto', '>=', now())
            ->where('fecha_probable_parto', '<=', $fechaLimite)
            ->orderBy('fecha_probable_parto', 'asc')
            ->get();
    }

    /**
     * Obtener vacas que necesitan revisión de celo
     *
     * @param int $diasDesdeUltimoParto
     * @return Collection
     */
    public function getVacasNecesitanCelo(int $diasDesdeUltimoParto = 21): Collection
    {
        $fechaLimite = now()->subDays($diasDesdeUltimoParto);
        
        // Obtener vacas con último parto hace más de X días y sin palpación preñada posterior
        $partos = RegistroReproductivo::with(['vaca'])
            ->porTipoEvento('Parto')
            ->where('fecha_evento', '<=', $fechaLimite)
            ->orderBy('fecha_evento', 'desc')
            ->get()
            ->unique('id_vaca')
            ->filter(function($parto) {
                // Verificar que no tenga una palpación preñada después del parto
                $ultimaPalpacion = RegistroReproductivo::porVaca($parto->id_vaca)
                    ->porTipoEvento('Palpación')
                    ->where('resultado_palpacion', 'Preñada')
                    ->where('fecha_evento', '>', $parto->fecha_evento)
                    ->orderBy('fecha_evento', 'desc')
                    ->first();
                
                return !$ultimaPalpacion;
            });
        
        return $partos;
    }

    /**
     * Obtener estadísticas reproductivas
     *
     * @return array
     */
    public function getEstadisticas(): array
    {
        return [
            'total_registros' => RegistroReproductivo::count(),
            'palpaciones_preñadas' => RegistroReproductivo::palpacionesPreñadas()->count(),
            'vacas_proximas_parto' => $this->getVacasProximasAlParto()->count(),
            'promedio_dias_abiertos' => RegistroReproductivo::whereNotNull('dias_abiertos')
                ->avg('dias_abiertos') ?? 0,
        ];
    }

    /**
     * Obtener datos para gráfica de eventos por tipo
     *
     * @return array
     */
    public function getDatosGraficaPorTipoEvento(): array
    {
        $datos = RegistroReproductivo::selectRaw('tipo_evento, COUNT(*) as total')
            ->groupBy('tipo_evento')
            ->get();

        return [
            'labels' => $datos->pluck('tipo_evento')->toArray(),
            'data' => $datos->pluck('total')->toArray(),
        ];
    }

    /**
     * Obtener datos para gráfica de vacas preñadas por mes
     *
     * @param int $meses
     * @return array
     */
    public function getDatosGraficaPreñadasPorMes(int $meses = 12): array
    {
        $fechaInicio = now()->subMonths($meses)->startOfMonth();
        
        $datos = RegistroReproductivo::selectRaw('DATE_FORMAT(fecha_evento, "%Y-%m") as mes, COUNT(*) as total')
            ->palpacionesPreñadas()
            ->where('fecha_evento', '>=', $fechaInicio)
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        return [
            'labels' => $datos->pluck('mes')->toArray(),
            'data' => $datos->pluck('total')->toArray(),
        ];
    }

    /**
     * Obtener datos para gráfica de días abiertos
     *
     * @return array
     */
    public function getDatosGraficaDiasAbiertos(): array
    {
        $datos = RegistroReproductivo::selectRaw('
                CASE 
                    WHEN dias_abiertos < 60 THEN "0-60 días"
                    WHEN dias_abiertos < 90 THEN "60-90 días"
                    WHEN dias_abiertos < 120 THEN "90-120 días"
                    ELSE "120+ días"
                END as rango,
                COUNT(*) as total
            ')
            ->whereNotNull('dias_abiertos')
            ->groupBy('rango')
            ->get();

        return [
            'labels' => $datos->pluck('rango')->toArray(),
            'data' => $datos->pluck('total')->toArray(),
        ];
    }
}

