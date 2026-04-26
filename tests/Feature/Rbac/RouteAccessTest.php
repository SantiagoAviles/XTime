<?php

namespace Tests\Feature\Rbac;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RouteAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function crearPermiso(string $nombre): Permission
    {
        return Permission::firstOrCreate(['name' => $nombre, 'guard_name' => 'web']);
    }

    private function crearRol(string $nombre, array $permisos = []): Role
    {
        $rol = Role::firstOrCreate(['name' => $nombre, 'guard_name' => 'web']);
        foreach ($permisos as $permiso) {
            $rol->givePermissionTo($this->crearPermiso($permiso));
        }
        return $rol;
    }

    private function crearUsuario(string $rol, array $permisos = []): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $roleModel = $this->crearRol($rol, $permisos);
        $user->assignRole($roleModel);
        return $user;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Dashboard
    // ─────────────────────────────────────────────────────────────────────────

    public function test_guest_no_puede_acceder_a_dashboard_y_es_redirigido_a_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_administrador_puede_acceder_al_dashboard(): void
    {
        $user = $this->crearUsuario('Administrador', [
            'access_dashboard',
            'view_employees',
            'manage_areas',
            'assign_roles',
            'view_activity_log',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_usuario_con_permiso_access_dashboard_puede_entrar(): void
    {
        $user = $this->crearUsuario('Empleado', ['access_dashboard']);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_usuario_sin_permiso_access_dashboard_recibe_403(): void
    {
        // Usuario autenticado pero sin permiso access_dashboard
        $user = User::factory()->create(['is_active' => true]);
        $rol = $this->crearRol('SinPermisoDashboard'); // rol sin permisos
        $user->assignRole($rol);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(403);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Empleados (requiere rol Administrador | RRHH | permiso view_employees)
    // ─────────────────────────────────────────────────────────────────────────

    public function test_administrador_puede_acceder_a_gestion_de_empleados(): void
    {
        $user = $this->crearUsuario('Administrador', ['access_dashboard', 'view_employees']);

        $response = $this->actingAs($user)->get('/empleados');

        $response->assertStatus(200);
    }

    public function test_empleado_sin_permiso_no_puede_acceder_a_gestion_de_empleados(): void
    {
        $user = $this->crearUsuario('Empleado', ['access_dashboard']);
        // El rol Empleado NO tiene view_employees

        $response = $this->actingAs($user)->get('/empleados');

        $response->assertStatus(403);
    }

    public function test_guest_es_redirigido_a_login_al_acceder_a_empleados(): void
    {
        $response = $this->get('/empleados');

        $response->assertRedirect('/login');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Áreas (requiere rol Administrador | RRHH | permiso manage_areas)
    // ─────────────────────────────────────────────────────────────────────────

    public function test_administrador_puede_acceder_a_gestion_de_areas(): void
    {
        $user = $this->crearUsuario('Administrador', ['access_dashboard', 'manage_areas']);

        $response = $this->actingAs($user)->get('/areas');

        $response->assertStatus(200);
    }

    public function test_empleado_sin_permiso_no_puede_acceder_a_gestion_de_areas(): void
    {
        $user = $this->crearUsuario('Empleado', ['access_dashboard']);

        $response = $this->actingAs($user)->get('/areas');

        $response->assertStatus(403);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Seguridad/Usuarios (requiere rol Administrador | permiso assign_roles)
    // ─────────────────────────────────────────────────────────────────────────

    public function test_administrador_puede_acceder_a_gestion_de_usuarios(): void
    {
        $user = $this->crearUsuario('Administrador', ['access_dashboard', 'assign_roles']);

        $response = $this->actingAs($user)->get('/seguridad');

        $response->assertStatus(200);
    }

    public function test_empleado_sin_permiso_no_puede_acceder_a_gestion_de_usuarios(): void
    {
        $user = $this->crearUsuario('Empleado', ['access_dashboard']);

        $response = $this->actingAs($user)->get('/seguridad');

        $response->assertStatus(403);
    }

    public function test_guest_es_redirigido_a_login_al_acceder_a_seguridad(): void
    {
        $response = $this->get('/seguridad');

        $response->assertRedirect('/login');
    }
}
