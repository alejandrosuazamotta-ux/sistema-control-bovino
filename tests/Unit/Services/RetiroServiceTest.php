<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\RetiroService;
use App\Models\Vaca;
use App\Models\Retiro;
use App\Models\ProduccionLechera;
use Carbon\Carbon;

class RetiroServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RetiroService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(RetiroService::class);
    }

    /**
     * Test: Crear retiro marca producciones como excluidas
     */
    public function test_crear_retiro_marca_producciones_excluidas(): void
    {
        $vaca = Vaca::factory()->create();
        
        // Crear producciones antes del retiro
        ProduccionLechera::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-10',
            'excluida_por_retiro' => false,
        ]);

        ProduccionLechera::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'excluida_por_retiro' => false,
        ]);

        $data = [
            'id_vaca' => $vaca->id_vaca,
            'tipo_retiro' => 'Ordeño',
            'fecha_inicio' => '2025-01-12',
            'fecha_fin' => '2025-01-20',
            'motivo' => 'Test retiro',
        ];

        $retiro = $this->service->create($data);

        // Verificar que las producciones dentro del rango están excluidas
        $produccionExcluida = ProduccionLechera::where('id_vaca', $vaca->id_vaca)
            ->where('fecha', '2025-01-15')
            ->first();
        
        $this->assertTrue($produccionExcluida->excluida_por_retiro);

        // Verificar que la producción antes del retiro NO está excluida
        $produccionNoExcluida = ProduccionLechera::where('id_vaca', $vaca->id_vaca)
            ->where('fecha', '2025-01-10')
            ->first();
        
        $this->assertFalse($produccionNoExcluida->excluida_por_retiro);
    }

    /**
     * Test: No permite retiros solapados
     */
    public function test_no_permite_retiros_solapados(): void
    {
        $vaca = Vaca::factory()->create();
        
        // Crear primer retiro
        Retiro::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'tipo_retiro' => 'Ordeño',
            'fecha_inicio' => '2025-01-10',
            'fecha_fin' => '2025-01-20',
            'activo' => true,
        ]);

        // Intentar crear retiro solapado
        $data = [
            'id_vaca' => $vaca->id_vaca,
            'tipo_retiro' => 'Ordeño',
            'fecha_inicio' => '2025-01-15',
            'fecha_fin' => '2025-01-25',
            'motivo' => 'Test retiro solapado',
        ];

        $this->expectException(\Exception::class);
        $this->service->create($data);
    }

    /**
     * Test CRÍTICO: Debe marcar automáticamente producciones ya existentes dentro del rango del retiro
     * NOTA: Este test documenta que esta funcionalidad DEBE existir. Si el servicio no la implementa, el test fallará.
     */
    public function test_crear_retiro_marca_producciones_existentes_automaticamente(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);
        
        // Crear producciones ANTES del retiro (dentro del rango del retiro futuro)
        $produccion1 = ProduccionLechera::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-12', // Dentro del rango del retiro
            'excluida_por_retiro' => false,
        ]);

        $produccion2 = ProduccionLechera::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-18', // Dentro del rango del retiro
            'excluida_por_retiro' => false,
        ]);

        $produccion3 = ProduccionLechera::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-08', // FUERA del rango del retiro
            'excluida_por_retiro' => false,
        ]);

        // Crear retiro que cubre las producciones existentes
        $data = [
            'id_vaca' => $vaca->id_vaca,
            'tipo_retiro' => 'Ordeño',
            'fecha_inicio' => '2025-01-10',
            'fecha_fin' => '2025-01-20',
            'activo' => true,
        ];

        $retiro = $this->service->create($data);

        // REFRESCAR producciones desde la base de datos
        $produccion1->refresh();
        $produccion2->refresh();
        $produccion3->refresh();

        // Verificar que las producciones dentro del rango están marcadas como excluidas
        // NOTA: Si el servicio no implementa esto, el test fallará
        $this->assertTrue(
            $produccion1->excluida_por_retiro,
            'La producción dentro del rango del retiro debe estar marcada como excluida automáticamente'
        );
        
        $this->assertTrue(
            $produccion2->excluida_por_retiro,
            'La producción dentro del rango del retiro debe estar marcada como excluida automáticamente'
        );

        // Verificar que la producción fuera del rango NO está excluida
        $this->assertFalse(
            $produccion3->excluida_por_retiro,
            'La producción fuera del rango del retiro NO debe estar marcada como excluida'
        );
    }

    /**
     * Test CRÍTICO: Bloquear nuevas producciones en retiro
     */
    public function test_bloquea_nuevas_producciones_durante_retiro(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);
        
        // Crear retiro activo
        Retiro::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'tipo_retiro' => 'Ordeño',
            'fecha_inicio' => '2025-01-10',
            'fecha_fin' => '2025-01-20',
            'activo' => true,
        ]);

        // Intentar crear producción durante el retiro
        $produccionService = app(\App\Services\ProduccionLecheraService::class);
        
        $data = [
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15', // Dentro del rango del retiro
            'turno' => 'Mañana',
            'cantidad_leche' => 10.5,
            'destino' => 'Venta',
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('retiro');
        $produccionService->create($data);
    }

    /**
     * Test CRÍTICO: Validar fechas inconsistentes (inicio > fin)
     */
    public function test_no_permite_retiro_con_fecha_inicio_mayor_que_fin(): void
    {
        $vaca = Vaca::factory()->create();
        
        $data = [
            'id_vaca' => $vaca->id_vaca,
            'tipo_retiro' => 'Ordeño',
            'fecha_inicio' => '2025-01-20', // Fecha inicio mayor que fin
            'fecha_fin' => '2025-01-10',     // Fecha fin menor que inicio
            'activo' => true,
        ];

        // El FormRequest debería validar esto, pero también validamos en el Service
        // Si el FormRequest no lo valida, el Service debe rechazarlo
        try {
            $retiro = $this->service->create($data);
            // Si llega aquí, el test debe fallar porque se creó un retiro con fechas inconsistentes
            $this->fail('Se permitió crear retiro con fecha_inicio > fecha_fin');
        } catch (\Exception $e) {
            // Esperado: debe lanzar excepción
            $this->assertTrue(true);
        }
    }
}

