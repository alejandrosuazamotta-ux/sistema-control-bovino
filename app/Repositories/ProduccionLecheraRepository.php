<?php

namespace App\Repositories;

use App\Models\ProduccionLechera;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProduccionLecheraRepository
{
    /**
     * Obtener todas las producciones con sus relaciones
     */
    public function allWithRelations(): Collection
    {
        return ProduccionLechera::with(['vaca', 'personal'])->get();
    }

    /**
     * Obtener producciones paginadas con relaciones y filtros
     */
    public function paginateWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ProduccionLechera::with(['vaca', 'personal']);

        // Búsqueda por código de vaca
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('vaca', function($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%");
            });
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

        // Filtro por personal
        if (isset($filters['id_personal']) && !empty($filters['id_personal'])) {
            $query->where('id_personal', $filters['id_personal']);
        }

        // Filtro por turno
        if (isset($filters['turno']) && !empty($filters['turno'])) {
            $query->where('turno', $filters['turno']);
        }

        // Filtro por destino
        if (isset($filters['destino']) && !empty($filters['destino'])) {
            $query->where('destino', $filters['destino']);
        }

        // Excluir registros de vacas en retiro por defecto
        if (!isset($filters['incluir_retiro']) || !$filters['incluir_retiro']) {
            $query->sinRetiro();
        }

        // Excluir registros excluidos por sanidad por defecto
        if (!isset($filters['incluir_sanidad']) || !$filters['incluir_sanidad']) {
            $query->sinSanidad();
        }

        return $query->orderBy('fecha', 'desc')
                    ->orderBy('turno', 'desc')
                    ->paginate($perPage);
    }

    /**
     * Obtener una producción por ID con todas sus relaciones
     */
    public function findWithRelations(string $id): ?ProduccionLechera
    {
        return ProduccionLechera::with([
            'vaca',
            'personal',
            'vaca.potrero'
        ])->find($id);
    }

    /**
     * Crear una nueva producción
     */
    public function create(array $data): ProduccionLechera
    {
        return ProduccionLechera::create($data);
    }

    /**
     * Actualizar una producción
     */
    public function update(ProduccionLechera $produccion, array $data): bool
    {
        return $produccion->update($data);
    }

    /**
     * Eliminar una producción
     */
    public function delete(ProduccionLechera $produccion): bool
    {
        return $produccion->delete();
    }

    /**
     * Obtener producción por ID
     */
    public function findById(string $id): ?ProduccionLechera
    {
        return ProduccionLechera::find($id);
    }

    /**
     * Verificar si existe un registro duplicado (misma vaca, fecha y turno)
     */
    public function existeDuplicado(int $vacaId, string $fecha, string $turno, ?int $excluirId = null): bool
    {
        $query = ProduccionLechera::where('id_vaca', $vacaId)
            ->where('fecha', $fecha)
            ->where('turno', $turno);

        if ($excluirId) {
            $query->where('id_produccion', '!=', $excluirId);
        }

        return $query->exists();
    }

    /**
     * Obtener total de producción del mes actual
     */
    public function getTotalProduccionMesActual(): float
    {
        return ProduccionLechera::whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->sinExclusiones()
            ->sum('cantidad_leche') ?? 0;
    }

    /**
     * Obtener promedio diario de los últimos N días
     */
    public function getPromedioDiario(int $dias = 30): float
    {
        return ProduccionLechera::where('fecha', '>=', now()->subDays($dias))
            ->sinExclusiones()
            ->avg('cantidad_leche') ?? 0;
    }

    /**
     * Obtener producción por potrero
     */
    public function getProduccionPorPotrero(int $potreroId, ?string $fechaInicio = null, ?string $fechaFin = null): Collection
    {
        $query = ProduccionLechera::with('vaca')
            ->whereHas('vaca', function($q) use ($potreroId) {
                $q->where('id_potrero', $potreroId);
            })
            ->sinExclusiones();

        if ($fechaInicio) {
            $query->where('fecha', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $query->where('fecha', '<=', $fechaFin);
        }

        return $query->get();
    }

    /**
     * Obtener producción mensual
     */
    public function getProduccionMensual(int $mes, int $anio): float
    {
        return ProduccionLechera::whereMonth('fecha', $mes)
            ->whereYear('fecha', $anio)
            ->sinExclusiones()
            ->sum('cantidad_leche') ?? 0;
    }

    /**
     * Obtener datos para gráfica de producción mensual (últimos N meses)
     */
    public function getProduccionMensualGrafica(int $meses = 12): array
    {
        $fechaInicio = now()->subMonths($meses);
        
        $producciones = ProduccionLechera::where('fecha', '>=', $fechaInicio)
            ->sinExclusiones()
            ->selectRaw('YEAR(fecha) as anio, MONTH(fecha) as mes, SUM(cantidad_leche) as total')
            ->groupBy('anio', 'mes')
            ->orderBy('anio', 'asc')
            ->orderBy('mes', 'asc')
            ->get();

        $labels = [];
        $data = [];

        foreach ($producciones as $prod) {
            $mesNombre = \Carbon\Carbon::create($prod->anio, $prod->mes, 1)->format('M Y');
            $labels[] = $mesNombre;
            $data[] = round($prod->total, 2);
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Obtener top N vacas más productivas
     */
    public function getTopVacasProductivas(int $top = 10, int $dias = 30): array
    {
        $fechaInicio = now()->subDays($dias);
        
        $vacas = ProduccionLechera::with('vaca')
            ->where('fecha', '>=', $fechaInicio)
            ->sinExclusiones()
            ->selectRaw('id_vaca, SUM(cantidad_leche) as total')
            ->groupBy('id_vaca')
            ->orderBy('total', 'desc')
            ->limit($top)
            ->get();

        $labels = [];
        $data = [];

        foreach ($vacas as $prod) {
            $labels[] = $prod->vaca->codigo ?? 'N/A';
            $data[] = round($prod->total, 2);
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Obtener curva de lactancia de una vaca
     */
    public function getCurvaLactancia(int $vacaId, ?string $fechaInicio = null): Collection
    {
        $query = ProduccionLechera::where('id_vaca', $vacaId)
            ->sinExclusiones()
            ->orderBy('fecha', 'asc');

        if ($fechaInicio) {
            $query->where('fecha', '>=', $fechaInicio);
        }

        return $query->get();
    }

    /**
     * Obtener pico de producción de una vaca
     */
    public function getPicoProduccion(int $vacaId): ?ProduccionLechera
    {
        return ProduccionLechera::where('id_vaca', $vacaId)
            ->sinExclusiones()
            ->orderBy('cantidad_leche', 'desc')
            ->first();
    }

    /**
     * Obtener promedio de producción de una vaca
     */
    public function getPromedioVaca(int $vacaId, ?int $mes = null, ?int $anio = null): float
    {
        $query = ProduccionLechera::where('id_vaca', $vacaId)
            ->sinExclusiones();

        if ($mes && $anio) {
            $query->whereMonth('fecha', $mes)
                  ->whereYear('fecha', $anio);
        }

        return $query->avg('cantidad_leche') ?? 0;
    }

    /**
     * Obtener datos para gráfica de producción por turno
     */
    public function getProduccionPorTurno(int $dias = 30): array
    {
        $fechaInicio = now()->subDays($dias);
        
        $producciones = ProduccionLechera::where('fecha', '>=', $fechaInicio)
            ->sinExclusiones()
            ->selectRaw('turno, SUM(cantidad_leche) as total')
            ->groupBy('turno')
            ->get();

        $labels = [];
        $data = [];
        $colors = ['AM' => '#28A745', 'PM' => '#0D6EFD'];

        foreach ($producciones as $prod) {
            $labels[] = $prod->turno ?? 'Sin turno';
            $data[] = round($prod->total, 2);
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => array_values(array_intersect_key($colors, array_flip($labels)))
        ];
    }

    /**
     * Obtener datos para gráfica de producción por destino
     */
    public function getProduccionPorDestino(int $dias = 30): array
    {
        $fechaInicio = now()->subDays($dias);
        
        $producciones = ProduccionLechera::where('fecha', '>=', $fechaInicio)
            ->sinExclusiones()
            ->selectRaw('destino, SUM(cantidad_leche) as total')
            ->groupBy('destino')
            ->get();

        $labels = [];
        $data = [];

        foreach ($producciones as $prod) {
            $labels[] = $prod->destino ?? 'Sin destino';
            $data[] = round($prod->total, 2);
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Obtener datos para gráfica de producción diaria (últimos N días)
     */
    public function getProduccionDiaria(int $dias = 30): array
    {
        $fechaInicio = now()->subDays($dias);
        
        $producciones = ProduccionLechera::where('fecha', '>=', $fechaInicio)
            ->sinExclusiones()
            ->selectRaw('DATE(fecha) as fecha, SUM(cantidad_leche) as total')
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get();

        $labels = [];
        $data = [];

        foreach ($producciones as $prod) {
            $labels[] = \Carbon\Carbon::parse($prod->fecha)->format('d/m');
            $data[] = round($prod->total, 2);
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
}

