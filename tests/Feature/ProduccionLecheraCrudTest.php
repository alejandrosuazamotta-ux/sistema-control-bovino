<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Vaca;
use App\Models\ProduccionLechera;
use Illuminate\Support\Facades\Gate;

class ProduccionLecheraCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear usuario admin
        $this->user = User::factory()->create();
        $this->user->assignRole('admin');
        
        $this->actingAs($this->user);
    }

    /**
     * Test: Listar producciones lecheras
     */
    public function test_lista_producciones_lecheras(): void
    {
        ProduccionLechera::factory()->count(5)->create();

        $response = $this->get(route('admin.produccion-lechera.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.produccion_lechera.index');
    }

    /**
     * Test: Crear producción lechera
     */
    public function test_crea_produccion_lechera(): void
    {
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);

        $data = [
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'turno' => 'Mañana',
            'cantidad_leche' => 10.5,
            'destino' => 'Venta',
        ];

        $response = $this->post(route('admin.produccion-lechera.store'), $data);

        $response->assertRedirect(route('admin.produccion-lechera.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('produccion_lechera', [
            'id_vaca' => $vaca->id_vaca,
            'fecha' => '2025-01-15',
            'turno' => 'Mañana',
        ]);
    }

    /**
     * Test: Ver detalle de producción lechera
     */
    public function test_ve_detalle_produccion_lechera(): void
    {
        $produccion = ProduccionLechera::factory()->create();

        $response = $this->get(route('admin.produccion-lechera.show', $produccion->id_produccion));

        $response->assertStatus(200);
        $response->assertViewIs('admin.produccion_lechera.show');
        $response->assertViewHas('produccion');
    }

    /**
     * Test: Actualizar producción lechera
     */
    public function test_actualiza_produccion_lechera(): void
    {
        $produccion = ProduccionLechera::factory()->create();

        $data = [
            'cantidad_leche' => 15.5,
        ];

        $response = $this->put(route('admin.produccion-lechera.update', $produccion->id_produccion), $data);

        $response->assertRedirect(route('admin.produccion-lechera.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('produccion_lechera', [
            'id_produccion' => $produccion->id_produccion,
            'cantidad_leche' => 15.5,
        ]);
    }

    /**
     * Test: Eliminar producción lechera
     */
    public function test_elimina_produccion_lechera(): void
    {
        $produccion = ProduccionLechera::factory()->create();

        $response = $this->delete(route('admin.produccion-lechera.destroy', $produccion->id_produccion));

        $response->assertRedirect(route('admin.produccion-lechera.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('produccion_lechera', [
            'id_produccion' => $produccion->id_produccion,
        ]);
    }
}

