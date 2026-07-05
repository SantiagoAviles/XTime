<?php

namespace Tests\Feature\Empleados;

use App\Models\Area;
use App\Models\Empleado;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmpleadoCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function adminUser(): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('Administrador');
        return $user;
    }

    private function empleadoBasico(Area $area, array $overrides = []): Empleado
    {
        return Empleado::create(array_merge([
            'area_id'       => $area->id,
            'nombres'       => 'Juan',
            'apellidos'     => 'Pérez',
            'dni'           => '12345678',
            'cargo'         => 'Operario',
            'estado'        => 'activo',
            'fecha_ingreso' => now()->toDateString(),
        ], $overrides));
    }

    public function test_admin_puede_crear_empleado(): void
    {
        $admin = $this->adminUser();
        $area = Area::create(['nombre' => 'Operaciones', 'is_active' => true]);

        $response = $this->actingAs($admin)->post('/empleados', [
            'nombres'       => 'Mario',
            'apellidos'     => 'Quispe',
            'dni'           => '87654321',
            'area_id'       => $area->id,
            'cargo'         => 'Operario',
            'fecha_ingreso' => now()->toDateString(),
            'estado'        => 'activo',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('empleados', ['dni' => '87654321']);
    }

    public function test_no_permite_dni_duplicado(): void
    {
        $admin = $this->adminUser();
        $area = Area::create(['nombre' => 'Logística', 'is_active' => true]);
        $this->empleadoBasico($area, ['dni' => '11122233']);

        $response = $this->actingAs($admin)->post('/empleados', [
            'nombres'       => 'Otro',
            'apellidos'     => 'Empleado',
            'dni'           => '11122233',
            'area_id'       => $area->id,
            'estado'        => 'activo',
        ]);

        $response->assertSessionHasErrors('dni');
        $this->assertEquals(1, Empleado::where('dni', '11122233')->count());
    }

    public function test_falla_si_faltan_campos_obligatorios(): void
    {
        $admin = $this->adminUser();

        $response = $this->actingAs($admin)->post('/empleados', []);

        $response->assertSessionHasErrors(['nombres', 'apellidos', 'dni', 'area_id']);
    }

    public function test_admin_puede_editar_empleado(): void
    {
        $admin = $this->adminUser();
        $area  = Area::create(['nombre' => 'Mantenimiento', 'is_active' => true]);
        $empleado = $this->empleadoBasico($area);

        $response = $this->actingAs($admin)->put("/empleados/{$empleado->id}", [
            'nombres'   => 'Juan Carlos',
            'apellidos' => 'Pérez García',
            'dni'       => $empleado->dni,
            'area_id'   => $area->id,
            'estado'    => 'activo',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('empleados', ['id' => $empleado->id, 'nombres' => 'Juan Carlos']);
    }

    public function test_baja_logica_no_elimina_fisicamente(): void
    {
        $admin = $this->adminUser();
        $area  = Area::create(['nombre' => 'Producción', 'is_active' => true]);
        $empleado = $this->empleadoBasico($area);

        $response = $this->actingAs($admin)->delete("/empleados/{$empleado->id}");

        $response->assertRedirect();
        $this->assertSoftDeleted('empleados', ['id' => $empleado->id]);
        $this->assertDatabaseHas('empleados', ['id' => $empleado->id, 'estado' => 'inactivo']);
    }

    public function test_filtro_por_busqueda_funciona(): void
    {
        $admin = $this->adminUser();
        $area  = Area::create(['nombre' => 'Calidad', 'is_active' => true]);
        $this->empleadoBasico($area, ['dni' => '10000001', 'nombres' => 'Aurora']);
        $this->empleadoBasico($area, ['dni' => '10000002', 'nombres' => 'Berenice']);

        $response = $this->actingAs($admin)->get('/empleados?busqueda=Aurora');

        $response->assertStatus(200);
        $response->assertSee('Aurora');
        $response->assertDontSee('Berenice');
    }
}
