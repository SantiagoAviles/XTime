<?php

namespace Tests\Feature\Justificaciones;

use App\Models\Area;
use App\Models\Empleado;
use App\Models\Justificacion;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupervisorBandejaScopingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_supervisor_solo_ve_justificaciones_de_su_propia_area(): void
    {
        $areaSupervisor = Area::create(['nombre' => 'Operaciones', 'is_active' => true]);
        $otraArea = Area::create(['nombre' => 'Logística', 'is_active' => true]);

        $userSupervisor = User::factory()->create(['is_active' => true]);
        $userSupervisor->assignRole('Supervisor');
        Empleado::create([
            'user_id' => $userSupervisor->id, 'area_id' => $areaSupervisor->id,
            'nombres' => 'Carla', 'apellidos' => 'Mendoza', 'dni' => '22222222', 'estado' => 'activo',
        ]);

        $empleadoPropio = Empleado::create([
            'area_id' => $areaSupervisor->id,
            'nombres' => 'Juan', 'apellidos' => 'Perez', 'dni' => '33333333', 'estado' => 'activo',
        ]);
        $empleadoOtraArea = Empleado::create([
            'area_id' => $otraArea->id,
            'nombres' => 'Lucia', 'apellidos' => 'Vargas', 'dni' => '44444444', 'estado' => 'activo',
        ]);

        Justificacion::create([
            'empleado_id' => $empleadoPropio->id,
            'fecha'       => now()->toDateString(),
            'tipo'        => Justificacion::TIPO_TARDANZA,
            'motivo'      => 'Motivo del propio área',
            'estado'      => Justificacion::ESTADO_PENDIENTE,
        ]);
        Justificacion::create([
            'empleado_id' => $empleadoOtraArea->id,
            'fecha'       => now()->toDateString(),
            'tipo'        => Justificacion::TIPO_TARDANZA,
            'motivo'      => 'Motivo de otra área',
            'estado'      => Justificacion::ESTADO_PENDIENTE,
        ]);

        $response = $this->actingAs($userSupervisor)->get('/justificaciones');

        $response->assertStatus(200);
        $response->assertSee('Juan Perez');
        $response->assertDontSee('Lucia Vargas');
    }
}
