<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\RegistroReproductivoService;
use App\Models\Vaca;
use App\Models\RegistroReproductivo;
use App\Repositories\RegistroReproductivoRepository;
use Carbon\Carbon;

class RegistroReproductivoServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RegistroReproductivoService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(RegistroReproductivoService::class);
    }

    /**
     * Test: Calcular fecha probable de parto correctamente
     */
    public function test_calcula_fecha_probable_parto_correctamente(): void
    {
        $vaca = Vaca::factory()->create();
        
        $data = [
            'id_vaca' => $vaca->id_vaca,
            'tipo_registro' => 'Inseminación',
            'fecha_registro' => '2025-01-15',
            'estado_reproductivo' => 'Preñada',
        ];

        $registro = $this->service->create($data);
        $registro->refresh();

        // La fecha probable de parto debe ser 280 días después (9 meses y 10 días)
        $fechaEsperada = Carbon::parse('2025-01-15')->addDays(280);
        $this->assertEquals(
            $fechaEsperada->format('Y-m-d'),
            $registro->fecha_probable_parto->format('Y-m-d')
        );
    }

    /**
     * Test: No calcular fecha probable si no es inseminación
     */
    public function test_no_calcula_fecha_probable_si_no_es_inseminacion(): void
    {
        $vaca = Vaca::factory()->create();
        
        $data = [
            'id_vaca' => $vaca->id_vaca,
            'tipo_registro' => 'Celo',
            'fecha_registro' => '2025-01-15',
            'estado_reproductivo' => 'En celo',
        ];

        $registro = $this->service->create($data);
        $registro->refresh();

        $this->assertNull($registro->fecha_probable_parto);
    }
}

