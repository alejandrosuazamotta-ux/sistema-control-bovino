<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\PruebaSanitariaService;
use App\Services\ProduccionLecheraService;
use App\Models\Vaca;
use App\Models\PruebaSanitaria;
use App\Models\ProduccionLechera;
use Illuminate\Support\Facades\Event;

/**
 * Tests CRÍTICOS: Impacto de Pruebas Sanitarias en Producción Lechera
 * 
 * Estos tests validan que las pruebas sanitarias tengan el impacto correcto
 * en la producción lechera según las reglas ganaderas.
 */
class PruebaSanitariaImpactoProduccionTest extends TestCase
{
    use RefreshDatabase;

    protected PruebaSanitariaService $pruebaService;
    protected ProduccionLecheraService $produccionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pruebaService = app(PruebaSanitariaService::class);
        $this->produccionService = app(ProduccionLecheraService::class);
        Event::fake();
    }

    /**
     * Test CRÍTICO: Mastitis positiva bloquea producción futura
     */
    public function test_mastitis_positiva_bloquea_produccion_futura(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);

        // Crear prueba de mastitis positiva
        $prueba = $this->pruebaService->create([
            'id_vaca' => $vaca->id_vaca,
            'tipo_prueba' => 'Mastitis',
            'resultado' => 'Positivo',
            'fecha_prueba' => '2025-01-10',
            'restriccion_ordeño' => true,
        ]);

        // Intentar crear producción después de la prueba
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('restricción de ordeño activa');

        $this->produccionService->create([
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'turno' => 'Mañana',
            'cantidad_leche' => 10.5,
            'destino' => 'Venta',
        ]);
    }

    /**
     * Test CRÍTICO: Mastitis positiva marca producciones existentes como excluidas
     */
    public function test_mastitis_positiva_marca_producciones_existentes_excluidas(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);

        // Crear producciones ANTES de la prueba
        $produccion1 = ProduccionLechera::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-08',
            'excluida_por_sanidad' => false,
        ]);

        $produccion2 = ProduccionLechera::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-12',
            'excluida_por_sanidad' => false,
        ]);

        // Crear prueba de mastitis positiva
        $prueba = $this->pruebaService->create([
            'id_vaca' => $vaca->id_vaca,
            'tipo_prueba' => 'Mastitis',
            'resultado' => 'Positivo',
            'fecha_prueba' => '2025-01-10',
            'restriccion_ordeño' => true,
        ]);

        // Refrescar producciones
        $produccion1->refresh();
        $produccion2->refresh();

        // Verificar que la producción ANTES de la prueba NO está excluida
        $this->assertFalse(
            $produccion1->excluida_por_sanidad,
            'La producción antes de la prueba NO debe estar excluida'
        );

        // Verificar que la producción DESPUÉS de la prueba SÍ está excluida
        $this->assertTrue(
            $produccion2->excluida_por_sanidad,
            'La producción después de la prueba DEBE estar excluida por sanidad'
        );
    }

    /**
     * Test CRÍTICO: Brucelosis positiva inhabilita vaca y bloquea producción
     */
    public function test_brucelosis_positiva_inhabilita_vaca_y_bloquea_produccion(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);

        // Crear prueba de Brucelosis positiva
        $prueba = $this->pruebaService->create([
            'id_vaca' => $vaca->id_vaca,
            'tipo_prueba' => 'Brucelosis',
            'resultado' => 'Positivo',
            'fecha_prueba' => '2025-01-10',
            'inhabilitada' => true,
        ]);

        $prueba->refresh();
        $vaca->refresh();

        // Verificar que la prueba marca la vaca como inhabilitada
        $this->assertTrue($prueba->inhabilitada);

        // Intentar crear producción después de la prueba
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('inhabilitada por prueba sanitaria positiva');

        $this->produccionService->create([
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'turno' => 'Mañana',
            'cantidad_leche' => 10.5,
            'destino' => 'Venta',
        ]);
    }

    /**
     * Test CRÍTICO: Tuberculosis positiva inhabilita vaca y bloquea producción
     */
    public function test_tuberculosis_positiva_inhabilita_vaca_y_bloquea_produccion(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);

        // Crear prueba de Tuberculosis positiva
        $prueba = $this->pruebaService->create([
            'id_vaca' => $vaca->id_vaca,
            'tipo_prueba' => 'Tuberculosis',
            'resultado' => 'Positivo',
            'fecha_prueba' => '2025-01-10',
            'inhabilitada' => true,
        ]);

        $prueba->refresh();
        $vaca->refresh();

        // Verificar que la prueba marca la vaca como inhabilitada
        $this->assertTrue($prueba->inhabilitada);

        // Intentar crear producción después de la prueba
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('inhabilitada por prueba sanitaria positiva');

        $this->produccionService->create([
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'turno' => 'Mañana',
            'cantidad_leche' => 10.5,
            'destino' => 'Venta',
        ]);
    }

    /**
     * Test CRÍTICO: Cambio de resultado Positivo a Negativo quita restricciones
     */
    public function test_cambio_positivo_a_negativo_quita_restricciones(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);

        // Crear prueba de mastitis positiva
        $prueba = $this->pruebaService->create([
            'id_vaca' => $vaca->id_vaca,
            'tipo_prueba' => 'Mastitis',
            'resultado' => 'Positivo',
            'fecha_prueba' => '2025-01-10',
            'restriccion_ordeño' => true,
        ]);

        // Verificar que no se puede crear producción
        $this->expectException(\Exception::class);
        $this->produccionService->create([
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'turno' => 'Mañana',
            'cantidad_leche' => 10.5,
            'destino' => 'Venta',
        ]);

        // Cambiar resultado a Negativo
        $this->pruebaService->update($prueba, [
            'resultado' => 'Negativo',
            'restriccion_ordeño' => false,
        ]);

        $prueba->refresh();

        // Verificar que se quitó la restricción
        $this->assertFalse($prueba->restriccion_ordeño);

        // Ahora SÍ debe permitir crear producción
        $produccion = $this->produccionService->create([
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-20',
            'turno' => 'Mañana',
            'cantidad_leche' => 10.5,
            'destino' => 'Venta',
        ]);

        $this->assertInstanceOf(ProduccionLechera::class, $produccion);
    }

    /**
     * Test CRÍTICO: Generación automática de alertas para mastitis positiva
     */
    public function test_genera_alerta_automatica_para_mastitis_positiva(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);

        // Crear prueba de mastitis positiva
        $prueba = $this->pruebaService->create([
            'id_vaca' => $vaca->id_vaca,
            'tipo_prueba' => 'Mastitis',
            'resultado' => 'Positivo',
            'fecha_prueba' => '2025-01-10',
            'restriccion_ordeño' => true,
            'severidad' => 'Moderada',
        ]);

        // Verificar que se generó una alerta
        $alerta = \App\Models\Notificacion::where('tipo', 'mastitis')
            ->where('entidad_tipo', PruebaSanitaria::class)
            ->where('entidad_id', $prueba->id_prueba)
            ->first();

        $this->assertNotNull($alerta, 'Debe generarse una alerta automática para mastitis positiva');
        $this->assertEquals('advertencia', $alerta->nivel);
        $this->assertStringContainsString('Mastitis detectada', $alerta->titulo);
    }

    /**
     * Test CRÍTICO: Restricción automática según resultado
     */
    public function test_restriccion_automatica_segun_resultado(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);

        // Crear prueba de mastitis positiva (debe activar restricción automáticamente)
        $prueba = $this->pruebaService->create([
            'id_vaca' => $vaca->id_vaca,
            'tipo_prueba' => 'Mastitis',
            'resultado' => 'Positivo',
            'fecha_prueba' => '2025-01-10',
        ]);

        $prueba->refresh();

        // Verificar que la restricción se activó automáticamente
        $this->assertTrue(
            $prueba->restriccion_ordeño,
            'La restricción de ordeño debe activarse automáticamente para mastitis positiva'
        );
    }
}

