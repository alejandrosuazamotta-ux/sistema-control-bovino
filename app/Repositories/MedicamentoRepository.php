<?php

namespace App\Repositories;

use App\Models\Medicamento;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MedicamentoRepository
{
    /**
     * Obtener todos los medicamentos con relaciones
     */
    public function allWithRelations(): Collection
    {
        return Medicamento::withCount('usosMedicamentos')->get();
    }

    /**
     * Obtener medicamentos paginados con filtros
     */
    public function paginateWithFilters(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Medicamento::withCount('usosMedicamentos');

        // Búsqueda por nombre o principio activo
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('principio_activo', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por tipo
        if (isset($filters['tipo']) && !empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        // Filtro por activo
        if (isset($filters['activo']) && $filters['activo'] !== null) {
            $query->where('activo', $filters['activo']);
        } else {
            // Por defecto mostrar solo activos
            $query->activos();
        }

        return $query->orderBy('nombre', 'asc')->paginate($perPage);
    }

    /**
     * Obtener un medicamento por ID
     */
    public function findById(string $id): ?Medicamento
    {
        return Medicamento::withCount('usosMedicamentos')->find($id);
    }

    /**
     * Crear un nuevo medicamento
     */
    public function create(array $data): Medicamento
    {
        return Medicamento::create($data);
    }

    /**
     * Actualizar un medicamento
     */
    public function update(Medicamento $medicamento, array $data): bool
    {
        return $medicamento->update($data);
    }

    /**
     * Eliminar un medicamento
     */
    public function delete(Medicamento $medicamento): bool
    {
        return $medicamento->delete();
    }

    /**
     * Obtener medicamentos activos
     */
    public function getActivos(): Collection
    {
        return Medicamento::activos()->orderBy('nombre', 'asc')->get();
    }

    /**
     * Obtener medicamentos con retiro de ordeño
     */
    public function getConRetiroOrdeño(): Collection
    {
        return Medicamento::where('periodo_retiro_dias', '>', 0)
            ->activos()
            ->orderBy('nombre', 'asc')
            ->get();
    }

    /**
     * Obtener medicamentos filtrados (sin paginación)
     */
    public function getFiltered(array $filters = []): Collection
    {
        $query = Medicamento::withCount('usosMedicamentos');

        // Búsqueda por nombre o principio activo
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('principio_activo', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por tipo
        if (isset($filters['tipo']) && !empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        // Filtro por activo
        if (isset($filters['activo']) && $filters['activo'] !== null) {
            $query->where('activo', $filters['activo']);
        }

        return $query->orderBy('nombre', 'asc')->get();
    }

    /**
     * Obtener datos para gráfica de medicamentos por tipo
     */
    public function getDatosGraficaPorTipo(): array
    {
        $datos = Medicamento::selectRaw('tipo, COUNT(*) as total')
            ->groupBy('tipo')
            ->get();

        return [
            'labels' => $datos->pluck('tipo')->toArray(),
            'data' => $datos->pluck('total')->toArray(),
        ];
    }

    /**
     * Obtener datos para gráfica de stock por medicamento (top 10)
     */
    public function getDatosGraficaStockPorMedicamento(int $limit = 10): array
    {
        // Nota: Medicamento no tiene stock directamente, usamos cantidad de usos como proxy
        $datos = Medicamento::withCount('usosMedicamentos')
            ->orderBy('usos_medicamentos_count', 'desc')
            ->limit($limit)
            ->get();

        return [
            'labels' => $datos->pluck('nombre')->toArray(),
            'data' => $datos->pluck('usos_medicamentos_count')->toArray(),
        ];
    }

    /**
     * Obtener datos para gráfica de medicamentos próximos a vencer
     */
    public function getDatosGraficaProximosVencer(int $dias = 30): array
    {
        $fechaLimite = \Carbon\Carbon::now()->addDays($dias);
        
        $datos = Medicamento::whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<=', $fechaLimite)
            ->where('fecha_vencimiento', '>=', now())
            ->orderBy('fecha_vencimiento', 'asc')
            ->limit(10)
            ->get();

        return [
            'labels' => $datos->pluck('nombre')->toArray(),
            'dias' => $datos->map(fn($m) => now()->diffInDays($m->fecha_vencimiento))->toArray(),
        ];
    }
}

