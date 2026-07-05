<?php

namespace Tests\Feature\Seguridad;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsignacionRolTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_puede_cambiar_rol_de_otro_usuario(): void
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('Administrador');

        $usuario = User::factory()->create(['is_active' => true]);
        $usuario->assignRole('Empleado');

        $resp = $this->actingAs($admin)->put("/seguridad/usuarios/{$usuario->id}", [
            'rol'       => 'Supervisor',
            'is_active' => 1,
        ]);

        $resp->assertRedirect();
        $this->assertTrue($usuario->fresh()->hasRole('Supervisor'));
        $this->assertFalse($usuario->fresh()->hasRole('Empleado'));
    }

    public function test_usuario_sin_permiso_no_puede_cambiar_roles(): void
    {
        $u = User::factory()->create(['is_active' => true]);
        $u->assignRole('Empleado');

        $otro = User::factory()->create(['is_active' => true]);
        $otro->assignRole('Empleado');

        $resp = $this->actingAs($u)->put("/seguridad/usuarios/{$otro->id}", [
            'rol' => 'Administrador',
        ]);

        $resp->assertStatus(403);
        $this->assertFalse($otro->fresh()->hasRole('Administrador'));
    }
}
