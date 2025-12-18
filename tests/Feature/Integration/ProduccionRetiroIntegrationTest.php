<?php

namespace Tests\Feature\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Vaca;
use App\Models\ProduccionLechera;
use App\Models\Retiro;
use App\Services\ProduccionLecheraService;
use App\Services\RetiroService;
use Carbon\Carbon;

class ProduccionRetiroIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Producción con retiro activo se marca como excluida
     */
    public function test_produccion_con_retiro_activo_se_marca_como_excluida(): void
    {
        $vaca = Vaca::factory()->create();
        $retiroService = app(RetiroService::class);
        $produccionService = app(ProduccionLecheraService::class);

        // Crear retiro
        $retiro = $retiroService->create([
            'id_vaca' => $vaca->id_vaca,
            'tipo_retiro' => 'Ordeño',
            'fecha_inicio' => '2025-01-10',
            'fecha_fin' => '2025-01-20',
            'motivo' => 'Test integración',
        ]);

        // Crear producción dentro del período de retiro
        $produccion = $produccionService->create([
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'turno' => 'Mañana',
            'cantidad_leche' => 10.5,
            'destino' => 'Venta',
        ]);

        $produccion->refresh();

        // Verificar que la producción está excluida
        $this->assertTrue($produccion->excluida_por_retiro);
    }

    /**
     * Test: Producción sin retiro activo NO se marca como excluida
     */
    public function test_produccion_sin_retiro_activo_no_se_marca_como_excluida(): void
    {
        $vaca = Vaca::factory()->create();
        $produccionService = app(ProduccionLecheraService::class);

        // Crear producción sin retiro
        $produccion = $produccionService->create([
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'turno' => 'Mañana',
            'cantidad_leche' => 10.5,
            'destino' => 'Venta',
        ]);

        $produccion->refresh();

        // Verificar que la producción NO está excluida
        $this->assertFalse($produccion->excluida_por_retiro);
    }
}

