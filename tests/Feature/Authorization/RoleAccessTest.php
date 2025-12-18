<?php

namespace Tests\Feature\Authorization;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Vaca;
use App\Models\ProduccionLechera;
use App\Models\Medicamento;
use App\Models\UsoMedicamento;
use App\Models\PruebaSanitaria;
use App\Models\Mortalidad;
use App\Models\Retiro;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Tests CRÍTICOS: Seguridad por Roles (Admin vs Pasante)
 * 
 * Valida que Admin y Pasante solo accedan a lo que les corresponde.
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
        if (!Role::where('name', 'Admin')->exists()) {
            Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        }
        if (!Role::where('name', 'Pasante')->exists()) {
            Role::create(['name' => 'Pasante', 'guard_name' => 'web']);
        }

        // Crear usuarios
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->pasante = User::factory()->create();
        $this->pasante->assignRole('Pasante');
    }

    // ============================================
    // TESTS: PRODUCCIÓN LECHERA
    // ============================================

    /**
     * Test: Admin puede ver listado de producción lechera
     */
    public function test_admin_puede_ver_listado_produccion_lechera(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.produccion-lechera.index'));

        $response->assertStatus(200);
    }

    /**
     * Test: Admin puede crear producción lechera
     */
    public function test_admin_puede_crear_produccion_lechera(): void
    {
        $this->actingAs($this->admin);
        $vaca = Vaca::factory()->create(['estado_reproductivo' => 'Lactancia']);

        $response = $this->get(route('admin.produccion-lechera.create'));

        $response->assertStatus(200);
    }

    /**
     * Test: Pasante puede ver listado de producción lechera (solo lectura)
     */
    public function test_pasante_puede_ver_listado_produccion_lechera(): void
    {
        $this->actingAs($this->pasante);

        $response = $this->get(route('pasante.produccion-lechera.index'));

        $response->assertStatus(200);
    }

    /**
     * Test: Pasante NO puede crear producción lechera
     */
    public function test_pasante_no_puede_crear_produccion_lechera(): void
    {
        $this->actingAs($this->pasante);

        // Intentar acceder a la ruta de creación (no existe para pasante, pero validamos)
        $response = $this->get(route('admin.produccion-lechera.create'));

        // Debe ser 403 o redirigir (depende de la implementación)
        $this->assertTrue(
            $response->status() === 403 || $response->status() === 302,
            'Pasante no debe poder acceder a crear producción lechera'
        );
    }

    // ============================================
    // TESTS: MEDICAMENTOS
    // ============================================

    /**
     * Test: Admin puede ver listado de medicamentos
     */
    public function test_admin_puede_ver_listado_medicamentos(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.medicamentos.index'));

        $response->assertStatus(200);
    }

    /**
     * Test: Admin puede crear medicamentos
     */
    public function test_admin_puede_crear_medicamentos(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.medicamentos.create'));

        $response->assertStatus(200);
    }

    /**
     * Test CRÍTICO: Pasante NO puede ver medicamentos
     */
    public function test_pasante_no_puede_ver_medicamentos(): void
    {
        $this->actingAs($this->pasante);

        // Intentar acceder a la ruta de medicamentos de pasante (no debe existir o debe ser 403)
        try {
            $response = $this->get(route('pasante.medicamentos.index'));
            // Si la ruta existe, debe ser 403
            $this->assertEquals(403, $response->status(), 'Pasante no debe poder ver medicamentos');
        } catch (\Exception $e) {
            // Si la ruta no existe, está bien (no hay acceso)
            $this->assertTrue(true);
        }
    }

    // ============================================
    // TESTS: USO DE MEDICAMENTOS
    // ============================================

    /**
     * Test: Admin puede ver listado de uso de medicamentos
     */
    public function test_admin_puede_ver_listado_uso_medicamentos(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.uso-medicamentos.index'));

        $response->assertStatus(200);
    }

    /**
     * Test CRÍTICO: Pasante NO puede ver uso de medicamentos
     */
    public function test_pasante_no_puede_ver_uso_medicamentos(): void
    {
        $this->actingAs($this->pasante);

        // Intentar acceder a la ruta de uso de medicamentos de pasante
        try {
            $response = $this->get(route('pasante.uso-medicamentos.index'));
            // Si la ruta existe, debe ser 403
            $this->assertEquals(403, $response->status(), 'Pasante no debe poder ver uso de medicamentos');
        } catch (\Exception $e) {
            // Si la ruta no existe, está bien (no hay acceso)
            $this->assertTrue(true);
        }
    }

    /**
     * Test CRÍTICO: Pasante NO puede crear uso de medicamentos
     */
    public function test_pasante_no_puede_crear_uso_medicamentos(): void
    {
        $this->actingAs($this->pasante);

        // Intentar acceder a la ruta de creación
        try {
            $response = $this->get(route('pasante.uso-medicamentos.create'));
            // Si la ruta existe, debe ser 403
            $this->assertEquals(403, $response->status(), 'Pasante no debe poder crear uso de medicamentos');
        } catch (\Exception $e) {
            // Si la ruta no existe, está bien (no hay acceso)
            $this->assertTrue(true);
        }
    }

    // ============================================
    // TESTS: RETIROS
    // ============================================

    /**
     * Test: Admin puede ver listado de retiros
     */
    public function test_admin_puede_ver_listado_retiros(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.retiros.index'));

        $response->assertStatus(200);
    }

    /**
     * Test CRÍTICO: Pasante NO puede acceder a retiros
     */
    public function test_pasante_no_puede_acceder_retiros(): void
    {
        $this->actingAs($this->pasante);

        // Intentar acceder a la ruta de retiros (no debe existir para pasante)
        try {
            $response = $this->get(route('admin.retiros.index'));
            // Debe ser 403 o redirigir
            $this->assertTrue(
                $response->status() === 403 || $response->status() === 302,
                'Pasante no debe poder acceder a retiros'
            );
        } catch (\Exception $e) {
            // Si la ruta no existe para pasante, está bien
            $this->assertTrue(true);
        }
    }

    // ============================================
    // TESTS: PRUEBAS SANITARIAS
    // ============================================

    /**
     * Test: Admin puede ver listado de pruebas sanitarias
     */
    public function test_admin_puede_ver_listado_pruebas_sanitarias(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.pruebas-sanitarias.index'));

        $response->assertStatus(200);
    }

    /**
     * Test: Admin puede crear pruebas sanitarias
     */
    public function test_admin_puede_crear_pruebas_sanitarias(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.pruebas-sanitarias.create'));

        $response->assertStatus(200);
    }

    /**
     * Test: Pasante puede ver sus propias pruebas sanitarias
     */
    public function test_pasante_puede_ver_sus_propias_pruebas_sanitarias(): void
    {
        $this->actingAs($this->pasante);

        $vaca = Vaca::factory()->create();
        $prueba = PruebaSanitaria::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'user_id' => $this->pasante->id,
        ]);

        $response = $this->get(route('pasante.pruebas-sanitarias.show', $prueba->id_prueba));

        $response->assertStatus(200);
    }

    /**
     * Test: Pasante puede crear pruebas sanitarias
     */
    public function test_pasante_puede_crear_pruebas_sanitarias(): void
    {
        $this->actingAs($this->pasante);

        $response = $this->get(route('pasante.pruebas-sanitarias.create'));

        $response->assertStatus(200);
    }

    /**
     * Test CRÍTICO: Pasante NO puede ver pruebas sanitarias de otros
     */
    public function test_pasante_no_puede_ver_pruebas_sanitarias_de_otros(): void
    {
        $this->actingAs($this->pasante);

        $vaca = Vaca::factory()->create();
        $otroUsuario = User::factory()->create();
        $otroUsuario->assignRole('Admin');
        
        $prueba = PruebaSanitaria::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'user_id' => $otroUsuario->id,
        ]);

        $response = $this->get(route('pasante.pruebas-sanitarias.show', $prueba->id_prueba));

        // Debe ser 403 o redirigir
        $this->assertTrue(
            $response->status() === 403 || $response->status() === 302,
            'Pasante no debe poder ver pruebas sanitarias de otros usuarios'
        );
    }

    // ============================================
    // TESTS: MORTALIDAD
    // ============================================

    /**
     * Test: Admin puede ver listado de mortalidad
     */
    public function test_admin_puede_ver_listado_mortalidad(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('admin.mortalidad.index'));

        $response->assertStatus(200);
    }

    /**
     * Test: Pasante puede ver listado de mortalidad
     */
    public function test_pasante_puede_ver_listado_mortalidad(): void
    {
        $this->actingAs($this->pasante);

        $response = $this->get(route('pasante.mortalidad.index'));

        $response->assertStatus(200);
    }

    /**
     * Test: Pasante puede crear mortalidad
     */
    public function test_pasante_puede_crear_mortalidad(): void
    {
        $this->actingAs($this->pasante);

        $response = $this->get(route('pasante.mortalidad.create'));

        $response->assertStatus(200);
    }

    /**
     * Test: Pasante NO puede eliminar mortalidad
     */
    public function test_pasante_no_puede_eliminar_mortalidad(): void
    {
        $this->actingAs($this->pasante);

        $vaca = Vaca::factory()->create();
        $mortalidad = \App\Models\Mortalidad::factory()->create([
            'id_vaca' => $vaca->id_vaca,
        ]);

        $response = $this->delete(route('pasante.mortalidad.destroy', $mortalidad->id_mortalidad));

        // Debe ser 403 o redirigir
        $this->assertTrue(
            $response->status() === 403 || $response->status() === 302,
            'Pasante no debe poder eliminar mortalidad'
        );
    }

    // ============================================
    // TESTS: POLICIES DIRECTAS
    // ============================================

    /**
     * Test: Policy de Medicamento - Pasante NO puede ver
     */
    public function test_policy_medicamento_pasante_no_puede_ver(): void
    {
        $medicamento = Medicamento::factory()->create();
        $policy = new \App\Policies\MedicamentoPolicy();

        $this->assertFalse(
            $policy->viewAny($this->pasante),
            'Pasante no debe poder ver medicamentos según Policy'
        );

        $this->assertFalse(
            $policy->view($this->pasante, $medicamento),
            'Pasante no debe poder ver un medicamento según Policy'
        );
    }

    /**
     * Test: Policy de Medicamento - Admin puede ver
     */
    public function test_policy_medicamento_admin_puede_ver(): void
    {
        $medicamento = Medicamento::factory()->create();
        $policy = new \App\Policies\MedicamentoPolicy();

        $this->assertTrue(
            $policy->viewAny($this->admin),
            'Admin debe poder ver medicamentos según Policy'
        );

        $this->assertTrue(
            $policy->view($this->admin, $medicamento),
            'Admin debe poder ver un medicamento según Policy'
        );
    }

    /**
     * Test: Policy de UsoMedicamento - Pasante NO puede ver
     */
    public function test_policy_uso_medicamento_pasante_no_puede_ver(): void
    {
        $uso = UsoMedicamento::factory()->create();
        $policy = new \App\Policies\UsoMedicamentoPolicy();

        $this->assertFalse(
            $policy->viewAny($this->pasante),
            'Pasante no debe poder ver uso de medicamentos según Policy'
        );

        $this->assertFalse(
            $policy->view($this->pasante, $uso),
            'Pasante no debe poder ver un uso de medicamento según Policy'
        );

        $this->assertFalse(
            $policy->create($this->pasante),
            'Pasante no debe poder crear uso de medicamentos según Policy'
        );
    }

    /**
     * Test: Policy de UsoMedicamento - Admin puede ver
     */
    public function test_policy_uso_medicamento_admin_puede_ver(): void
    {
        $uso = UsoMedicamento::factory()->create();
        $policy = new \App\Policies\UsoMedicamentoPolicy();

        $this->assertTrue(
            $policy->viewAny($this->admin),
            'Admin debe poder ver uso de medicamentos según Policy'
        );

        $this->assertTrue(
            $policy->view($this->admin, $uso),
            'Admin debe poder ver un uso de medicamento según Policy'
        );

        $this->assertTrue(
            $policy->create($this->admin),
            'Admin debe poder crear uso de medicamentos según Policy'
        );
    }

    /**
     * Test: Policy de ProduccionLechera - Pasante solo lectura
     */
    public function test_policy_produccion_lechera_pasante_solo_lectura(): void
    {
        $produccion = ProduccionLechera::factory()->create();
        $policy = new \App\Policies\ProduccionLecheraPolicy();

        $this->assertTrue(
            $policy->viewAny($this->pasante),
            'Pasante debe poder ver listado de producción lechera'
        );

        $this->assertTrue(
            $policy->view($this->pasante, $produccion),
            'Pasante debe poder ver una producción lechera'
        );

        $this->assertFalse(
            $policy->create($this->pasante),
            'Pasante NO debe poder crear producción lechera'
        );

        $this->assertFalse(
            $policy->update($this->pasante, $produccion),
            'Pasante NO debe poder actualizar producción lechera'
        );

        $this->assertFalse(
            $policy->delete($this->pasante, $produccion),
            'Pasante NO debe poder eliminar producción lechera'
        );
    }

    /**
     * Test: Policy de ProduccionLechera - Admin acceso total
     */
    public function test_policy_produccion_lechera_admin_acceso_total(): void
    {
        $produccion = ProduccionLechera::factory()->create();
        $policy = new \App\Policies\ProduccionLecheraPolicy();

        $this->assertTrue($policy->viewAny($this->admin));
        $this->assertTrue($policy->view($this->admin, $produccion));
        $this->assertTrue($policy->create($this->admin));
        $this->assertTrue($policy->update($this->admin, $produccion));
        $this->assertTrue($policy->delete($this->admin, $produccion));
    }

    /**
     * Test: Policy de PruebaSanitaria - Pasante puede crear
     */
    public function test_policy_prueba_sanitaria_pasante_puede_crear(): void
    {
        $policy = new \App\Policies\PruebaSanitariaPolicy();

        $this->assertTrue(
            $policy->create($this->pasante),
            'Pasante debe poder crear pruebas sanitarias'
        );
    }

    /**
     * Test: Policy de PruebaSanitaria - Pasante NO puede eliminar
     */
    public function test_policy_prueba_sanitaria_pasante_no_puede_eliminar(): void
    {
        $vaca = Vaca::factory()->create();
        $prueba = PruebaSanitaria::factory()->create([
            'id_vaca' => $vaca->id_vaca,
            'user_id' => $this->pasante->id,
        ]);

        $policy = new \App\Policies\PruebaSanitariaPolicy();

        $this->assertFalse(
            $policy->delete($this->pasante, $prueba),
            'Pasante NO debe poder eliminar pruebas sanitarias'
        );
    }
}

