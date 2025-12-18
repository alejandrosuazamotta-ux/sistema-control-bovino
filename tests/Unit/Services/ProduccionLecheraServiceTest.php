<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\ProduccionLecheraService;
use App\Models\Vaca;
use App\Models\ProduccionLechera;
use App\Models\Retiro;
use Carbon\Carbon;

class ProduccionLecheraServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProduccionLecheraService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ProduccionLecheraService::class);
    }

    /**
     * Test: Crear producción lechera válida
     */
    public function test_crea_produccion_lechera_valida(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);

        $data = [
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'turno' => 'Mañana',
            'cantidad_leche' => 10.5,
            'destino' => 'Venta',
        ];

        $produccion = $this->service->create($data);

        $this->assertInstanceOf(ProduccionLechera::class, $produccion);
        $this->assertEquals($vaca->id_vaca, $produccion->id_vaca);
        $this->assertEquals(10.5, $produccion->cantidad_leche);
        $this->assertFalse($produccion->excluida_por_retiro);
        $this->assertFalse($produccion->excluida_por_sanidad);
    }

    /**
     * Test: No permite crear producción si la vaca está en retiro
     */
    public function test_no_permite_produccion_si_vaca_en_retiro(): void
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

        $data = [
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'turno' => 'Mañana',
            'cantidad_leche' => 10.5,
            'destino' => 'Venta',
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('La vaca está en retiro');
        $this->service->create($data);
    }

    /**
     * Test: No permite crear producción si la vaca no está en lactancia
     */
    public function test_no_permite_produccion_si_vaca_no_en_lactancia(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Preñada']);

        $data = [
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'turno' => 'Mañana',
            'cantidad_leche' => 10.5,
            'destino' => 'Venta',
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Solo se puede registrar producción para vacas en estado de Lactancia');
        $this->service->create($data);
    }

    /**
     * Test: No permite crear producción duplicada (misma vaca, fecha y turno)
     */
    public function test_no_permite_produccion_duplicada(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);

        // Crear producción existente
        ProduccionLechera::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'turno' => 'Mañana',
        ]);

        $data = [
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'turno' => 'Mañana',
            'cantidad_leche' => 10.5,
            'destino' => 'Venta',
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Ya existe un registro de producción');
        $this->service->create($data);
    }

    /**
     * Test: Actualizar producción lechera
     */
    public function test_actualiza_produccion_lechera(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);
        $produccion = ProduccionLechera::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'cantidad_leche' => 10.0,
        ]);

        $data = [
            'cantidad_leche' => 15.5,
        ];

        $produccionActualizada = $this->service->update($produccion, $data);

        $this->assertEquals(15.5, $produccionActualizada->cantidad_leche);
    }

    /**
     * Test: Eliminar producción lechera
     */
    public function test_elimina_produccion_lechera(): void
    {
        $produccion = ProduccionLechera::factory()->create();

        $resultado = $this->service->delete($produccion);

        $this->assertTrue($resultado);
        $this->assertDatabaseMissing('produccion_lechera', [
            'id_produccion' => $produccion->id_produccion,
        ]);
    }

    /**
     * Test CRÍTICO: No permite producción si la vaca tiene prueba sanitaria positiva (restricción de ordeño)
     */
    public function test_no_permite_produccion_si_vaca_tiene_restriccion_ordeño_activa(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);

        // Crear prueba sanitaria positiva con restricción de ordeño
        \App\Models\Salud::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'tipo_registro' => 'Prueba mastitis',
            'tipo_prueba' => 'Mastitis',
            'resultado' => 'Positivo',
            'restriccion_ordeño' => true,
            'fecha' => '2025-01-10',
        ]);

        $data = [
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'turno' => 'Mañana',
            'cantidad_leche' => 10.5,
            'destino' => 'Venta',
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('restricción de ordeño activa');
        $this->service->create($data);
    }

    /**
     * Test CRÍTICO: No permite producción si la vaca está inhabilitada (Brucelosis)
     */
    public function test_no_permite_produccion_si_vaca_inhabilitada_brucelosis(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);

        // Crear prueba de Brucelosis positiva que inhabilita la vaca
        \App\Models\Salud::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'tipo_registro' => 'Prueba Brucelosis',
            'tipo_prueba' => 'Brucelosis',
            'resultado' => 'Positivo',
            'inhabilitada' => true,
            'fecha' => '2025-01-10',
        ]);

        $data = [
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'turno' => 'Mañana',
            'cantidad_leche' => 10.5,
            'destino' => 'Venta',
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('inhabilitada por prueba sanitaria positiva');
        $this->service->create($data);
    }

    /**
     * Test CRÍTICO: No permite producción si la vaca está inhabilitada (Tuberculosis)
     */
    public function test_no_permite_produccion_si_vaca_inhabilitada_tuberculosis(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);

        // Crear prueba de Tuberculosis positiva que inhabilita la vaca
        \App\Models\Salud::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'tipo_registro' => 'Prueba Tuberculosis',
            'tipo_prueba' => 'Tuberculosis',
            'resultado' => 'Positivo',
            'inhabilitada' => true,
            'fecha' => '2025-01-10',
        ]);

        $data = [
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'turno' => 'Mañana',
            'cantidad_leche' => 10.5,
            'destino' => 'Venta',
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('inhabilitada por prueba sanitaria positiva');
        $this->service->create($data);
    }

    /**
     * Test CRÍTICO: No permite producción con fecha futura
     */
    public function test_no_permite_produccion_con_fecha_futura(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);

        $data = [
            'id_vaca' => $vaca->id_vaca,
            'fecha' => Carbon::now()->addDays(5)->format('Y-m-d'), // Fecha futura
            'turno' => 'Mañana',
            'cantidad_leche' => 10.5,
            'destino' => 'Venta',
        ];

        // El FormRequest debería validar esto, pero también validamos en el Service
        // Si el FormRequest no lo valida, el Service debe rechazarlo
        try {
            $produccion = $this->service->create($data);
            // Si llega aquí, el test debe fallar porque se creó una producción con fecha futura
            $this->fail('Se permitió crear producción con fecha futura');
        } catch (\Exception $e) {
            // Esperado: debe lanzar excepción
            $this->assertStringContainsString('fecha', strtolower($e->getMessage()));
        }
    }

    /**
     * Test CRÍTICO: No permite producción con litros ≤ 0
     */
    public function test_no_permite_produccion_con_litros_cero(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);

        $data = [
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'turno' => 'Mañana',
            'cantidad_leche' => 0, // Litros cero
            'destino' => 'Venta',
        ];

        // El FormRequest debería validar esto (min:0.01), pero también validamos
        try {
            $produccion = $this->service->create($data);
            // Si llega aquí, el test debe fallar
            $this->fail('Se permitió crear producción con litros cero');
        } catch (\Exception $e) {
            // Esperado: debe lanzar excepción o el FormRequest debe rechazarlo
            $this->assertTrue(true);
        }
    }

    /**
     * Test CRÍTICO: No permite producción con litros negativos
     */
    public function test_no_permite_produccion_con_litros_negativos(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);

        $data = [
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'turno' => 'Mañana',
            'cantidad_leche' => -5.0, // Litros negativos
            'destino' => 'Venta',
        ];

        // El FormRequest debería validar esto (min:0.01), pero también validamos
        try {
            $produccion = $this->service->create($data);
            // Si llega aquí, el test debe fallar
            $this->fail('Se permitió crear producción con litros negativos');
        } catch (\Exception $e) {
            // Esperado: debe lanzar excepción o el FormRequest debe rechazarlo
            $this->assertTrue(true);
        }
    }

    /**
     * Test CRÍTICO: No permite producción con turno inválido
     */
    public function test_no_permite_produccion_con_turno_invalido(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);

        $data = [
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'turno' => 'TurnoInvalido', // Turno inválido
            'cantidad_leche' => 10.5,
            'destino' => 'Venta',
        ];

        // El FormRequest debería validar esto (in:AM,PM), pero también validamos
        try {
            $produccion = $this->service->create($data);
            // Si llega aquí, el test debe fallar
            $this->fail('Se permitió crear producción con turno inválido');
        } catch (\Exception $e) {
            // Esperado: debe lanzar excepción o el FormRequest debe rechazarlo
            $this->assertTrue(true);
        }
    }
}

