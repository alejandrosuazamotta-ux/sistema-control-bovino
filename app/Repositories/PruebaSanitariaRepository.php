<?php

namespace App\Repositories;

use App\Models\PruebaSanitaria;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PruebaSanitariaRepository
{
    /**
     * Obtener todas las pruebas sanitarias con relaciones
     */
    public function allWithRelations(): Collection
    {
        return PruebaSanitaria::with(['vaca', 'personal', 'usuario'])->get();
    }

    /**
     * Obtener pruebas sanitarias paginadas con filtros
     */
    public function paginateWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = PruebaSanitaria::with(['vaca', 'personal', 'usuario']);

        // Búsqueda por código de vaca
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('vaca', function($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por tipo de prueba
        if (isset($filters['tipo_prueba']) && !empty($filters['tipo_prueba'])) {
            $query->where('tipo_prueba', $filters['tipo_prueba']);
        }

        // Filtro por resultado
        if (isset($filters['resultado']) && !empty($filters['resultado'])) {
            $query->where('resultado', $filters['resultado']);
        }

        // Filtro por vaca específica
        if (isset($filters['id_vaca']) && !empty($filters['id_vaca'])) {
            $query->where('id_vaca', $filters['id_vaca']);
        }

        // Filtro por fecha inicio
        if (isset($filters['fecha_inicio']) && !empty($filters['fecha_inicio'])) {
            $query->where('fecha_prueba', '>=', $filters['fecha_inicio']);
        }

        // Filtro por fecha fin
        if (isset($filters['fecha_fin']) && !empty($filters['fecha_fin'])) {
            $query->where('fecha_prueba', '<=', $filters['fecha_fin']);
        }

        // Filtro por cerradas/abiertas
        if (isset($filters['cerrada'])) {
            $query->where('cerrada', $filters['cerrada']);
        }

        // Filtro por usuario (para Pasante)
        if (isset($filters['user_id']) && !empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query->orderBy('fecha_prueba', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);
    }

    /**
     * Obtener una prueba sanitaria por ID con relaciones
     */
    public function findWithRelations(string $id): ?PruebaSanitaria
    {
        return PruebaSanitaria::with(['vaca', 'personal', 'usuario'])->find($id);
    }

    /**
     * Obtener prueba sanitaria por ID
     */
    public function findById(string $id): ?PruebaSanitaria
    {
        return PruebaSanitaria::find($id);
    }

    /**
     * Crear una nueva prueba sanitaria
     */
    public function create(array $data): PruebaSanitaria
    {
        return PruebaSanitaria::create($data);
    }

    /**
     * Actualizar una prueba sanitaria
     */
    public function update(PruebaSanitaria $prueba, array $data): bool
    {
        return $prueba->update($data);
    }

    /**
     * Eliminar una prueba sanitaria
     */
    public function delete(PruebaSanitaria $prueba): bool
    {
        return $prueba->delete();
    }

    /**
     * Obtener pruebas sanitarias positivas recientes
     */
    public function getPruebasPositivasRecientes(int $dias = 30): Collection
    {
        return PruebaSanitaria::with('vaca')
            ->resultadoPositivo()
            ->where('fecha_prueba', '>=', now()->subDays($dias))
            ->orderBy('fecha_prueba', 'desc')
            ->get();
    }

    /**
     * Obtener pruebas sanitarias pendientes
     */
    public function getPruebasPendientes(): Collection
    {
        return PruebaSanitaria::with(['vaca', 'personal'])
            ->pendientes()
            ->orderBy('fecha_prueba', 'asc')
            ->get();
    }

    /**
     * Obtener pruebas sanitarias por vaca
     */
    public function getPruebasPorVaca(int $vacaId): Collection
    {
        return PruebaSanitaria::where('id_vaca', $vacaId)
            ->orderBy('fecha_prueba', 'desc')
            ->get();
    }

    /**
     * Verificar si una vaca tiene prueba positiva activa
     */
    public function tienePruebaPositivaActiva(int $vacaId, string $tipoPrueba): bool
    {
        return PruebaSanitaria::where('id_vaca', $vacaId)
            ->where('tipo_prueba', $tipoPrueba)
            ->resultadoPositivo()
            ->where(function($query) {
                $query->where('restriccion_ordeño', true)
                      ->orWhere('inhabilitada', true);
            })
            ->exists();
    }

    /**
     * Obtener estadísticas de pruebas sanitarias
     */
    public function getEstadisticas(): array
    {
        $total = PruebaSanitaria::count();
        $positivas = PruebaSanitaria::resultadoPositivo()->count();
        $negativas = PruebaSanitaria::resultadoNegativo()->count();
        $pendientes = PruebaSanitaria::pendientes()->count();

        $porTipo = PruebaSanitaria::selectRaw('tipo_prueba, COUNT(*) as total')
            ->groupBy('tipo_prueba')
            ->get()
            ->pluck('total', 'tipo_prueba')
            ->toArray();

        $mastitisPositivas = PruebaSanitaria::mastitis()->resultadoPositivo()->count();
        $brucelosisPositivas = PruebaSanitaria::brucelosis()->resultadoPositivo()->count();
        $tuberculosisPositivas = PruebaSanitaria::tuberculosis()->resultadoPositivo()->count();

        return [
            'total' => $total,
            'positivas' => $positivas,
            'negativas' => $negativas,
            'pendientes' => $pendientes,
            'por_tipo' => $porTipo,
            'mastitis_positivas' => $mastitisPositivas,
            'brucelosis_positivas' => $brucelosisPositivas,
            'tuberculosis_positivas' => $tuberculosisPositivas,
            'con_restriccion_ordeño' => PruebaSanitaria::conRestriccionOrdeño()->count(),
            'inhabilitadas' => PruebaSanitaria::inhabilitadas()->count()
        ];
    }

    /**
     * Obtener datos para gráficas
     */
    public function getDatosGraficas(): array
    {
        // Pruebas por tipo (últimos 30 días)
        $porTipo = PruebaSanitaria::where('fecha_prueba', '>=', now()->subDays(30))
            ->selectRaw('tipo_prueba, COUNT(*) as total')
            ->groupBy('tipo_prueba')
            ->get()
            ->pluck('total', 'tipo_prueba')
            ->toArray();

        // Pruebas por resultado (últimos 30 días)
        $porResultado = PruebaSanitaria::where('fecha_prueba', '>=', now()->subDays(30))
            ->selectRaw('resultado, COUNT(*) as total')
            ->groupBy('resultado')
            ->get()
            ->pluck('total', 'resultado')
            ->toArray();

        // Pruebas por mes (últimos 12 meses)
        $porMes = PruebaSanitaria::where('fecha_prueba', '>=', now()->subMonths(12))
            ->selectRaw('YEAR(fecha_prueba) as anio, MONTH(fecha_prueba) as mes, COUNT(*) as total')
            ->groupBy('anio', 'mes')
            ->orderBy('anio', 'asc')
            ->orderBy('mes', 'asc')
            ->get();

        $labelsMensual = [];
        $dataMensual = [];
        foreach ($porMes as $item) {
            $labelsMensual[] = \Carbon\Carbon::create($item->anio, $item->mes, 1)->format('M Y');
            $dataMensual[] = $item->total;
        }

        return [
            'por_tipo' => [
                'labels' => array_keys($porTipo),
                'data' => array_values($porTipo)
            ],
            'por_resultado' => [
                'labels' => array_keys($porResultado),
                'data' => array_values($porResultado)
            ],
            'por_mes' => [
                'labels' => $labelsMensual,
                'data' => $dataMensual
            ]
        ];
    }
}

