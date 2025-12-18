<?php

namespace App\Services;

use App\Models\Notificacion;
use App\Models\Vaca;
use App\Models\Cria;
use App\Models\Retiro;
use App\Models\RegistroReproductivo;
use App\Models\Salud;
use App\Models\InventarioBodega;
use App\Services\RegistroReproductivoService;
use App\Services\CriaService;
use App\Services\RetiroService;
use App\Services\SaludService;
use App\Services\InventarioBodegaService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AlertaService
{
    protected RegistroReproductivoService $reproductivoService;
    protected CriaService $criaService;
    protected RetiroService $retiroService;
    protected SaludService $saludService;
    protected InventarioBodegaService $inventarioService;

    public function __construct(
        RegistroReproductivoService $reproductivoService,
        CriaService $criaService,
        RetiroService $retiroService,
        SaludService $saludService,
        InventarioBodegaService $inventarioService
    ) {
        $this->reproductivoService = $reproductivoService;
        $this->criaService = $criaService;
        $this->retiroService = $retiroService;
        $this->saludService = $saludService;
        $this->inventarioService = $inventarioService;
    }

    /**
     * Generar todas las alertas del sistema
     *
     * @return array
     */
    public function generarTodasLasAlertas(): array
    {
        $alertasGeneradas = [];

        // Alertas reproductivas
        $alertasGeneradas['preparto_21'] = $this->generarAlertasPreparto(21);
        $alertasGeneradas['preparto_7'] = $this->generarAlertasPreparto(7);
        $alertasGeneradas['celo'] = $this->generarAlertasCelo();
        $alertasGeneradas['destete'] = $this->generarAlertasDestete();

        // Alertas de producción
        $alertasGeneradas['retiros_activos'] = $this->generarAlertasRetirosActivos();

        // Alertas sanitarias
        $alertasGeneradas['mastitis_recientes'] = $this->generarAlertasMastitisRecientes();

        // Alertas de inventario
        $alertasGeneradas['stock_bajo'] = $this->generarAlertasStockBajo();
        $alertasGeneradas['proximos_vencer'] = $this->generarAlertasProximosAVencer();
        $alertasGeneradas['productos_vencidos'] = $this->generarAlertasProductosVencidos();

        Log::info('Alertas generadas', [
            'total' => array_sum($alertasGeneradas),
            'tipos' => array_keys($alertasGeneradas)
        ]);

        return $alertasGeneradas;
    }

    /**
     * Generar alertas de vacas próximas al parto
     *
     * @param int $diasAntes
     * @return int
     */
    public function generarAlertasPreparto(int $diasAntes = 21): int
    {
        $vacasProximas = $this->reproductivoService->getVacasProximasAlParto($diasAntes);
        $contador = 0;

        foreach ($vacasProximas as $registro) {
            if (!$registro->fecha_probable_parto || !$registro->vaca) {
                continue; // Saltar si no hay fecha probable de parto o vaca
            }
            
            $fechaParto = $registro->fecha_probable_parto;
            $diasRestantes = now()->diffInDays($fechaParto);

            // Verificar si ya existe una alerta similar
            $existeAlerta = Notificacion::where('tipo', 'preparto')
                ->where('entidad_tipo', RegistroReproductivo::class)
                ->where('entidad_id', $registro->id_registro)
                ->where('leida', false)
                ->whereDate('created_at', today())
                ->exists();

            if (!$existeAlerta) {
                Notificacion::create([
                    'tipo' => 'preparto',
                    'nivel' => $diasAntes <= 7 ? 'urgente' : 'advertencia',
                    'titulo' => "Vaca próxima al parto ({$diasRestantes} días)",
                    'mensaje' => "La vaca {$registro->vaca->codigo} está próxima al parto. Fecha probable: {$fechaParto->format('d/m/Y')}",
                    'entidad_tipo' => RegistroReproductivo::class,
                    'entidad_id' => $registro->id_registro,
                    'fecha_referencia' => $fechaParto,
                    'leida' => false,
                    'estado' => 'pendiente',
                ]);
                $contador++;
            }
        }

        return $contador;
    }

    /**
     * Generar alertas de vacas que necesitan celo
     *
     * @return int
     */
    public function generarAlertasCelo(): int
    {
        $vacasNecesitanCelo = $this->reproductivoService->getVacasNecesitanCelo(21);
        $contador = 0;

        foreach ($vacasNecesitanCelo as $vaca) {
            if (!$vaca || !$vaca->id_vaca) {
                continue; // Saltar si no hay vaca válida
            }
            
            // Verificar si ya existe una alerta similar
            $existeAlerta = Notificacion::where('tipo', 'celo')
                ->where('entidad_tipo', Vaca::class)
                ->where('entidad_id', $vaca->id_vaca)
                ->where('leida', false)
                ->whereDate('created_at', today())
                ->exists();

            if (!$existeAlerta) {
                Notificacion::create([
                    'tipo' => 'celo',
                    'nivel' => 'informacion',
                    'titulo' => "Vaca necesita revisión de celo",
                    'mensaje' => "La vaca {$vaca->codigo} necesita revisión de celo. Han pasado más de 21 días desde el último parto.",
                    'entidad_tipo' => Vaca::class,
                    'entidad_id' => $vaca->id_vaca,
                    'leida' => false,
                    'estado' => 'pendiente',
                ]);
                $contador++;
            }
        }

        return $contador;
    }

    /**
     * Generar alertas de crías próximas al destete
     *
     * @return int
     */
    public function generarAlertasDestete(): int
    {
        $criasProximas = $this->criaService->getProximasAlDestete();
        $contador = 0;

        foreach ($criasProximas as $cria) {
            if (!$cria->vacaMadre) {
                continue; // Saltar si no hay vaca madre asociada
            }
            
            $edadDias = $cria->edadDias ?? 0;

            // Verificar si ya existe una alerta similar
            $existeAlerta = Notificacion::where('tipo', 'destete')
                ->where('entidad_tipo', Cria::class)
                ->where('entidad_id', $cria->id_cria)
                ->where('leida', false)
                ->whereDate('created_at', today())
                ->exists();

            if (!$existeAlerta) {
                Notificacion::create([
                    'tipo' => 'destete',
                    'nivel' => 'advertencia',
                    'titulo' => "Cría próxima al destete ({$edadDias} días)",
                    'mensaje' => "La cría de la vaca {$cria->vacaMadre->codigo} está próxima al destete. Edad: {$edadDias} días.",
                    'entidad_tipo' => Cria::class,
                    'entidad_id' => $cria->id_cria,
                    'leida' => false,
                    'estado' => 'pendiente',
                ]);
                $contador++;
            }
        }

        return $contador;
    }

    /**
     * Generar alertas de retiros activos
     *
     * @return int
     */
    public function generarAlertasRetirosActivos(): int
    {
        $retirosActivos = $this->retiroService->getRetirosActivos();
        $contador = 0;

        foreach ($retirosActivos as $retiro) {
            if (!$retiro->fecha_fin || !$retiro->vaca) {
                continue; // Saltar si no hay fecha fin o vaca asociada
            }
            
            $diasRestantes = now()->diffInDays($retiro->fecha_fin);

            // Solo alertar si quedan menos de 3 días
            if ($diasRestantes <= 3 && $diasRestantes >= 0) {
                // Verificar si ya existe una alerta similar
                $existeAlerta = Notificacion::where('tipo', 'retiro')
                    ->where('entidad_tipo', Retiro::class)
                    ->where('entidad_id', $retiro->id_retiro)
                    ->where('leida', false)
                    ->whereDate('created_at', today())
                    ->exists();

                if (!$existeAlerta) {
                    Notificacion::create([
                        'tipo' => 'retiro',
                        'nivel' => $diasRestantes <= 1 ? 'urgente' : 'advertencia',
                        'titulo' => "Retiro activo finaliza pronto ({$diasRestantes} días)",
                        'mensaje' => "El retiro de la vaca {$retiro->vaca->codigo} finaliza en {$diasRestantes} día(s). Fecha fin: {$retiro->fecha_fin->format('d/m/Y')}",
                        'entidad_tipo' => Retiro::class,
                        'entidad_id' => $retiro->id_retiro,
                        'fecha_referencia' => $retiro->fecha_fin,
                        'leida' => false,
                    ]);
                    $contador++;
                }
            }
        }

        return $contador;
    }

    /**
     * Generar alertas de mastitis recientes
     *
     * @return int
     */
    /**
     * Generar alertas de mastitis recientes
     * 
     * UNIFICACIÓN: Consulta tanto Salud como PruebaSanitaria
     *
     * @return int
     */
    public function generarAlertasMastitisRecientes(): int
    {
        $contador = 0;
        $fechaDesde = now()->subDays(7);

        // Consultar en Salud (módulo antiguo)
        $mastitisSalud = Salud::where('tipo_prueba', 'Mastitis')
            ->where('resultado', 'Positivo')
            ->where('fecha', '>=', $fechaDesde)
            ->with('vaca')
            ->get();

        foreach ($mastitisSalud as $salud) {
            if (!$salud->vaca) {
                continue;
            }

            // Verificar si ya existe una alerta similar
            $existeAlerta = Notificacion::where('tipo', 'mastitis')
                ->where('entidad_tipo', Salud::class)
                ->where('entidad_id', $salud->id_salud)
                ->where('leida', false)
                ->whereDate('created_at', today())
                ->exists();

            if (!$existeAlerta) {
                Notificacion::create([
                    'tipo' => 'mastitis',
                    'nivel' => 'urgente',
                    'titulo' => "Mastitis detectada en vaca {$salud->vaca->codigo}",
                    'mensaje' => "Se detectó mastitis en la vaca {$salud->vaca->codigo}. Fecha: {$salud->fecha->format('d/m/Y')}",
                    'entidad_tipo' => Salud::class,
                    'entidad_id' => $salud->id_salud,
                    'fecha_referencia' => $salud->fecha,
                    'leida' => false,
                    'estado' => 'pendiente',
                ]);
                $contador++;
            }
        }

        // Consultar en PruebaSanitaria (módulo nuevo) - FUENTE DE VERDAD PRINCIPAL
        $mastitisPruebaSanitaria = \App\Models\PruebaSanitaria::where('tipo_prueba', 'Mastitis')
            ->where('resultado', 'Positivo')
            ->where('fecha_prueba', '>=', $fechaDesde)
            ->where('cerrada', false) // Solo pruebas abiertas
            ->with('vaca')
            ->get();

        foreach ($mastitisPruebaSanitaria as $prueba) {
            if (!$prueba->vaca) {
                continue;
            }

            // Verificar si ya existe una alerta similar
            $existeAlerta = Notificacion::where('tipo', 'mastitis')
                ->where('entidad_tipo', \App\Models\PruebaSanitaria::class)
                ->where('entidad_id', $prueba->id_prueba)
                ->where('leida', false)
                ->whereDate('created_at', today())
                ->exists();

            if (!$existeAlerta) {
                Notificacion::create([
                    'tipo' => 'mastitis',
                    'nivel' => 'urgente',
                    'titulo' => "Mastitis detectada en vaca {$prueba->vaca->codigo}",
                    'mensaje' => "Se detectó mastitis en la vaca {$prueba->vaca->codigo}. Fecha: {$prueba->fecha_prueba->format('d/m/Y')}",
                    'entidad_tipo' => \App\Models\PruebaSanitaria::class,
                    'entidad_id' => $prueba->id_prueba,
                    'fecha_referencia' => $prueba->fecha_prueba,
                    'leida' => false,
                    'estado' => 'pendiente',
                ]);
                $contador++;
            }
        }

        return $contador;
    }

    /**
     * Generar alertas de productos con stock bajo
     *
     * @return int
     */
    public function generarAlertasStockBajo(): int
    {
        $productosStockBajo = $this->inventarioService->getStockBajo();
        $contador = 0;

        foreach ($productosStockBajo as $producto) {
            // Verificar si ya existe una alerta similar
            $existeAlerta = Notificacion::where('tipo', 'inventario_stock_bajo')
                ->where('entidad_tipo', InventarioBodega::class)
                ->where('entidad_id', $producto->id_inventario)
                ->where('leida', false)
                ->whereDate('created_at', today())
                ->exists();

            if (!$existeAlerta) {
                $diferencia = $producto->stock_minimo - $producto->stock_actual;
                Notificacion::create([
                    'tipo' => 'inventario_stock_bajo',
                    'nivel' => $producto->stock_actual <= 0 ? 'urgente' : 'advertencia',
                    'titulo' => "Stock bajo: {$producto->nombre}",
                    'mensaje' => "El producto {$producto->nombre} ({$producto->codigo}) tiene stock bajo. Stock actual: {$producto->stock_actual} {$producto->unidad_medida}, Mínimo: {$producto->stock_minimo} {$producto->unidad_medida}",
                    'entidad_tipo' => InventarioBodega::class,
                    'entidad_id' => $producto->id_inventario,
                    'fecha_referencia' => now(),
                    'leida' => false,
                    'estado' => 'pendiente',
                ]);
                $contador++;
            }
        }

        return $contador;
    }

    /**
     * Generar alertas de productos próximos a vencer
     *
     * @return int
     */
    public function generarAlertasProximosAVencer(): int
    {
        $productosProximos = $this->inventarioService->getProximosAVencer(30);
        $contador = 0;

        foreach ($productosProximos as $producto) {
            if (!$producto->fecha_vencimiento) {
                continue;
            }

            $diasRestantes = $producto->fecha_vencimiento->diffInDays(now());

            // Verificar si ya existe una alerta similar
            $existeAlerta = Notificacion::where('tipo', 'inventario_vencimiento')
                ->where('entidad_tipo', InventarioBodega::class)
                ->where('entidad_id', $producto->id_inventario)
                ->where('leida', false)
                ->whereDate('created_at', today())
                ->exists();

            if (!$existeAlerta) {
                Notificacion::create([
                    'tipo' => 'inventario_vencimiento',
                    'nivel' => $diasRestantes <= 7 ? 'urgente' : ($diasRestantes <= 15 ? 'advertencia' : 'informacion'),
                    'titulo' => "Producto próximo a vencer: {$producto->nombre}",
                    'mensaje' => "El producto {$producto->nombre} ({$producto->codigo}) vence en {$diasRestantes} día(s). Fecha de vencimiento: {$producto->fecha_vencimiento->format('d/m/Y')}",
                    'entidad_tipo' => InventarioBodega::class,
                    'entidad_id' => $producto->id_inventario,
                    'fecha_referencia' => $producto->fecha_vencimiento,
                    'leida' => false,
                    'estado' => 'pendiente',
                ]);
                $contador++;
            }
        }

        return $contador;
    }

    /**
     * Generar alertas de productos vencidos
     *
     * @return int
     */
    public function generarAlertasProductosVencidos(): int
    {
        $productosVencidos = $this->inventarioService->getVencidos();
        $contador = 0;

        foreach ($productosVencidos as $producto) {
            if (!$producto->fecha_vencimiento) {
                continue;
            }

            // Verificar si ya existe una alerta similar
            $existeAlerta = Notificacion::where('tipo', 'inventario_vencido')
                ->where('entidad_tipo', InventarioBodega::class)
                ->where('entidad_id', $producto->id_inventario)
                ->where('leida', false)
                ->whereDate('created_at', today())
                ->exists();

            if (!$existeAlerta) {
                $diasVencido = now()->diffInDays($producto->fecha_vencimiento);
                Notificacion::create([
                    'tipo' => 'inventario_vencido',
                    'nivel' => 'urgente',
                    'titulo' => "Producto vencido: {$producto->nombre}",
                    'mensaje' => "El producto {$producto->nombre} ({$producto->codigo}) está vencido desde hace {$diasVencido} día(s). Fecha de vencimiento: {$producto->fecha_vencimiento->format('d/m/Y')}",
                    'entidad_tipo' => InventarioBodega::class,
                    'entidad_id' => $producto->id_inventario,
                    'fecha_referencia' => $producto->fecha_vencimiento,
                    'leida' => false,
                    'estado' => 'pendiente',
                ]);
                $contador++;
            }
        }

        return $contador;
    }

    /**
     * Obtener alertas no leídas
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAlertasNoLeidas(int $limit = 10)
    {
        return Notificacion::where('leida', false)
            ->orderBy('nivel', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener todas las alertas con paginación
     *
     * @param array $filters
     * @param int $perPage
     * @param int|null $userId Para filtrar por usuario (pasante)
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAlertasPaginadas(array $filters = [], int $perPage = 15, ?int $userId = null)
    {
        $query = Notificacion::query();

        // Filtro por usuario (para pasantes)
        if ($userId !== null) {
            $query->asignadasA($userId);
        }

        if (isset($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (isset($filters['nivel'])) {
            $query->where('nivel', $filters['nivel']);
        }

        if (isset($filters['leida'])) {
            $query->where('leida', $filters['leida']);
        }

        if (isset($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Marcar alerta como leída
     *
     * @param int|string $id
     * @return bool
     */
    public function marcarComoLeida($id): bool
    {
        $notificacion = Notificacion::where('id_notificacion', $id)->first();
        if ($notificacion) {
            $notificacion->leida = true;
            $notificacion->fecha_leida = now();
            return $notificacion->save();
        }
        return false;
    }

    /**
     * Marcar todas las alertas como leídas
     *
     * @return int
     */
    public function marcarTodasComoLeidas(): int
    {
        return Notificacion::where('leida', false)
            ->update([
                'leida' => true,
                'fecha_leida' => now()
            ]);
    }

    /**
     * Limpiar alertas antiguas (más de 30 días)
     *
     * @return int
     */
    public function limpiarAlertasAntiguas(): int
    {
        return Notificacion::where('created_at', '<', now()->subDays(30))
            ->where('leida', true)
            ->delete();
    }

    /**
     * Obtener contador de alertas no leídas por tipo
     *
     * @param int|null $userId Para filtrar por usuario (pasante)
     * @return array
     */
    public function getContadorAlertas(?int $userId = null): array
    {
        $query = Notificacion::query();
        
        if ($userId !== null) {
            $query->asignadasA($userId);
        }

        return [
            'total' => (clone $query)->where('leida', false)->count(),
            'urgentes' => (clone $query)->where('leida', false)->where('nivel', 'urgente')->count(),
            'advertencias' => (clone $query)->where('leida', false)->where('nivel', 'advertencia')->count(),
            'informacion' => (clone $query)->where('leida', false)->where('nivel', 'informacion')->count(),
            'pendientes' => (clone $query)->where('estado', 'pendiente')->count(),
            'vistas' => (clone $query)->where('estado', 'vista')->count(),
            'atendidas' => (clone $query)->where('estado', 'atendida')->count(),
        ];
    }

    /**
     * Marcar alerta como vista (para pasante)
     *
     * @param int|string $id
     * @return bool
     */
    public function marcarComoVista($id): bool
    {
        $notificacion = Notificacion::where('id_notificacion', $id)->first();
        if ($notificacion) {
            return $notificacion->marcarComoVista();
        }
        return false;
    }

    /**
     * Marcar alerta como atendida (solo admin)
     *
     * @param int|string $id
     * @return bool
     */
    public function marcarComoAtendida($id): bool
    {
        $notificacion = Notificacion::where('id_notificacion', $id)->first();
        if ($notificacion) {
            return $notificacion->marcarComoAtendida();
        }
        return false;
    }

    /**
     * Obtener datos para gráficas
     *
     * @param int|null $userId Para filtrar por usuario (pasante)
     * @return array
     */
    public function getDatosGraficas(?int $userId = null): array
    {
        $query = Notificacion::query();
        
        if ($userId !== null) {
            $query->asignadasA($userId);
        }

        // Alertas por tipo (últimos 30 días)
        $alertasPorTipo = (clone $query)
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('tipo, COUNT(*) as total')
            ->groupBy('tipo')
            ->get()
            ->pluck('total', 'tipo')
            ->toArray();

        // Alertas por estado
        $alertasPorEstado = (clone $query)
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->get()
            ->pluck('total', 'estado')
            ->toArray();

        // Alertas por nivel
        $alertasPorNivel = (clone $query)
            ->where('leida', false)
            ->selectRaw('nivel, COUNT(*) as total')
            ->groupBy('nivel')
            ->get()
            ->pluck('total', 'nivel')
            ->toArray();

        // Alertas por mes (últimos 6 meses)
        $alertasPorMes = (clone $query)
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mes, COUNT(*) as total')
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        return [
            'por_tipo' => [
                'labels' => array_keys($alertasPorTipo),
                'data' => array_values($alertasPorTipo),
            ],
            'por_estado' => [
                'labels' => array_keys($alertasPorEstado),
                'data' => array_values($alertasPorEstado),
            ],
            'por_nivel' => [
                'labels' => array_keys($alertasPorNivel),
                'data' => array_values($alertasPorNivel),
            ],
            'por_mes' => [
                'labels' => $alertasPorMes->pluck('mes')->toArray(),
                'data' => $alertasPorMes->pluck('total')->toArray(),
            ],
        ];
    }
}

