<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\AlertaService;
use App\Models\Vaca;
use App\Models\Notificacion;
use App\Models\RegistroReproductivo;
use App\Models\Retiro;
use App\Models\InventarioBodega;
use Carbon\Carbon;

class AlertaServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AlertaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AlertaService::class);
    }

    /**
     * Test: Generar alerta de preparto
     */
    public function test_genera_alerta_preparto(): void
    {
        $vaca = Vaca::factory()->create();
        
        // Crear registro reproductivo con fecha probable de parto en 5 días
        $fechaParto = Carbon::now()->addDays(5);
        $registro = RegistroReproductivo::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'tipo_evento' => 'Inseminación',
            'fecha_probable_parto' => $fechaParto,
            'estado_reproductivo' => 'Preñada',
        ]);

        $cantidad = $this->service->generarAlertasPreparto(7);

        // Verificar que se generó al menos una alerta
        $this->assertGreaterThan(0, $cantidad);
        
        // Verificar que existe la notificación
        $notificacion = Notificacion::where('tipo', 'preparto')
            ->where('entidad_id', $registro->id_registro)
            ->first();
        
        $this->assertNotNull($notificacion);
        $this->assertEquals('urgente', $notificacion->nivel);
    }

    /**
     * Test: Generar alerta de retiros activos
     */
    public function test_genera_alerta_retiros_activos(): void
    {
        $vaca = Vaca::factory()->create();
        
        // Crear retiro activo
        Retiro::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'tipo_retiro' => 'Ordeño',
            'fecha_inicio' => Carbon::now()->subDays(5),
            'fecha_fin' => Carbon::now()->addDays(5),
            'activo' => true,
        ]);

        $cantidad = $this->service->generarAlertasRetirosActivos();

        // Verificar que se generó al menos una alerta
        $this->assertGreaterThan(0, $cantidad);
        
        // Verificar que existe la notificación
        $notificacion = Notificacion::where('tipo', 'retiro_activo')
            ->where('entidad_tipo', Retiro::class)
            ->first();
        
        $this->assertNotNull($notificacion);
    }

    /**
     * Test: Generar alerta de stock bajo
     */
    public function test_genera_alerta_stock_bajo(): void
    {
        // Crear inventario con stock bajo
        InventarioBodega::factory()->create([
            'stock_actual' => 5,
            'stock_minimo' => 10,
        ]);

        $cantidad = $this->service->generarAlertasStockBajo();

        // Verificar que se generó al menos una alerta
        $this->assertGreaterThan(0, $cantidad);
        
        // Verificar que existe la notificación
        $notificacion = Notificacion::where('tipo', 'stock_bajo')
            ->where('entidad_tipo', InventarioBodega::class)
            ->first();
        
        $this->assertNotNull($notificacion);
        $this->assertEquals('advertencia', $notificacion->nivel);
    }

    /**
     * Test: Generar todas las alertas
     */
    public function test_genera_todas_las_alertas(): void
    {
        $vaca = Vaca::factory()->create();
        
        // Crear datos para generar alertas
        RegistroReproductivo::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'fecha_probable_parto' => Carbon::now()->addDays(5),
            'estado_reproductivo' => 'Preñada',
        ]);

        Retiro::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'activo' => true,
        ]);

        InventarioBodega::factory()->create([
            'stock_actual' => 5,
            'stock_minimo' => 10,
        ]);

        $resultado = $this->service->generarTodasLasAlertas();

        // Verificar que se generaron alertas
        $this->assertIsArray($resultado);
        $this->assertGreaterThan(0, array_sum($resultado));
    }
}

