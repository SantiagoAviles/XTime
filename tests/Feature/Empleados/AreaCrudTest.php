<?php

namespace Tests\Feature\Empleados;

use App\Models\Area;
use App\Models\Empleado;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AreaCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function admin(): User
    {
        $u = User::factory()->create(['is_active' => true]);
        $u->assignRole('Administrador');
        return $u;
    }

    public function test_admin_puede_crear_area(): void
    {
        $admin = $this->admin();

        $resp = $this->actingAs($admin)->post('/areas', [
            'nombre'      => 'Calidad',
            'descripcion' => 'Aseguramiento de calidad',
            'is_active'   => 1,
        ]);

        $resp->assertRedirect();
        $this->assertDatabaseHas('areas', ['nombre' => 'Calidad']);
    }

    public function test_nombre_unico(): void
    {
        $admin = $this->admin();
        Area::create(['nombre' => 'Logística', 'is_active' => true]);

        $resp = $this->actingAs($admin)->post('/areas', ['nombre' => 'Logística']);

        $resp->assertSessionHasErrors('nombre');
    }

    public function test_no_se_puede_eliminar_area_con_empleados_activos(): void
    {
        $admin = $this->admin();
        $area  = Area::create(['nombre' => 'Operaciones', 'is_active' => true]);
        Empleado::create([
            'area_id' => $area->id,
            'nombres' => 'A',
            'apellidos' => 'B',
            'dni' => '99887766',
            'estado' => 'activo',
        ]);

        $resp = $this->actingAs($admin)->delete("/areas/{$area->id}");

        $resp->assertRedirect();
        $resp->assertSessionHas('error');
        $this->assertDatabaseHas('areas', ['id' => $area->id]);
    }
}
