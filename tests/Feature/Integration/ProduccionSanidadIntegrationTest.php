<?php

namespace Tests\Feature\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Vaca;
use App\Models\ProduccionLechera;
use App\Models\Salud;
use App\Services\ProduccionLecheraService;
use App\Services\SaludService;
use Carbon\Carbon;

class ProduccionSanidadIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Producción con mastitis positiva se marca como excluida por sanidad
     */
    public function test_produccion_con_mastitis_positiva_se_marca_como_excluida_por_sanidad(): void
    {
        $vaca = Vaca::factory()->create();
        $saludService = app(SaludService::class);
        $produccionService = app(ProduccionLecheraService::class);

        // Crear prueba de mastitis positiva
        $salud = $saludService->create([
            'id_vaca' => $vaca->id_vaca,
            'tipo_registro' => 'Prueba mastitis',
            'tipo_prueba' => 'Mastitis',
            'resultado' => 'Positivo',
            'fecha' => '2025-01-12',
        ]);

        // Crear producción después de la prueba
        $produccion = $produccionService->create([
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'turno' => 'Mañana',
            'cantidad_leche' => 10.5,
            'destino' => 'Venta',
        ]);

        $produccion->refresh();

        // Verificar que la producción está excluida por sanidad
        $this->assertTrue($produccion->excluida_por_sanidad);
    }
}

