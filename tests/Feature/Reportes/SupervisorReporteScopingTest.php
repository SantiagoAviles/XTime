<?php

namespace Tests\Feature\Reportes;

use App\Models\Area;
use App\Models\Empleado;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupervisorReporteScopingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_supervisor_ve_su_propia_area_y_no_ve_otras_en_el_reporte(): void
    {
        $areaSupervisor = Area::create(['nombre' => 'Operaciones', 'is_active' => true]);
        $otraArea = Area::create(['nombre' => 'Logística', 'is_active' => true]);

        $userSupervisor = User::factory()->create(['is_active' => true]);
        $userSupervisor->assignRole('Supervisor');
        Empleado::create([
            'user_id' => $userSupervisor->id, 'area_id' => $areaSupervisor->id,
            'nombres' => 'Carla', 'apellidos' => 'Mendoza', 'dni' => '22222222', 'estado' => 'activo',
        ]);

        Empleado::create([
            'area_id' => $areaSupervisor->id,
            'nombres' => 'Juan', 'apellidos' => 'Perez', 'dni' => '33333333', 'estado' => 'activo',
        ]);
        Empleado::create([
            'area_id' => $otraArea->id,
            'nombres' => 'Lucia', 'apellidos' => 'Vargas', 'dni' => '44444444', 'estado' => 'activo',
        ]);

        $response = $this->actingAs($userSupervisor)->get('/reportes/asistencia?previsualizar=1');

        $response->assertStatus(200);
        $response->assertSee('Juan Perez');
        $response->assertDontSee('Lucia Vargas');
    }
}
