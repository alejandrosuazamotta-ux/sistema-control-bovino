<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\SaludService;
use App\Models\Vaca;
use App\Models\Salud;
use App\Models\ProduccionLechera;
use Carbon\Carbon;

class SaludServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SaludService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SaludService::class);
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
            'tipo_registro' => 'Prueba mastitis',
            'tipo_prueba' => 'Mastitis',
            'resultado' => 'Positivo',
            'fecha' => '2025-01-12',
        ];

        $salud = $this->service->create($data);
        $salud->refresh();
        $vaca->refresh();

        // Verificar que la vaca tiene restricción de ordeño
        $this->assertTrue($salud->restriccion_ordeño);

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
     * Test: Mastitis negativa quita restricción y exclusiones
     */
    public function test_mastitis_negativa_quita_restriccion(): void
    {
        $vaca = Vaca::factory()->create();
        
        // Crear prueba positiva primero
        $saludPositiva = Salud::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'tipo_registro' => 'Prueba mastitis',
            'tipo_prueba' => 'Mastitis',
            'resultado' => 'Positivo',
            'restriccion_ordeño' => true,
            'fecha' => '2025-01-12',
        ]);

        // Crear producción excluida
        ProduccionLechera::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'excluida_por_sanidad' => true,
        ]);

        // Actualizar a negativo
        $data = [
            'resultado' => 'Negativo',
        ];

        $this->service->update($saludPositiva, $data);
        $saludPositiva->refresh();

        // Verificar que se quitó la restricción
        $this->assertFalse($saludPositiva->restriccion_ordeño);
    }
}

