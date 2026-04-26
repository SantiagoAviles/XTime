<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    private function crearUsuarioConRol(string $rol = 'Empleado', bool $activo = true): User
    {
        $user = User::factory()->create(['is_active' => $activo]);

        Permission::firstOrCreate(['name' => 'access_dashboard', 'guard_name' => 'web']);
        $roleModel = Role::firstOrCreate(['name' => $rol, 'guard_name' => 'web']);
        $roleModel->givePermissionTo('access_dashboard');
        $user->assignRole($roleModel);

        return $user;
    }

    public function test_usuario_ve_pantalla_de_login(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_usuario_activo_con_rol_puede_iniciar_sesion(): void
    {
        $user = $this->crearUsuarioConRol();

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_credenciales_incorrectas_no_inician_sesion(): void
    {
        $user = $this->crearUsuarioConRol();

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'contraseña-incorrecta',
        ]);

        $response->assertSessionHasErrors('credentials');
        $this->assertGuest();
    }

    public function test_usuario_inactivo_no_puede_iniciar_sesion(): void
    {
        $user = $this->crearUsuarioConRol('Empleado', false);

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('credentials');
        $this->assertGuest();
    }

    public function test_usuario_sin_rol_no_puede_iniciar_sesion(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('credentials');
        $this->assertGuest();
    }

    public function test_se_bloquea_login_tras_cinco_intentos_fallidos(): void
    {
        $user = $this->crearUsuarioConRol();

        // Cinco intentos fallidos
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email'    => $user->email,
                'password' => 'contraseña-incorrecta',
            ]);
        }

        // Sexto intento (aunque con la contraseña correcta) debe quedar bloqueado
        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('credentials');
        $this->assertStringContainsString(
            'bloqueado',
            session()->get('errors')->first('credentials')
        );
    }

    public function test_se_registra_evento_login_exitoso_en_activity_log(): void
    {
        $user = $this->crearUsuarioConRol();

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'auth',
            'event'    => 'login_success',
        ]);
    }

    public function test_se_registra_evento_login_fallido_en_activity_log(): void
    {
        $user = $this->crearUsuarioConRol();

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'contraseña-incorrecta',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'auth',
            'event'    => 'login_failed',
        ]);
    }

    public function test_se_registra_evento_usuario_inactivo_en_activity_log(): void
    {
        $user = $this->crearUsuarioConRol('Empleado', false);

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'auth',
            'event'    => 'inactive_user_login_attempt',
        ]);
    }
}
