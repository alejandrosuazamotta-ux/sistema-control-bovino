<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\PruebaSanitariaService;
use App\Models\Vaca;
use App\Models\PruebaSanitaria;
use App\Models\ProduccionLechera;
use Illuminate\Support\Facades\Event;

class PruebaSanitariaServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PruebaSanitariaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PruebaSanitariaService::class);
        Event::fake();
    }

    /**
     * Test: Crear prueba sanitaria válida
     */
    public function test_crea_prueba_sanitaria_valida(): void
    {
        $vaca = Vaca::factory()->create();

        $data = [
            'id_vaca' => $vaca->id_vaca,
            'tipo_prueba' => 'Mastitis',
            'resultado' => 'Negativo',
            'fecha_prueba' => '2025-01-15',
        ];

        $prueba = $this->service->create($data);

        $this->assertInstanceOf(PruebaSanitaria::class, $prueba);
        $this->assertEquals($vaca->id_vaca, $prueba->id_vaca);
        $this->assertEquals('Mastitis', $prueba->tipo_prueba);
        $this->assertEquals('Negativo', $prueba->resultado);
    }

    /**
     * Test: Mastitis positiva bloquea ordeño y marca producciones como excluidas
     */
    public function test_mastitis_positiva_bloquea_ordeno_y_excluye_producciones(): void
    {
        $vaca = Vaca::factory()->create();

        // Crear producciones antes de la prueba
        ProduccionLechera::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-10',
            'excluida_por_sanidad' => false,
        ]);

        ProduccionLechera::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'excluida_por_sanidad' => false,
        ]);

        $data = [
            'id_vaca' => $vaca->id_vaca,
            'tipo_prueba' => 'Mastitis',
            'resultado' => 'Positivo',
            'fecha_prueba' => '2025-01-12',
            'restriccion_ordeño' => true,
        ];

        $prueba = $this->service->create($data);
        $prueba->refresh();
        $vaca->refresh();

        // Verificar que la prueba tiene restricción de ordeño
        $this->assertTrue($prueba->restriccion_ordeño);

        // Verificar que las producciones desde la fecha de la prueba están excluidas
        $produccionExcluida = ProduccionLechera::where('id_vaca', $vaca->id_vaca)
            ->where('fecha', '2025-01-15')
            ->first();
        
        $this->assertTrue($produccionExcluida->excluida_por_sanidad);

        // Verificar que la producción antes de la prueba NO está excluida
        $produccionNoExcluida = ProduccionLechera::where('id_vaca', $vaca->id_vaca)
            ->where('fecha', '2025-01-10')
            ->first();
        
        $this->assertFalse($produccionNoExcluida->excluida_por_sanidad);
    }

    /**
     * Test: Brucelosis positiva inhabilita vaca
     */
    public function test_brucelosis_positiva_inhabilita_vaca(): void
    {
        $vaca = Vaca::factory()->create();

        $data = [
            'id_vaca' => $vaca->id_vaca,
            'tipo_prueba' => 'Brucelosis',
            'resultado' => 'Positivo',
            'fecha_prueba' => '2025-01-15',
            'inhabilitada' => true,
        ];

        $prueba = $this->service->create($data);
        $prueba->refresh();

        // Verificar que la prueba marca la vaca como inhabilitada
        $this->assertTrue($prueba->inhabilitada);
    }

    /**
     * Test: No permite editar prueba cerrada
     */
    public function test_no_permite_editar_prueba_cerrada(): void
    {
        $vaca = Vaca::factory()->create();
        $prueba = PruebaSanitaria::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'cerrada' => true,
        ]);

        $data = [
            'resultado' => 'Negativo',
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No se puede editar una prueba sanitaria cerrada');
        $this->service->update($prueba, $data);
    }

    /**
     * Test: Eliminar prueba sanitaria quita restricciones
     */
    public function test_eliminar_prueba_sanitaria_quita_restricciones(): void
    {
        $vaca = Vaca::factory()->create();
        
        // Crear producción excluida
        $produccion = ProduccionLechera::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'excluida_por_sanidad' => true,
        ]);

        // Crear prueba positiva
        $prueba = PruebaSanitaria::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'tipo_prueba' => 'Mastitis',
            'resultado' => 'Positivo',
            'fecha_prueba' => '2025-01-12',
            'restriccion_ordeño' => true,
        ]);

        // Eliminar la prueba
        $this->service->delete($prueba);

        // Verificar que la producción ya no está excluida
        $produccion->refresh();
        $this->assertFalse($produccion->excluida_por_sanidad);
    }

    /**
     * Test CRÍTICO: Impacto real en Producción Lechera - Mastitis positiva bloquea nuevas producciones
     */
    public function test_mastitis_positiva_bloquea_nuevas_producciones(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);

        // Crear prueba de Mastitis positiva
        $prueba = $this->service->create([
            'id_vaca' => $vaca->id_vaca,
            'tipo_prueba' => 'Mastitis',
            'resultado' => 'Positivo',
            'fecha_prueba' => '2025-01-12',
            'restriccion_ordeño' => true,
        ]);

        // Intentar crear producción después de la prueba
        $produccionService = app(\App\Services\ProduccionLecheraService::class);
        
        $data = [
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'turno' => 'Mañana',
            'cantidad_leche' => 10.5,
            'destino' => 'Venta',
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('restricción de ordeño activa');
        $produccionService->create($data);
    }

    /**
     * Test CRÍTICO: Impacto real en Producción Lechera - Brucelosis positiva bloquea nuevas producciones
     */
    public function test_brucelosis_positiva_bloquea_nuevas_producciones(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);

        // Crear prueba de Brucelosis positiva
        $prueba = $this->service->create([
            'id_vaca' => $vaca->id_vaca,
            'tipo_prueba' => 'Brucelosis',
            'resultado' => 'Positivo',
            'fecha_prueba' => '2025-01-12',
            'inhabilitada' => true,
        ]);

        // Intentar crear producción después de la prueba
        $produccionService = app(\App\Services\ProduccionLecheraService::class);
        
        $data = [
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'turno' => 'Mañana',
            'cantidad_leche' => 10.5,
            'destino' => 'Venta',
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('inhabilitada por prueba sanitaria positiva');
        $produccionService->create($data);
    }

    /**
     * Test CRÍTICO: Impacto real en Producción Lechera - Tuberculosis positiva bloquea nuevas producciones
     */
    public function test_tuberculosis_positiva_bloquea_nuevas_producciones(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);

        // Crear prueba de Tuberculosis positiva
        $prueba = $this->service->create([
            'id_vaca' => $vaca->id_vaca,
            'tipo_prueba' => 'Tuberculosis',
            'resultado' => 'Positivo',
            'fecha_prueba' => '2025-01-12',
            'inhabilitada' => true,
        ]);

        // Intentar crear producción después de la prueba
        $produccionService = app(\App\Services\ProduccionLecheraService::class);
        
        $data = [
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'turno' => 'Mañana',
            'cantidad_leche' => 10.5,
            'destino' => 'Venta',
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('inhabilitada por prueba sanitaria positiva');
        $produccionService->create($data);
    }

    /**
     * Test CRÍTICO: Restricción automática según resultado - Mastitis positiva aplica restricción automáticamente
     */
    public function test_mastitis_positiva_aplica_restriccion_automaticamente(): void
    {
        $vaca = Vaca::factory()->create();

        $data = [
            'id_vaca' => $vaca->id_vaca,
            'tipo_prueba' => 'Mastitis',
            'resultado' => 'Positivo',
            'fecha_prueba' => '2025-01-15',
            // NO especificar restriccion_ordeño, debe aplicarse automáticamente
        ];

        $prueba = $this->service->create($data);
        $prueba->refresh();

        // Verificar que la restricción se aplicó automáticamente
        $this->assertTrue(
            $prueba->restriccion_ordeño,
            'La restricción de ordeño debe aplicarse automáticamente para Mastitis positiva'
        );
    }

    /**
     * Test CRÍTICO: Restricción automática según resultado - Brucelosis positiva inhabilita automáticamente
     */
    public function test_brucelosis_positiva_inhabilita_automaticamente(): void
    {
        $vaca = Vaca::factory()->create();

        $data = [
            'id_vaca' => $vaca->id_vaca,
            'tipo_prueba' => 'Brucelosis',
            'resultado' => 'Positivo',
            'fecha_prueba' => '2025-01-15',
            // NO especificar inhabilitada, debe aplicarse automáticamente
        ];

        $prueba = $this->service->create($data);
        $prueba->refresh();

        // Verificar que la inhabilitación se aplicó automáticamente
        $this->assertTrue(
            $prueba->inhabilitada,
            'La inhabilitación debe aplicarse automáticamente para Brucelosis positiva'
        );
    }

    /**
     * Test CRÍTICO: Generación de alertas - Mastitis positiva genera alerta
     */
    public function test_mastitis_positiva_genera_alerta(): void
    {
        $vaca = Vaca::factory()->create();

        $data = [
            'id_vaca' => $vaca->id_vaca,
            'tipo_prueba' => 'Mastitis',
            'resultado' => 'Positivo',
            'fecha_prueba' => '2025-01-15',
            'restriccion_ordeño' => true,
            'severidad' => 'Moderada',
        ];

        $prueba = $this->service->create($data);

        // Verificar que se generó una alerta
        $alerta = \App\Models\Notificacion::where('tipo', 'mastitis')
            ->where('entidad_tipo', PruebaSanitaria::class)
            ->where('entidad_id', $prueba->id_prueba)
            ->first();

        $this->assertNotNull($alerta, 'Debe generarse una alerta para Mastitis positiva');
        $this->assertEquals('urgente', $alerta->nivel);
        $this->assertStringContainsString($vaca->codigo, $alerta->titulo);
    }

    /**
     * Test CRÍTICO: Generación de alertas - Brucelosis positiva genera alerta urgente
     */
    public function test_brucelosis_positiva_genera_alerta_urgente(): void
    {
        $vaca = Vaca::factory()->create();

        $data = [
            'id_vaca' => $vaca->id_vaca,
            'tipo_prueba' => 'Brucelosis',
            'resultado' => 'Positivo',
            'fecha_prueba' => '2025-01-15',
            'inhabilitada' => true,
        ];

        $prueba = $this->service->create($data);

        // Verificar que se generó una alerta
        $alerta = \App\Models\Notificacion::where('tipo', 'enfermedad_grave')
            ->where('entidad_tipo', Vaca::class)
            ->where('entidad_id', $vaca->id_vaca)
            ->first();

        $this->assertNotNull($alerta, 'Debe generarse una alerta urgente para Brucelosis positiva');
        $this->assertEquals('urgente', $alerta->nivel);
        $this->assertStringContainsString('INHABILITADA', $alerta->titulo);
    }
}

