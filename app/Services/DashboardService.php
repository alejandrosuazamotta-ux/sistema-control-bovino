<?php

namespace App\Services;

use App\Models\Vaca;
use App\Models\Cria;
use App\Models\ProduccionLechera;
use App\Models\RegistroReproductivo;
use App\Models\Mortalidad;
use App\Models\Potrero;
use App\Repositories\ProduccionLecheraRepository;
use App\Repositories\RegistroReproductivoRepository;
use App\Repositories\MortalidadRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    protected ProduccionLecheraRepository $produccionRepository;
    protected RegistroReproductivoRepository $reproductivoRepository;
    protected MortalidadRepository $mortalidadRepository;

    public function __construct(
        ProduccionLecheraRepository $produccionRepository,
        RegistroReproductivoRepository $reproductivoRepository,
        MortalidadRepository $mortalidadRepository
    ) {
        $this->produccionRepository = $produccionRepository;
        $this->reproductivoRepository = $reproductivoRepository;
        $this->mortalidadRepository = $mortalidadRepository;
    }

    /**
     * Obtener datos para gráfica de producción diaria (últimos 30 días) - CON CACHÉ
     */
    public function getProduccionDiaria(int $dias = 30): array
    {
        return Cache::remember("dashboard.produccion_diaria.{$dias}", 300, function () use ($dias) {
            $fechaInicio = now()->subDays($dias);
            
            $producciones = ProduccionLechera::where('fecha', '>=', $fechaInicio)
                ->sinRetiro()
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
        });
    }

    /**
     * Obtener datos para gráfica de producción mensual (últimos 12 meses)
     */
    public function getProduccionMensual(int $meses = 12): array
    {
        return Cache::remember("dashboard.produccion_mensual.{$meses}", 600, function () use ($meses) {
            $fechaInicio = now()->subMonths($meses);
            
            $producciones = ProduccionLechera::where('fecha', '>=', $fechaInicio)
                ->sinRetiro()
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
        });
    }

    /**
     * Obtener datos para gráfica de vacas por estado (donut) - CON CACHÉ
     */
    public function getVacasPorEstado(): array
    {
        return Cache::remember('dashboard.vacas_por_estado', 600, function () {
            $estados = Vaca::selectRaw('estado_salud, COUNT(*) as total')
                ->groupBy('estado_salud')
                ->get();

            $labels = [];
            $data = [];
            $colors = [
                'Sana' => '#28A745',
                'En tratamiento' => '#FFC107',
                'En observación' => '#17A2B8',
                'Muerta' => '#DC3545'
            ];

            foreach ($estados as $estado) {
                $labels[] = $estado->estado_salud;
                $data[] = $estado->total;
            }

            return [
                'labels' => $labels,
                'data' => $data,
                'colors' => array_values(array_intersect_key($colors, array_flip($labels)))
            ];
        });
    }

    /**
     * Obtener datos para gráfica de producción por potrero (pastel) - CON CACHÉ
     */
    public function getProduccionPorPotrero(): array
    {
        return Cache::remember('dashboard.produccion_por_potrero', 300, function () {
            $producciones = ProduccionLechera::with('vaca.potrero')
                ->sinRetiro()
                ->where('fecha', '>=', now()->subDays(30))
                ->get()
                ->groupBy(function($item) {
                    return $item->vaca->potrero->nombre ?? 'Sin Potrero';
                })
                ->map(function($group) {
                    return $group->sum('cantidad_leche');
                });

            $labels = $producciones->keys()->toArray();
            $data = $producciones->values()->toArray();

            return [
                'labels' => $labels,
                'data' => array_map('round', $data)
            ];
        });
    }

    /**
     * Obtener datos para gráfica de ranking de vacas productivas (top 10)
     */
    public function getRankingVacasProductivas(int $top = 10): array
    {
        return Cache::remember("dashboard.ranking_vacas.{$top}", 300, function () use ($top) {
            $vacas = ProduccionLechera::with('vaca')
                ->sinRetiro()
                ->where('fecha', '>=', now()->subDays(30))
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
        });
    }

    /**
     * Obtener datos para gráfica de estado reproductivo (barras)
     */
    public function getEstadoReproductivo(): array
    {
        return Cache::remember('dashboard.estado_reproductivo', 600, function () {
            $estados = Vaca::selectRaw('estado_reproductivo, COUNT(*) as total')
                ->groupBy('estado_reproductivo')
                ->get();

            $labels = [];
            $data = [];

            foreach ($estados as $estado) {
                $labels[] = $estado->estado_reproductivo;
                $data[] = $estado->total;
            }

            return [
                'labels' => $labels,
                'data' => $data
            ];
        });
    }

    /**
     * Obtener estadísticas generales del dashboard (con caché)
     */
    public function getEstadisticasGenerales(): array
    {
        return Cache::remember('dashboard.estadisticas_generales', 300, function () {
            return [
                'total_vacas' => Vaca::count(),
                'vacas_activas' => Vaca::where('estado_salud', '!=', 'Muerta')->count(),
                'total_crias' => Cria::count(),
                'crias_destetadas' => Cria::where('estado_destete', 'Destetada')->count(),
                'produccion_hoy' => ProduccionLechera::whereDate('fecha', today())
                    ->sinRetiro()
                    ->sum('cantidad_leche') ?? 0,
                'produccion_mes' => $this->produccionRepository->getTotalProduccionMesActual(),
                'vacas_preñadas' => Vaca::where('estado_reproductivo', 'Preñada')->count(),
                'vacas_lactancia' => Vaca::where('estado_reproductivo', 'Lactancia')->count(),
                'mortalidad_mes' => Mortalidad::whereMonth('fecha', now()->month)
                    ->whereYear('fecha', now()->year)
                    ->count(),
            ];
        });
    }

    /**
     * Obtener alertas del sistema (con caché corto)
     */
    public function getAlertas(): array
    {
        return Cache::remember('dashboard.alertas', 60, function () {
            return $this->generarAlertas();
        });
    }

    /**
     * Generar alertas del sistema
     */
    protected function generarAlertas(): array
    {
        $alertas = [];

        // Vacas próximas al parto (21 días)
        $vacasProximasParto = $this->reproductivoRepository->getVacasProximasAlParto(21);
        if ($vacasProximasParto->count() > 0) {
            $alertas[] = [
                'tipo' => 'warning',
                'icono' => 'fa-baby',
                'titulo' => 'Vacas Próximas al Parto',
                'mensaje' => "Hay {$vacasProximasParto->count()} vaca(s) próximas al parto (21 días)",
                'cantidad' => $vacasProximasParto->count()
            ];
        }

        // Vacas que necesitan celo
        $vacasNecesitanCelo = $this->reproductivoRepository->getVacasNecesitanCelo();
        if ($vacasNecesitanCelo->count() > 0) {
            $alertas[] = [
                'tipo' => 'info',
                'icono' => 'fa-heart',
                'titulo' => 'Vacas que Necesitan Celo',
                'mensaje' => "Hay {$vacasNecesitanCelo->count()} vaca(s) que necesitan revisión de celo",
                'cantidad' => $vacasNecesitanCelo->count()
            ];
        }

        // Crías próximas al destete
        $criasProximasDestete = Cria::where('estado_destete', 'No destetada')
            ->whereNotNull('fecha_destete')
            ->where('fecha_destete', '<=', now()->addDays(7))
            ->where('fecha_destete', '>=', now())
            ->count();

        if ($criasProximasDestete > 0) {
            $alertas[] = [
                'tipo' => 'warning',
                'icono' => 'fa-baby',
                'titulo' => 'Crías Próximas al Destete',
                'mensaje' => "Hay {$criasProximasDestete} cría(s) próximas al destete (7 días)",
                'cantidad' => $criasProximasDestete
            ];
        }

        // Alertas de inventario - Stock bajo
        $inventarioService = app(\App\Services\InventarioBodegaService::class);
        $stockBajo = $inventarioService->getStockBajo()->count();
        if ($stockBajo > 0) {
            $alertas[] = [
                'tipo' => 'danger',
                'icono' => 'fa-exclamation-triangle',
                'titulo' => 'Productos con Stock Bajo',
                'mensaje' => "Hay {$stockBajo} producto(s) con stock bajo o agotado",
                'cantidad' => $stockBajo
            ];
        }

        // Alertas de inventario - Próximos a vencer
        $proximosVencer = $inventarioService->getProximosAVencer(30)->count();
        if ($proximosVencer > 0) {
            $alertas[] = [
                'tipo' => 'warning',
                'icono' => 'fa-calendar-times',
                'titulo' => 'Productos Próximos a Vencer',
                'mensaje' => "Hay {$proximosVencer} producto(s) próximos a vencer (30 días)",
                'cantidad' => $proximosVencer
            ];
        }

        // Alertas de inventario - Vencidos
        $vencidos = $inventarioService->getVencidos()->count();
        if ($vencidos > 0) {
            $alertas[] = [
                'tipo' => 'danger',
                'icono' => 'fa-times-circle',
                'titulo' => 'Productos Vencidos',
                'mensaje' => "Hay {$vencidos} producto(s) vencidos que requieren atención",
                'cantidad' => $vencidos
            ];
        }

        return $alertas;
    }
}

