<?php

namespace App\Repositories;

use App\Models\InventarioBodega;
use App\Models\MovimientoInventario;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class InventarioBodegaRepository
{
    /**
     * Obtener todos los productos con relaciones
     */
    public function allWithRelations(): Collection
    {
        return InventarioBodega::with(['medicamento', 'movimientos'])
            ->withCount('movimientos')
            ->get();
    }

    /**
     * Obtener productos paginados con filtros
     */
    public function paginateWithFilters(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = InventarioBodega::with(['medicamento'])
            ->withCount('movimientos');

        // Búsqueda por código, nombre o proveedor
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%")
                  ->orWhere('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('proveedor', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por tipo de producto
        if (isset($filters['tipo_producto']) && !empty($filters['tipo_producto'])) {
            $query->where('tipo_producto', $filters['tipo_producto']);
        }

        // Filtro por activo
        if (isset($filters['activo']) && $filters['activo'] !== null) {
            $query->where('activo', $filters['activo']);
        } else {
            // Por defecto mostrar solo activos
            $query->activos();
        }

        // Filtro por stock bajo
        if (isset($filters['stock_bajo']) && $filters['stock_bajo']) {
            $query->stockBajo();
        }

        // Filtro por próximos a vencer
        if (isset($filters['proximos_vencer']) && $filters['proximos_vencer']) {
            $query->proximosAVencer();
        }

        // Filtro por vencidos
        if (isset($filters['vencidos']) && $filters['vencidos']) {
            $query->vencidos();
        }

        return $query->orderBy('nombre', 'asc')->paginate($perPage);
    }

    /**
     * Obtener un producto por ID
     */
    public function findById(string $id): ?InventarioBodega
    {
        return InventarioBodega::with(['medicamento', 'movimientos.personal', 'movimientos.vaca'])
            ->find($id);
    }

    /**
     * Crear un nuevo producto
     */
    public function create(array $data): InventarioBodega
    {
        return InventarioBodega::create($data);
    }

    /**
     * Actualizar un producto
     */
    public function update(InventarioBodega $inventario, array $data): bool
    {
        return $inventario->update($data);
    }

    /**
     * Eliminar un producto
     */
    public function delete(InventarioBodega $inventario): bool
    {
        return $inventario->delete();
    }

    /**
     * Obtener productos con stock bajo
     */
    public function getStockBajo(): Collection
    {
        return InventarioBodega::stockBajo()
            ->activos()
            ->orderBy('stock_actual', 'asc')
            ->get();
    }

    /**
     * Obtener productos próximos a vencer
     */
    public function getProximosAVencer(int $dias = 30): Collection
    {
        return InventarioBodega::proximosAVencer($dias)
            ->activos()
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();
    }

    /**
     * Obtener productos vencidos
     */
    public function getVencidos(): Collection
    {
        return InventarioBodega::vencidos()
            ->orderBy('fecha_vencimiento', 'asc')
            ->get();
    }

    /**
     * Obtener productos filtrados (sin paginación) para exportación
     */
    public function getFiltered(array $filters = []): Collection
    {
        $query = InventarioBodega::with(['medicamento']);

        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%")
                  ->orWhere('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('proveedor', 'LIKE', "%{$search}%");
            });
        }

        if (isset($filters['tipo_producto']) && !empty($filters['tipo_producto'])) {
            $query->where('tipo_producto', $filters['tipo_producto']);
        }

        if (isset($filters['activo']) && $filters['activo'] !== null) {
            $query->where('activo', $filters['activo']);
        }

        return $query->orderBy('nombre', 'asc')->get();
    }

    /**
     * Obtener estadísticas de inventario
     */
    public function getEstadisticas(): array
    {
        $totalProductos = InventarioBodega::activos()->count();
        $stockBajo = InventarioBodega::stockBajo()->activos()->count();
        $proximosVencer = InventarioBodega::proximosAVencer(30)->activos()->count();
        $vencidos = InventarioBodega::vencidos()->count();
        
        $valorTotalStock = InventarioBodega::activos()
            ->selectRaw('SUM(stock_actual * precio_unitario) as total')
            ->value('total') ?? 0;

        return [
            'total_productos' => $totalProductos,
            'stock_bajo' => $stockBajo,
            'proximos_vencer' => $proximosVencer,
            'vencidos' => $vencidos,
            'valor_total_stock' => $valorTotalStock,
        ];
    }

    /**
     * Obtener datos para gráfica de stock por tipo
     */
    public function getDatosGraficaStockPorTipo(): array
    {
        $datos = InventarioBodega::activos()
            ->selectRaw('tipo_producto, SUM(stock_actual) as total_stock')
            ->groupBy('tipo_producto')
            ->get();

        return [
            'labels' => $datos->pluck('tipo_producto')->toArray(),
            'data' => $datos->pluck('total_stock')->toArray(),
        ];
    }

    /**
     * Obtener datos para gráfica de movimientos por mes
     */
    public function getDatosGraficaMovimientosPorMes(int $meses = 6): array
    {
        $fechaInicio = Carbon::now()->subMonths($meses)->startOfMonth();
        
        $entradas = MovimientoInventario::entradas()
            ->where('fecha_movimiento', '>=', $fechaInicio)
            ->selectRaw('DATE_FORMAT(fecha_movimiento, "%Y-%m") as mes, SUM(cantidad) as total')
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        $salidas = MovimientoInventario::salidas()
            ->where('fecha_movimiento', '>=', $fechaInicio)
            ->selectRaw('DATE_FORMAT(fecha_movimiento, "%Y-%m") as mes, SUM(cantidad) as total')
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        return [
            'labels' => $entradas->pluck('mes')->unique()->sort()->values()->toArray(),
            'entradas' => $entradas->pluck('total')->toArray(),
            'salidas' => $salidas->pluck('total')->toArray(),
        ];
    }

    /**
     * Obtener datos para gráfica de productos próximos a vencer
     */
    public function getDatosGraficaProximosVencer(): array
    {
        $productos = InventarioBodega::proximosAVencer(30)
            ->activos()
            ->orderBy('fecha_vencimiento', 'asc')
            ->limit(10)
            ->get();

        return [
            'labels' => $productos->pluck('nombre')->toArray(),
            'dias' => $productos->map(function($p) {
                return $p->dias_hasta_vencimiento ?? 0;
            })->toArray(),
        ];
    }

    /**
     * Crear un movimiento de inventario
     */
    public function crearMovimiento(array $data): MovimientoInventario
    {
        return MovimientoInventario::create($data);
    }

    /**
     * Obtener movimientos de un producto
     */
    public function getMovimientosProducto(int $idInventario, array $filters = []): Collection
    {
        $query = MovimientoInventario::where('id_inventario', $idInventario)
            ->with(['personal', 'vaca', 'usoMedicamento']);

        if (isset($filters['tipo_movimiento']) && !empty($filters['tipo_movimiento'])) {
            $query->where('tipo_movimiento', $filters['tipo_movimiento']);
        }

        if (isset($filters['fecha_inicio']) && !empty($filters['fecha_inicio'])) {
            $query->where('fecha_movimiento', '>=', $filters['fecha_inicio']);
        }

        if (isset($filters['fecha_fin']) && !empty($filters['fecha_fin'])) {
            $query->where('fecha_movimiento', '<=', $filters['fecha_fin']);
        }

        return $query->orderBy('fecha_movimiento', 'desc')->get();
    }
}

