<?php

namespace App\Services;

use App\Models\InventarioBodega;
use App\Models\MovimientoInventario;
use App\Repositories\InventarioBodegaRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventarioBodegaService
{
    protected InventarioBodegaRepository $repository;

    public function __construct(InventarioBodegaRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Crear un nuevo producto en inventario
     *
     * @param array $data
     * @return InventarioBodega
     * @throws \Exception
     */
    public function create(array $data): InventarioBodega
    {
        return DB::transaction(function () use ($data) {
            $inventario = $this->repository->create($data);

            // Si hay stock inicial, crear movimiento de entrada
            if (isset($data['stock_actual']) && $data['stock_actual'] > 0) {
                $this->registrarEntrada($inventario->id_inventario, [
                    'cantidad' => $data['stock_actual'],
                    'precio_unitario' => $data['precio_unitario'] ?? 0,
                    'fecha_movimiento' => now(),
                    'motivo' => 'Stock inicial',
                    'id_personal' => auth()->user()->personal->id_personal ?? null,
                ]);
            }

            Log::info('Producto de inventario creado', [
                'inventario_id' => $inventario->id_inventario,
                'codigo' => $inventario->codigo,
                'nombre' => $inventario->nombre,
                'user_id' => auth()->id()
            ]);

            return $inventario;
        });
    }

    /**
     * Actualizar un producto en inventario
     *
     * @param InventarioBodega $inventario
     * @param array $data
     * @return InventarioBodega
     * @throws \Exception
     */
    public function update(InventarioBodega $inventario, array $data): InventarioBodega
    {
        return DB::transaction(function () use ($inventario, $data) {
            $this->repository->update($inventario, $data);
            $inventario->refresh();

            Log::info('Producto de inventario actualizado', [
                'inventario_id' => $inventario->id_inventario,
                'codigo' => $inventario->codigo,
                'user_id' => auth()->id()
            ]);

            return $inventario;
        });
    }

    /**
     * Eliminar un producto en inventario
     *
     * @param InventarioBodega $inventario
     * @return bool
     * @throws \Exception
     */
    public function delete(InventarioBodega $inventario): bool
    {
        return DB::transaction(function () use ($inventario) {
            // Verificar que no tenga movimientos asociados
            if ($inventario->movimientos()->count() > 0) {
                throw new \Exception('No se puede eliminar el producto porque tiene movimientos registrados.');
            }

            $inventarioId = $inventario->id_inventario;
            $codigo = $inventario->codigo;

            $deleted = $this->repository->delete($inventario);

            if ($deleted) {
                Log::info('Producto de inventario eliminado', [
                    'inventario_id' => $inventarioId,
                    'codigo' => $codigo,
                    'user_id' => auth()->id()
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Registrar una entrada de inventario
     *
     * @param int $idInventario
     * @param array $data
     * @return MovimientoInventario
     * @throws \Exception
     */
    public function registrarEntrada(int $idInventario, array $data): MovimientoInventario
    {
        return DB::transaction(function () use ($idInventario, $data) {
            $inventario = $this->repository->findById($idInventario);
            
            if (!$inventario) {
                throw new \Exception('Producto de inventario no encontrado.');
            }

            // Calcular valor total si no se proporciona
            if (!isset($data['valor_total'])) {
                $data['valor_total'] = $data['cantidad'] * ($data['precio_unitario'] ?? $inventario->precio_unitario);
            }

            // Crear movimiento
            $movimiento = $this->repository->crearMovimiento([
                'id_inventario' => $idInventario,
                'tipo_movimiento' => 'Entrada',
                'cantidad' => $data['cantidad'],
                'precio_unitario' => $data['precio_unitario'] ?? $inventario->precio_unitario,
                'valor_total' => $data['valor_total'],
                'fecha_movimiento' => $data['fecha_movimiento'] ?? now(),
                'motivo' => $data['motivo'] ?? 'Entrada de inventario',
                'id_personal' => $data['id_personal'] ?? auth()->user()->personal->id_personal ?? null,
                'observaciones' => $data['observaciones'] ?? null,
            ]);

            // Actualizar stock
            $inventario->stock_actual += $data['cantidad'];
            
            // Actualizar precio unitario si se proporciona uno nuevo
            if (isset($data['precio_unitario']) && $data['precio_unitario'] > 0) {
                $inventario->precio_unitario = $data['precio_unitario'];
            }
            
            $inventario->save();

            Log::info('Entrada de inventario registrada', [
                'inventario_id' => $idInventario,
                'movimiento_id' => $movimiento->id_movimiento,
                'cantidad' => $data['cantidad'],
                'user_id' => auth()->id()
            ]);

            return $movimiento;
        });
    }

    /**
     * Registrar una salida de inventario
     *
     * @param int $idInventario
     * @param array $data
     * @return MovimientoInventario
     * @throws \Exception
     */
    public function registrarSalida(int $idInventario, array $data): MovimientoInventario
    {
        return DB::transaction(function () use ($idInventario, $data) {
            $inventario = $this->repository->findById($idInventario);
            
            if (!$inventario) {
                throw new \Exception('Producto de inventario no encontrado.');
            }

            // Verificar stock disponible
            if ($inventario->stock_actual < $data['cantidad']) {
                throw new \Exception('Stock insuficiente. Stock disponible: ' . $inventario->stock_actual);
            }

            // Calcular valor total
            $precioUnitario = $data['precio_unitario'] ?? $inventario->precio_unitario;
            $valorTotal = $data['cantidad'] * $precioUnitario;

            // Crear movimiento
            $movimiento = $this->repository->crearMovimiento([
                'id_inventario' => $idInventario,
                'tipo_movimiento' => 'Salida',
                'cantidad' => $data['cantidad'],
                'precio_unitario' => $precioUnitario,
                'valor_total' => $valorTotal,
                'fecha_movimiento' => $data['fecha_movimiento'] ?? now(),
                'motivo' => $data['motivo'] ?? 'Salida de inventario',
                'id_personal' => $data['id_personal'] ?? auth()->user()->personal->id_personal ?? null,
                'id_vaca' => $data['id_vaca'] ?? null,
                'id_uso_medicamento' => $data['id_uso_medicamento'] ?? null,
                'observaciones' => $data['observaciones'] ?? null,
            ]);

            // Actualizar stock
            $inventario->stock_actual -= $data['cantidad'];
            $inventario->save();

            Log::info('Salida de inventario registrada', [
                'inventario_id' => $idInventario,
                'movimiento_id' => $movimiento->id_movimiento,
                'cantidad' => $data['cantidad'],
                'user_id' => auth()->id()
            ]);

            return $movimiento;
        });
    }

    /**
     * Registrar un ajuste de inventario
     *
     * @param int $idInventario
     * @param array $data
     * @return MovimientoInventario
     * @throws \Exception
     */
    public function registrarAjuste(int $idInventario, array $data): MovimientoInventario
    {
        return DB::transaction(function () use ($idInventario, $data) {
            $inventario = $this->repository->findById($idInventario);
            
            if (!$inventario) {
                throw new \Exception('Producto de inventario no encontrado.');
            }

            $stockAnterior = $inventario->stock_actual;
            $diferencia = $data['stock_nuevo'] - $stockAnterior;

            // Crear movimiento de ajuste
            $movimiento = $this->repository->crearMovimiento([
                'id_inventario' => $idInventario,
                'tipo_movimiento' => 'Ajuste',
                'cantidad' => abs($diferencia),
                'precio_unitario' => $inventario->precio_unitario,
                'valor_total' => abs($diferencia) * $inventario->precio_unitario,
                'fecha_movimiento' => $data['fecha_movimiento'] ?? now(),
                'motivo' => $data['motivo'] ?? 'Ajuste de inventario',
                'id_personal' => $data['id_personal'] ?? auth()->user()->personal->id_personal ?? null,
                'observaciones' => $data['observaciones'] ?? 'Stock anterior: ' . $stockAnterior . ', Stock nuevo: ' . $data['stock_nuevo'],
            ]);

            // Actualizar stock
            $inventario->stock_actual = $data['stock_nuevo'];
            $inventario->save();

            Log::info('Ajuste de inventario registrado', [
                'inventario_id' => $idInventario,
                'movimiento_id' => $movimiento->id_movimiento,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $data['stock_nuevo'],
                'user_id' => auth()->id()
            ]);

            return $movimiento;
        });
    }

    /**
     * Registrar salida automática cuando se usa un medicamento
     *
     * @param int $idMedicamento
     * @param float $cantidad
     * @param int $idUsoMedicamento
     * @param int|null $idVaca
     * @return MovimientoInventario|null
     */
    public function registrarSalidaPorUsoMedicamento(
        int $idMedicamento,
        float $cantidad,
        int $idUsoMedicamento,
        ?int $idVaca = null
    ): ?MovimientoInventario {
        try {
            // Buscar producto de inventario relacionado con el medicamento
            $inventario = InventarioBodega::where('id_medicamento', $idMedicamento)
                ->where('activo', true)
                ->first();

            if (!$inventario) {
                Log::warning('No se encontró producto de inventario para medicamento', [
                    'medicamento_id' => $idMedicamento,
                ]);
                return null;
            }

            return $this->registrarSalida($inventario->id_inventario, [
                'cantidad' => $cantidad,
                'fecha_movimiento' => now(),
                'motivo' => 'Uso de medicamento',
                'id_vaca' => $idVaca,
                'id_uso_medicamento' => $idUsoMedicamento,
                'id_personal' => auth()->user()->personal->id_personal ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al registrar salida por uso de medicamento', [
                'medicamento_id' => $idMedicamento,
                'cantidad' => $cantidad,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Obtener lista paginada de productos con filtros
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(array $filters = [], int $perPage = 10)
    {
        return $this->repository->paginateWithFilters($filters, $perPage);
    }

    /**
     * Obtener un producto por ID
     *
     * @param string $id
     * @return InventarioBodega|null
     */
    public function findById(string $id): ?InventarioBodega
    {
        return $this->repository->findById($id);
    }

    /**
     * Obtener productos con stock bajo
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStockBajo()
    {
        return $this->repository->getStockBajo();
    }

    /**
     * Obtener productos próximos a vencer
     *
     * @param int $dias
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProximosAVencer(int $dias = 30)
    {
        return $this->repository->getProximosAVencer($dias);
    }

    /**
     * Obtener productos vencidos
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVencidos()
    {
        return $this->repository->getVencidos();
    }

    /**
     * Obtener estadísticas de inventario
     *
     * @return array
     */
    public function getEstadisticas(): array
    {
        return $this->repository->getEstadisticas();
    }

    /**
     * Obtener datos para gráficas
     *
     * @return array
     */
    public function getDatosGraficas(): array
    {
        return [
            'stock_por_tipo' => $this->repository->getDatosGraficaStockPorTipo(),
            'movimientos_por_mes' => $this->repository->getDatosGraficaMovimientosPorMes(6),
            'proximos_vencer' => $this->repository->getDatosGraficaProximosVencer(),
        ];
    }

    /**
     * Obtener productos filtrados (sin paginación) para exportación
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFiltered(array $filters = [])
    {
        return $this->repository->getFiltered($filters);
    }

    /**
     * Obtener movimientos de un producto
     *
     * @param int $idInventario
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMovimientosProducto(int $idInventario, array $filters = [])
    {
        return $this->repository->getMovimientosProducto($idInventario, $filters);
    }
}

