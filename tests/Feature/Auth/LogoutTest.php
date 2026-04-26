<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    private function crearUsuarioAutenticado(): User
    {
        $user = User::factory()->create(['is_active' => true]);

        Permission::firstOrCreate(['name' => 'access_dashboard', 'guard_name' => 'web']);
        $rol = Role::firstOrCreate(['name' => 'Empleado', 'guard_name' => 'web']);
        $rol->givePermissionTo('access_dashboard');
        $user->assignRole($rol);

        return $user;
    }

    public function test_usuario_autenticado_puede_cerrar_sesion(): void
    {
        $user = $this->crearUsuarioAutenticado();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
    }

    public function test_despues_del_logout_el_usuario_queda_como_guest(): void
    {
        $user = $this->crearUsuarioAutenticado();

        $this->actingAs($user)->post('/logout');

        $this->assertGuest();
    }

    public function test_logout_invalida_la_sesion_y_redirige_a_login(): void
    {
        $user = $this->crearUsuarioAutenticado();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirectToRoute('login');
        $this->assertGuest();
    }

    public function test_guest_no_puede_acceder_a_logout_sin_autenticacion(): void
    {
        $response = $this->post('/logout');

        // Sin autenticación redirige a login
        $response->assertRedirect('/login');
    }

    public function test_se_registra_evento_logout_en_activity_log(): void
    {
        $user = $this->crearUsuarioAutenticado();

        $this->actingAs($user)->post('/logout');

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'auth',
            'event'    => 'logout',
        ]);
    }
}
