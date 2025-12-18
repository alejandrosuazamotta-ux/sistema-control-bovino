<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\ProduccionLechera;
use App\Models\Medicamento;
use App\Models\PruebaSanitaria;
use App\Models\Mortalidad;
use Spatie\Permission\Models\Role;

/**
 * Tests de rutas protegidas
 * 
 * Valida que las rutas estén protegidas correctamente por middleware y Policies
 */
class RouteProtectionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pasante;
    protected User $usuarioSinRol;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles si no existen
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Pasante', 'guard_name' => 'web']);

        // Crear usuarios
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->pasante = User::factory()->create();
        $this->pasante->assignRole('Pasante');

        $this->usuarioSinRol = User::factory()->create();
    }

    // ============================================
    // RUTAS PROTEGIDAS POR MIDDLEWARE
    // ============================================

    /**
     * Test: Rutas de Admin requieren autenticación y rol Admin
     */
    public function test_rutas_admin_requieren_autenticacion_y_rol(): void
    {
        // Sin autenticación
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));

        // Con autenticación pero sin rol
        $response = $this->actingAs($this->usuarioSinRol)
            ->get(route('admin.dashboard'));
        $response->assertStatus(403);

        // Con autenticación y rol Admin
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }

    /**
     * Test: Rutas de Pasante requieren autenticación y rol Pasante
     */
    public function test_rutas_pasante_requieren_autenticacion_y_rol(): void
    {
        // Sin autenticación
        $response = $this->get(route('pasante.dashboard'));
        $response->assertRedirect(route('login'));

        // Con autenticación pero sin rol
        $response = $this->actingAs($this->usuarioSinRol)
            ->get(route('pasante.dashboard'));
        $response->assertStatus(403);

        // Con autenticación y rol Pasante
        $response = $this->actingAs($this->pasante)
            ->get(route('pasante.dashboard'));
        $response->assertStatus(200);
    }

    // ============================================
    // RUTAS PROTEGIDAS POR POLICIES
    // ============================================

    /**
     * Test: Crear producción lechera requiere Policy
     */
    public function test_crear_produccion_lechera_requiere_policy(): void
    {
        $vaca = \App\Models\Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);

        // Admin puede crear
        $response = $this->actingAs($this->admin)
            ->post(route('admin.produccion-lechera.store'), [
                'id_vaca' => $vaca->id_vaca,
                'fecha' => '2025-01-15',
                'turno' => 'Mañana',
                'cantidad_leche' => 10.5,
                'destino' => 'Venta',
                'id_personal' => \App\Models\Personal::factory()->create()->id_personal,
            ]);
        $response->assertRedirect(); // Redirige después de crear

        // Pasante NO puede crear
        $response = $this->actingAs($this->pasante)
            ->post(route('admin.produccion-lechera.store'), [
                'id_vaca' => $vaca->id_vaca,
                'fecha' => '2025-01-15',
                'turno' => 'Mañana',
                'cantidad_leche' => 10.5,
                'destino' => 'Venta',
                'id_personal' => \App\Models\Personal::factory()->create()->id_personal,
            ]);
        $response->assertStatus(403);
    }

    /**
     * Test: Editar producción lechera requiere Policy
     */
    public function test_editar_produccion_lechera_requiere_policy(): void
    {
        $produccion = ProduccionLechera::factory()->create();

        // Admin puede editar
        $response = $this->actingAs($this->admin)
            ->get(route('admin.produccion-lechera.edit', $produccion->id_produccion));
        $response->assertStatus(200);

        // Pasante NO puede editar
        $response = $this->actingAs($this->pasante)
            ->get(route('admin.produccion-lechera.edit', $produccion->id_produccion));
        $response->assertStatus(403);
    }

    /**
     * Test: Eliminar producción lechera requiere Policy
     */
    public function test_eliminar_produccion_lechera_requiere_policy(): void
    {
        $produccion = ProduccionLechera::factory()->create();

        // Admin puede eliminar
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.produccion-lechera.destroy', $produccion->id_produccion));
        $response->assertRedirect();

        // Pasante NO puede eliminar
        $produccion2 = ProduccionLechera::factory()->create();
        $response = $this->actingAs($this->pasante)
            ->delete(route('admin.produccion-lechera.destroy', $produccion2->id_produccion));
        $response->assertStatus(403);
    }

    /**
     * Test: Crear medicamento requiere Policy
     */
    public function test_crear_medicamento_requiere_policy(): void
    {
        // Admin puede crear
        $response = $this->actingAs($this->admin)
            ->get(route('admin.medicamentos.create'));
        $response->assertStatus(200);

        // Pasante NO puede crear
        $response = $this->actingAs($this->pasante)
            ->get(route('admin.medicamentos.create'));
        $response->assertStatus(403);
    }

    /**
     * Test: Editar medicamento requiere Policy
     */
    public function test_editar_medicamento_requiere_policy(): void
    {
        $medicamento = Medicamento::factory()->create();

        // Admin puede editar
        $response = $this->actingAs($this->admin)
            ->get(route('admin.medicamentos.edit', $medicamento->id_medicamento));
        $response->assertStatus(200);

        // Pasante NO puede editar
        $response = $this->actingAs($this->pasante)
            ->get(route('admin.medicamentos.edit', $medicamento->id_medicamento));
        $response->assertStatus(403);
    }

    /**
     * Test: Eliminar prueba sanitaria requiere Policy
     */
    public function test_eliminar_prueba_sanitaria_requiere_policy(): void
    {
        $prueba = PruebaSanitaria::factory()->create(['user_id' => $this->pasante->id]);

        // Admin puede eliminar
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.pruebas-sanitarias.destroy', $prueba->id_prueba));
        $response->assertRedirect();

        // Pasante NO puede eliminar (incluso si es suya)
        $prueba2 = PruebaSanitaria::factory()->create(['user_id' => $this->pasante->id]);
        $response = $this->actingAs($this->pasante)
            ->delete(route('pasante.pruebas-sanitarias.destroy', $prueba2->id_prueba));
        $response->assertStatus(403);
    }

    /**
     * Test: Editar mortalidad requiere Policy
     */
    public function test_editar_mortalidad_requiere_policy(): void
    {
        $mortalidad = Mortalidad::factory()->create();

        // Admin puede editar
        $response = $this->actingAs($this->admin)
            ->get(route('admin.mortalidad.edit', $mortalidad->id_mortalidad));
        $response->assertStatus(200);

        // Pasante NO puede editar
        $response = $this->actingAs($this->pasante)
            ->get(route('pasante.mortalidad.edit', $mortalidad->id_mortalidad));
        $response->assertStatus(403);
    }

    // ============================================
    // RUTAS SIN ACCESO PARA PASANTE
    // ============================================

    /**
     * Test: Pasante NO tiene acceso a retiros
     */
    public function test_pasante_no_tiene_acceso_a_retiros(): void
    {
        $this->actingAs($this->pasante);

        // No existe ruta de retiros para pasante
        $response = $this->get('/pasante/retiros');
        $response->assertStatus(404);

        // Intentar acceder a ruta de admin (debe ser 403)
        $response = $this->get(route('admin.retiros.index'));
        $response->assertStatus(403);
    }

    /**
     * Test: Pasante NO puede importar/exportar producción lechera
     */
    public function test_pasante_no_puede_importar_exportar_produccion_lechera(): void
    {
        $this->actingAs($this->pasante);

        // No existen rutas de import/export para pasante
        $response = $this->get('/pasante/produccion-lechera/importar');
        $response->assertStatus(404);

        // Intentar acceder a ruta de admin (debe ser 403)
        $response = $this->get(route('admin.produccion-lechera.import'));
        $response->assertStatus(403);
    }
}

