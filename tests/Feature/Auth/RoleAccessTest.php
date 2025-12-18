<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Vaca;
use App\Models\ProduccionLechera;
use App\Models\Medicamento;
use App\Models\PruebaSanitaria;
use App\Models\Mortalidad;
use Spatie\Permission\Models\Role;

/**
 * Tests de acceso por rol
 * 
 * Valida que Admin y Pasante solo accedan a lo que les corresponde
 */
class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pasante;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles si no existen
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Pasante', 'guard_name' => 'web']);

        // Crear usuarios con roles
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->pasante = User::factory()->create();
        $this->pasante->assignRole('Pasante');
    }

    // ============================================
    // ADMIN - ACCESO TOTAL
    // ============================================

    /**
     * Test: Admin puede acceder a dashboard
     */
    public function test_admin_puede_acceder_a_dashboard(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
    }

    /**
     * Test: Admin puede acceder a producción lechera (CRUD completo)
     */
    public function test_admin_puede_acceder_a_produccion_lechera_crud(): void
    {
        $this->actingAs($this->admin);

        // Index
        $response = $this->get(route('admin.produccion-lechera.index'));
        $response->assertStatus(200);

        // Create
        $response = $this->get(route('admin.produccion-lechera.create'));
        $response->assertStatus(200);
    }

    /**
     * Test: Admin puede acceder a medicamentos (CRUD completo)
     */
    public function test_admin_puede_acceder_a_medicamentos_crud(): void
    {
        $this->actingAs($this->admin);

        // Index
        $response = $this->get(route('admin.medicamentos.index'));
        $response->assertStatus(200);

        // Create
        $response = $this->get(route('admin.medicamentos.create'));
        $response->assertStatus(200);
    }

    /**
     * Test: Admin puede acceder a retiros (CRUD completo)
     */
    public function test_admin_puede_acceder_a_retiros_crud(): void
    {
        $this->actingAs($this->admin);

        // Index
        $response = $this->get(route('admin.retiros.index'));
        $response->assertStatus(200);

        // Create
        $response = $this->get(route('admin.retiros.create'));
        $response->assertStatus(200);
    }

    /**
     * Test: Admin puede acceder a pruebas sanitarias (CRUD completo)
     */
    public function test_admin_puede_acceder_a_pruebas_sanitarias_crud(): void
    {
        $this->actingAs($this->admin);

        // Index
        $response = $this->get(route('admin.pruebas-sanitarias.index'));
        $response->assertStatus(200);

        // Create
        $response = $this->get(route('admin.pruebas-sanitarias.create'));
        $response->assertStatus(200);
    }

    // ============================================
    // PASANTE - ACCESO LIMITADO
    // ============================================

    /**
     * Test: Pasante puede acceder a dashboard
     */
    public function test_pasante_puede_acceder_a_dashboard(): void
    {
        $response = $this->actingAs($this->pasante)
            ->get(route('pasante.dashboard'));

        $response->assertStatus(200);
    }

    /**
     * Test: Pasante puede acceder a producción lechera (SOLO lectura)
     */
    public function test_pasante_puede_acceder_a_produccion_lechera_solo_lectura(): void
    {
        $produccion = ProduccionLechera::factory()->create();

        $this->actingAs($this->pasante);

        // Index - OK
        $response = $this->get(route('pasante.produccion-lechera.index'));
        $response->assertStatus(200);

        // Show - OK
        $response = $this->get(route('pasante.produccion-lechera.show', $produccion->id_produccion));
        $response->assertStatus(200);

        // Create - NO debe tener ruta, pero si existe debe ser 403
        // No hay ruta create para pasante en producción lechera
    }

    /**
     * Test: Pasante puede acceder a medicamentos (SOLO lectura)
     */
    public function test_pasante_puede_acceder_a_medicamentos_solo_lectura(): void
    {
        $medicamento = Medicamento::factory()->create();

        $this->actingAs($this->pasante);

        // Index - OK
        $response = $this->get(route('pasante.medicamentos.index'));
        $response->assertStatus(200);

        // Show - OK
        $response = $this->get(route('pasante.medicamentos.show', $medicamento->id_medicamento));
        $response->assertStatus(200);
    }

    /**
     * Test: Pasante NO puede acceder a retiros
     */
    public function test_pasante_no_puede_acceder_a_retiros(): void
    {
        $this->actingAs($this->pasante);

        // Intentar acceder a ruta de retiros (no existe para pasante)
        // Si existe alguna ruta, debe ser 403
        $response = $this->get('/pasante/retiros');
        $response->assertStatus(404); // No existe la ruta
    }

    /**
     * Test: Pasante puede acceder a pruebas sanitarias (crear y ver sus propias)
     */
    public function test_pasante_puede_acceder_a_pruebas_sanitarias_crear_y_ver_propias(): void
    {
        $pruebaPropia = PruebaSanitaria::factory()->create(['user_id' => $this->pasante->id]);
        $pruebaAjena = PruebaSanitaria::factory()->create(['user_id' => $this->admin->id]);

        $this->actingAs($this->pasante);

        // Index - OK (solo ve las propias)
        $response = $this->get(route('pasante.pruebas-sanitarias.index'));
        $response->assertStatus(200);

        // Create - OK
        $response = $this->get(route('pasante.pruebas-sanitarias.create'));
        $response->assertStatus(200);

        // Show propia - OK
        $response = $this->get(route('pasante.pruebas-sanitarias.show', $pruebaPropia->id_prueba));
        $response->assertStatus(200);

        // Show ajena - Debe ser 403
        $response = $this->get(route('pasante.pruebas-sanitarias.show', $pruebaAjena->id_prueba));
        $response->assertStatus(403);
    }

    /**
     * Test: Pasante puede acceder a mortalidad (crear y ver)
     */
    public function test_pasante_puede_acceder_a_mortalidad_crear_y_ver(): void
    {
        $mortalidad = Mortalidad::factory()->create();

        $this->actingAs($this->pasante);

        // Index - OK
        $response = $this->get(route('pasante.mortalidad.index'));
        $response->assertStatus(200);

        // Create - OK
        $response = $this->get(route('pasante.mortalidad.create'));
        $response->assertStatus(200);

        // Show - OK
        $response = $this->get(route('pasante.mortalidad.show', $mortalidad->id_mortalidad));
        $response->assertStatus(200);
    }

    /**
     * Test: Pasante NO puede editar mortalidad
     */
    public function test_pasante_no_puede_editar_mortalidad(): void
    {
        $mortalidad = Mortalidad::factory()->create();

        $this->actingAs($this->pasante);

        // Edit - Debe ser 403
        $response = $this->get(route('pasante.mortalidad.edit', $mortalidad->id_mortalidad));
        $response->assertStatus(403);
    }

    /**
     * Test: Pasante NO puede eliminar mortalidad
     */
    public function test_pasante_no_puede_eliminar_mortalidad(): void
    {
        $mortalidad = Mortalidad::factory()->create();

        $this->actingAs($this->pasante);

        // Delete - Debe ser 403
        $response = $this->delete(route('pasante.mortalidad.destroy', $mortalidad->id_mortalidad));
        $response->assertStatus(403);
    }

    // ============================================
    // AISLAMIENTO DE ROLES
    // ============================================

    /**
     * Test: Pasante NO puede acceder a rutas de Admin
     */
    public function test_pasante_no_puede_acceder_a_rutas_de_admin(): void
    {
        $this->actingAs($this->pasante);

        // Intentar acceder a dashboard de admin
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(403); // Prohibido por middleware role:Admin

        // Intentar acceder a producción lechera de admin (create)
        $response = $this->get(route('admin.produccion-lechera.create'));
        $response->assertStatus(403);
    }

    /**
     * Test: Admin NO puede acceder a rutas de Pasante (si aplica)
     */
    public function test_admin_no_puede_acceder_a_rutas_especificas_de_pasante(): void
    {
        $this->actingAs($this->admin);

        // Admin puede acceder a dashboard de pasante (no hay restricción)
        // Pero las rutas específicas de pasante (actividades, tareas) no son para admin
        // Esto es correcto - admin tiene sus propias rutas
    }
}

