<?php

namespace App\Repositories;

use App\Models\Retiro;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class RetiroRepository
{
    /**
     * Obtener todos los retiros con relaciones
     */
    public function allWithRelations(): Collection
    {
        return Retiro::with(['vaca', 'usoMedicamento.medicamento'])->get();
    }

    /**
     * Obtener retiros paginados con filtros
     */
    public function paginateWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Retiro::with(['vaca', 'usoMedicamento.medicamento']);

        // Búsqueda por código de vaca
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('vaca', function($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por vaca
        if (isset($filters['id_vaca']) && !empty($filters['id_vaca'])) {
            $query->where('id_vaca', $filters['id_vaca']);
        }

        // Filtro por tipo de retiro
        if (isset($filters['tipo_retiro']) && !empty($filters['tipo_retiro'])) {
            $query->where('tipo_retiro', $filters['tipo_retiro']);
        }

        // Filtro por activo
        if (isset($filters['activo']) && $filters['activo'] !== null) {
            $query->where('activo', $filters['activo']);
        }

        // Filtro por fecha inicio
        if (isset($filters['fecha_inicio']) && !empty($filters['fecha_inicio'])) {
            $query->where('fecha_inicio', '>=', $filters['fecha_inicio']);
        }

        // Filtro por fecha fin
        if (isset($filters['fecha_fin']) && !empty($filters['fecha_fin'])) {
            $query->where('fecha_fin', '<=', $filters['fecha_fin']);
        }

        return $query->orderBy('fecha_inicio', 'desc')->paginate($perPage);
    }

    /**
     * Obtener un retiro por ID
     */
    public function findById(string $id): ?Retiro
    {
        return Retiro::find($id);
    }

    /**
     * Obtener un retiro por ID con todas sus relaciones
     */
    public function findWithRelations(string $id): ?Retiro
    {
        return Retiro::with(['vaca', 'usoMedicamento.medicamento'])->find($id);
    }

    /**
     * Crear un nuevo retiro
     */
    public function create(array $data): Retiro
    {
        return Retiro::create($data);
    }

    /**
     * Actualizar un retiro
     */
    public function update(Retiro $retiro, array $data): bool
    {
        return $retiro->update($data);
    }

    /**
     * Eliminar un retiro
     */
    public function delete(Retiro $retiro): bool
    {
        return $retiro->delete();
    }

    /**
     * Obtener retiros activos de una vaca
     */
    public function getRetirosActivosVaca(int $vacaId, ?Carbon $fecha = null): Collection
    {
        $fecha = $fecha ?? now();
        
        return Retiro::where('id_vaca', $vacaId)
            ->activos()
            ->where('fecha_inicio', '<=', $fecha)
            ->where('fecha_fin', '>=', $fecha)
            ->get();
    }

    /**
     * Verificar si una vaca tiene retiro activo de ordeño
     */
    public function tieneRetiroOrdeñoActivo(int $vacaId, ?Carbon $fecha = null): bool
    {
        $fecha = $fecha ?? now();
        
        return Retiro::where('id_vaca', $vacaId)
            ->activos()
            ->ordeño()
            ->where('fecha_inicio', '<=', $fecha)
            ->where('fecha_fin', '>=', $fecha)
            ->exists();
    }

    /**
     * Verificar si una vaca tiene retiro activo de producción
     */
    public function tieneRetiroProduccionActivo(int $vacaId, ?Carbon $fecha = null): bool
    {
        $fecha = $fecha ?? now();
        
        return Retiro::where('id_vaca', $vacaId)
            ->activos()
            ->produccion()
            ->where('fecha_inicio', '<=', $fecha)
            ->where('fecha_fin', '>=', $fecha)
            ->exists();
    }

    /**
     * Obtener retiros activos actualmente
     */
    public function getRetirosActivos(): Collection
    {
        return Retiro::with(['vaca', 'usoMedicamento.medicamento'])
            ->activos()
            ->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now())
            ->orderBy('fecha_fin', 'asc')
            ->get();
    }

    /**
     * Obtener retiros próximos a vencer (próximos 7 días)
     */
    public function getRetirosProximosAVencer(int $dias = 7): Collection
    {
        $fechaVencimiento = now()->addDays($dias);
        
        return Retiro::with(['vaca', 'usoMedicamento.medicamento'])
            ->activos()
            ->where('fecha_fin', '>=', now())
            ->where('fecha_fin', '<=', $fechaVencimiento)
            ->orderBy('fecha_fin', 'asc')
            ->get();
    }

    /**
     * Obtener retiros filtrados (sin paginación)
     */
    public function getFiltered(array $filters = []): Collection
    {
        $query = Retiro::with(['vaca', 'usoMedicamento.medicamento']);

        // Búsqueda por código de vaca
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('vaca', function($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por vaca
        if (isset($filters['id_vaca']) && !empty($filters['id_vaca'])) {
            $query->where('id_vaca', $filters['id_vaca']);
        }

        // Filtro por tipo de retiro
        if (isset($filters['tipo_retiro']) && !empty($filters['tipo_retiro'])) {
            $query->where('tipo_retiro', $filters['tipo_retiro']);
        }

        // Filtro por activo
        if (isset($filters['activo']) && $filters['activo'] !== null) {
            $query->where('activo', $filters['activo']);
        }

        // Filtro por fecha inicio
        if (isset($filters['fecha_inicio']) && !empty($filters['fecha_inicio'])) {
            $query->where('fecha_inicio', '>=', $filters['fecha_inicio']);
        }

        // Filtro por fecha fin
        if (isset($filters['fecha_fin']) && !empty($filters['fecha_fin'])) {
            $query->where('fecha_fin', '<=', $filters['fecha_fin']);
        }

        return $query->orderBy('fecha_inicio', 'desc')->get();
    }

    /**
     * Obtener datos para gráfica de retiros por tipo
     */
    public function getDatosGraficaPorTipo(): array
    {
        $datos = Retiro::selectRaw('tipo_retiro, COUNT(*) as total')
            ->groupBy('tipo_retiro')
            ->get();

        return [
            'labels' => $datos->pluck('tipo_retiro')->toArray(),
            'data' => $datos->pluck('total')->toArray(),
        ];
    }

    /**
     * Obtener datos para gráfica de retiros por mes
     */
    public function getDatosGraficaRetirosPorMes(int $meses = 12): array
    {
        $fechaInicio = \Carbon\Carbon::now()->subMonths($meses)->startOfMonth();
        
        $datos = Retiro::where('fecha_inicio', '>=', $fechaInicio)
            ->selectRaw('DATE_FORMAT(fecha_inicio, "%Y-%m") as mes, COUNT(*) as total')
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        return [
            'labels' => $datos->pluck('mes')->toArray(),
            'data' => $datos->pluck('total')->toArray(),
        ];
    }

    /**
     * Obtener datos para gráfica de retiros activos vs inactivos
     */
    public function getDatosGraficaActivosVsInactivos(): array
    {
        $activos = Retiro::where('activo', true)->count();
        $inactivos = Retiro::where('activo', false)->count();

        return [
            'labels' => ['Activos', 'Inactivos'],
            'data' => [$activos, $inactivos],
        ];
    }
}

