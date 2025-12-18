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
use App\Models\Cria;
use App\Models\RegistroReproductivo;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

/**
 * Tests de autorización por Policies
 * 
 * Valida que las Policies funcionen correctamente según el rol del usuario
 */
class PolicyAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $pasante;
    protected User $supervisor;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles si no existen
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Pasante', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Supervisor', 'guard_name' => 'web']);

        // Crear usuarios con roles
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->pasante = User::factory()->create();
        $this->pasante->assignRole('Pasante');

        $this->supervisor = User::factory()->create();
        $this->supervisor->assignRole('Supervisor');
    }

    // ============================================
    // PRODUCCIÓN LECHERA
    // ============================================

    /**
     * Test: Admin puede ver cualquier producción
     */
    public function test_admin_puede_ver_cualquier_produccion(): void
    {
        $produccion = ProduccionLechera::factory()->create();

        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('view', $produccion));
    }

    /**
     * Test: Pasante puede ver cualquier producción (solo lectura)
     */
    public function test_pasante_puede_ver_cualquier_produccion(): void
    {
        $produccion = ProduccionLechera::factory()->create();

        $this->actingAs($this->pasante);
        $this->assertTrue(Gate::allows('view', $produccion));
    }

    /**
     * Test: Admin puede crear producción
     */
    public function test_admin_puede_crear_produccion(): void
    {
        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('create', ProduccionLechera::class));
    }

    /**
     * Test: Pasante NO puede crear producción
     */
    public function test_pasante_no_puede_crear_produccion(): void
    {
        $this->actingAs($this->pasante);
        $this->assertFalse(Gate::allows('create', ProduccionLechera::class));
    }

    /**
     * Test: Admin puede editar producción
     */
    public function test_admin_puede_editar_produccion(): void
    {
        $produccion = ProduccionLechera::factory()->create();

        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('update', $produccion));
    }

    /**
     * Test: Pasante NO puede editar producción
     */
    public function test_pasante_no_puede_editar_produccion(): void
    {
        $produccion = ProduccionLechera::factory()->create();

        $this->actingAs($this->pasante);
        $this->assertFalse(Gate::allows('update', $produccion));
    }

    /**
     * Test: Admin puede eliminar producción
     */
    public function test_admin_puede_eliminar_produccion(): void
    {
        $produccion = ProduccionLechera::factory()->create();

        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('delete', $produccion));
    }

    /**
     * Test: Pasante NO puede eliminar producción
     */
    public function test_pasante_no_puede_eliminar_produccion(): void
    {
        $produccion = ProduccionLechera::factory()->create();

        $this->actingAs($this->pasante);
        $this->assertFalse(Gate::allows('delete', $produccion));
    }

    // ============================================
    // MEDICAMENTOS
    // ============================================

    /**
     * Test: Admin puede ver cualquier medicamento
     */
    public function test_admin_puede_ver_cualquier_medicamento(): void
    {
        $medicamento = Medicamento::factory()->create();

        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('view', $medicamento));
    }

    /**
     * Test: Pasante puede ver cualquier medicamento (solo lectura)
     */
    public function test_pasante_puede_ver_cualquier_medicamento(): void
    {
        $medicamento = Medicamento::factory()->create();

        $this->actingAs($this->pasante);
        $this->assertTrue(Gate::allows('view', $medicamento));
    }

    /**
     * Test: Admin puede crear medicamento
     */
    public function test_admin_puede_crear_medicamento(): void
    {
        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('create', Medicamento::class));
    }

    /**
     * Test: Pasante NO puede crear medicamento
     */
    public function test_pasante_no_puede_crear_medicamento(): void
    {
        $this->actingAs($this->pasante);
        $this->assertFalse(Gate::allows('create', Medicamento::class));
    }

    /**
     * Test: Admin puede editar medicamento
     */
    public function test_admin_puede_editar_medicamento(): void
    {
        $medicamento = Medicamento::factory()->create();

        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('update', $medicamento));
    }

    /**
     * Test: Pasante NO puede editar medicamento
     */
    public function test_pasante_no_puede_editar_medicamento(): void
    {
        $medicamento = Medicamento::factory()->create();

        $this->actingAs($this->pasante);
        $this->assertFalse(Gate::allows('update', $medicamento));
    }

    /**
     * Test: Admin puede eliminar medicamento
     */
    public function test_admin_puede_eliminar_medicamento(): void
    {
        $medicamento = Medicamento::factory()->create();

        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('delete', $medicamento));
    }

    /**
     * Test: Pasante NO puede eliminar medicamento
     */
    public function test_pasante_no_puede_eliminar_medicamento(): void
    {
        $medicamento = Medicamento::factory()->create();

        $this->actingAs($this->pasante);
        $this->assertFalse(Gate::allows('delete', $medicamento));
    }

    // ============================================
    // PRUEBAS SANITARIAS
    // ============================================

    /**
     * Test: Admin puede ver cualquier prueba sanitaria
     */
    public function test_admin_puede_ver_cualquier_prueba_sanitaria(): void
    {
        $prueba = PruebaSanitaria::factory()->create();

        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('view', $prueba));
    }

    /**
     * Test: Pasante puede ver solo sus propias pruebas sanitarias
     */
    public function test_pasante_puede_ver_solo_sus_propias_pruebas_sanitarias(): void
    {
        $pruebaPropia = PruebaSanitaria::factory()->create(['user_id' => $this->pasante->id]);
        $pruebaAjena = PruebaSanitaria::factory()->create(['user_id' => $this->admin->id]);

        $this->actingAs($this->pasante);
        $this->assertTrue(Gate::allows('view', $pruebaPropia));
        $this->assertFalse(Gate::allows('view', $pruebaAjena));
    }

    /**
     * Test: Admin puede crear prueba sanitaria
     */
    public function test_admin_puede_crear_prueba_sanitaria(): void
    {
        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('create', PruebaSanitaria::class));
    }

    /**
     * Test: Pasante puede crear prueba sanitaria
     */
    public function test_pasante_puede_crear_prueba_sanitaria(): void
    {
        $this->actingAs($this->pasante);
        $this->assertTrue(Gate::allows('create', PruebaSanitaria::class));
    }

    /**
     * Test: Admin puede editar prueba sanitaria abierta
     */
    public function test_admin_puede_editar_prueba_sanitaria_abierta(): void
    {
        $prueba = PruebaSanitaria::factory()->create(['cerrada' => false]);

        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('update', $prueba));
    }

    /**
     * Test: Pasante puede editar solo sus propias pruebas sanitarias abiertas
     */
    public function test_pasante_puede_editar_solo_sus_propias_pruebas_sanitarias_abiertas(): void
    {
        $pruebaPropiaAbierta = PruebaSanitaria::factory()->create([
            'user_id' => $this->pasante->id,
            'cerrada' => false
        ]);
        $pruebaPropiaCerrada = PruebaSanitaria::factory()->create([
            'user_id' => $this->pasante->id,
            'cerrada' => true
        ]);
        $pruebaAjena = PruebaSanitaria::factory()->create([
            'user_id' => $this->admin->id,
            'cerrada' => false
        ]);

        $this->actingAs($this->pasante);
        $this->assertTrue(Gate::allows('update', $pruebaPropiaAbierta));
        $this->assertFalse(Gate::allows('update', $pruebaPropiaCerrada));
        $this->assertFalse(Gate::allows('update', $pruebaAjena));
    }

    /**
     * Test: Admin puede eliminar prueba sanitaria
     */
    public function test_admin_puede_eliminar_prueba_sanitaria(): void
    {
        $prueba = PruebaSanitaria::factory()->create();

        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('delete', $prueba));
    }

    /**
     * Test: Pasante NO puede eliminar prueba sanitaria
     */
    public function test_pasante_no_puede_eliminar_prueba_sanitaria(): void
    {
        $prueba = PruebaSanitaria::factory()->create(['user_id' => $this->pasante->id]);

        $this->actingAs($this->pasante);
        $this->assertFalse(Gate::allows('delete', $prueba));
    }

    // ============================================
    // MORTALIDAD
    // ============================================

    /**
     * Test: Admin puede ver cualquier mortalidad
     */
    public function test_admin_puede_ver_cualquier_mortalidad(): void
    {
        $mortalidad = Mortalidad::factory()->create();

        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('view', $mortalidad));
    }

    /**
     * Test: Pasante puede ver cualquier mortalidad
     */
    public function test_pasante_puede_ver_cualquier_mortalidad(): void
    {
        $mortalidad = Mortalidad::factory()->create();

        $this->actingAs($this->pasante);
        $this->assertTrue(Gate::allows('view', $mortalidad));
    }

    /**
     * Test: Admin puede crear mortalidad
     */
    public function test_admin_puede_crear_mortalidad(): void
    {
        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('create', Mortalidad::class));
    }

    /**
     * Test: Pasante puede crear mortalidad
     */
    public function test_pasante_puede_crear_mortalidad(): void
    {
        $this->actingAs($this->pasante);
        $this->assertTrue(Gate::allows('create', Mortalidad::class));
    }

    /**
     * Test: Admin puede editar mortalidad
     */
    public function test_admin_puede_editar_mortalidad(): void
    {
        $mortalidad = Mortalidad::factory()->create();

        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('update', $mortalidad));
    }

    /**
     * Test: Pasante NO puede editar mortalidad
     */
    public function test_pasante_no_puede_editar_mortalidad(): void
    {
        $mortalidad = Mortalidad::factory()->create();

        $this->actingAs($this->pasante);
        $this->assertFalse(Gate::allows('update', $mortalidad));
    }

    /**
     * Test: Admin puede eliminar mortalidad
     */
    public function test_admin_puede_eliminar_mortalidad(): void
    {
        $mortalidad = Mortalidad::factory()->create();

        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('delete', $mortalidad));
    }

    /**
     * Test: Pasante NO puede eliminar mortalidad
     */
    public function test_pasante_no_puede_eliminar_mortalidad(): void
    {
        $mortalidad = Mortalidad::factory()->create();

        $this->actingAs($this->pasante);
        $this->assertFalse(Gate::allows('delete', $mortalidad));
    }

    // ============================================
    // CRÍAS
    // ============================================

    /**
     * Test: Admin puede ver cualquier cría
     */
    public function test_admin_puede_ver_cualquier_cria(): void
    {
        $cria = Cria::factory()->create();

        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('view', $cria));
    }

    /**
     * Test: Pasante puede ver cualquier cría
     */
    public function test_pasante_puede_ver_cualquier_cria(): void
    {
        $cria = Cria::factory()->create();

        $this->actingAs($this->pasante);
        $this->assertTrue(Gate::allows('view', $cria));
    }

    /**
     * Test: Admin puede crear cría
     */
    public function test_admin_puede_crear_cria(): void
    {
        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('create', Cria::class));
    }

    /**
     * Test: Pasante NO puede crear cría
     */
    public function test_pasante_no_puede_crear_cria(): void
    {
        $this->actingAs($this->pasante);
        $this->assertFalse(Gate::allows('create', Cria::class));
    }

    // ============================================
    // REGISTROS REPRODUCTIVOS
    // ============================================

    /**
     * Test: Admin puede ver cualquier registro reproductivo
     */
    public function test_admin_puede_ver_cualquier_registro_reproductivo(): void
    {
        $registro = RegistroReproductivo::factory()->create();

        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('view', $registro));
    }

    /**
     * Test: Pasante puede ver cualquier registro reproductivo
     */
    public function test_pasante_puede_ver_cualquier_registro_reproductivo(): void
    {
        $registro = RegistroReproductivo::factory()->create();

        $this->actingAs($this->pasante);
        $this->assertTrue(Gate::allows('view', $registro));
    }

    /**
     * Test: Admin puede crear registro reproductivo
     */
    public function test_admin_puede_crear_registro_reproductivo(): void
    {
        $this->actingAs($this->admin);
        $this->assertTrue(Gate::allows('create', RegistroReproductivo::class));
    }

    /**
     * Test: Pasante NO puede crear registro reproductivo
     */
    public function test_pasante_no_puede_crear_registro_reproductivo(): void
    {
        $this->actingAs($this->pasante);
        $this->assertFalse(Gate::allows('create', RegistroReproductivo::class));
    }
}

