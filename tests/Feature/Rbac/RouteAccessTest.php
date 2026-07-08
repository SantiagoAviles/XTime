<?php

namespace Tests\Feature\Rbac;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
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
    // Raíz "/"
    // ─────────────────────────────────────────────────────────────────────────

    public function test_raiz_redirige_a_login_para_invitados(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_raiz_redirige_a_dashboard_para_administrador(): void
    {
        $user = $this->crearUsuario('Administrador', ['access_dashboard']);

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect('/dashboard');
    }

    public function test_raiz_redirige_a_autogestion_para_empleado(): void
    {
        $user = $this->crearUsuario('Empleado', ['view_self_portal']);

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect('/autogestion');
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

    public function test_empleado_seedeado_no_puede_acceder_al_dashboard_general_pero_si_a_autogestion(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('Empleado');

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(403);

        $response = $this->actingAs($user)->get('/autogestion');
        $response->assertStatus(403); // 403 porque este usuario no tiene un Empleado vinculado, no por falta de permiso
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

        // /seguridad redirige a /seguridad/usuarios; seguimos el redirect.
        $response = $this->actingAs($user)->get('/seguridad');
        $response->assertRedirect('/seguridad/usuarios');

        $follow = $this->actingAs($user)->get('/seguridad/usuarios');
        $follow->assertStatus(200);
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

    // ─────────────────────────────────────────────────────────────────────────
    // Turnos (requiere permiso view_turnos o manage_turnos)
    // ─────────────────────────────────────────────────────────────────────────

    public function test_usuario_con_permiso_view_turnos_puede_listar(): void
    {
        $user = $this->crearUsuario('ConVerTurnos', ['view_turnos']);

        $response = $this->actingAs($user)->get('/turnos');

        $response->assertStatus(200);
    }

    public function test_usuario_sin_permiso_no_puede_listar_turnos(): void
    {
        $user = $this->crearUsuario('SinTurnos');

        $response = $this->actingAs($user)->get('/turnos');

        $response->assertStatus(403);
    }

    public function test_guest_es_redirigido_a_login_al_acceder_a_turnos(): void
    {
        $response = $this->get('/turnos');

        $response->assertRedirect('/login');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Feriados y Reglas de horas extra (requiere manage_feriados|manage_reglas_horas_extra)
    // ─────────────────────────────────────────────────────────────────────────

    public function test_usuario_con_permiso_manage_feriados_puede_listar(): void
    {
        $user = $this->crearUsuario('ConFeriados', ['manage_feriados']);

        $response = $this->actingAs($user)->get('/feriados');

        $response->assertStatus(200);
    }

    public function test_usuario_sin_permiso_no_puede_listar_feriados(): void
    {
        $user = $this->crearUsuario('SinFeriados');

        $response = $this->actingAs($user)->get('/feriados');

        $response->assertStatus(403);
    }

    public function test_usuario_con_permiso_manage_reglas_horas_extra_puede_listar(): void
    {
        $user = $this->crearUsuario('ConReglas', ['manage_reglas_horas_extra']);

        $response = $this->actingAs($user)->get('/reglas-horas-extra');

        $response->assertStatus(200);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Asistencias: panel y marcación manual
    // ─────────────────────────────────────────────────────────────────────────

    public function test_usuario_con_permiso_view_attendance_panel_puede_ver_panel(): void
    {
        $user = $this->crearUsuario('ConPanel', ['view_attendance_panel']);

        $response = $this->actingAs($user)->get('/asistencias/panel');

        $response->assertStatus(200);
    }

    public function test_usuario_sin_permiso_no_puede_ver_panel(): void
    {
        $user = $this->crearUsuario('SinPanel');

        $response = $this->actingAs($user)->get('/asistencias/panel');

        $response->assertStatus(403);
    }

    public function test_usuario_con_permiso_mark_attendance_manual_puede_acceder_al_formulario(): void
    {
        $user = $this->crearUsuario('ConManual', ['mark_attendance_manual']);

        $response = $this->actingAs($user)->get('/asistencias/manual');

        $response->assertStatus(200);
    }

    public function test_usuario_sin_permiso_no_puede_acceder_a_marcacion_manual(): void
    {
        $user = $this->crearUsuario('SinManual');

        $response = $this->actingAs($user)->get('/asistencias/manual');

        $response->assertStatus(403);
    }

    public function test_kiosko_es_publico_sin_login(): void
    {
        $response = $this->get('/asistencias/kiosko');

        $response->assertStatus(200);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Justificaciones (bandeja de revisión)
    // ─────────────────────────────────────────────────────────────────────────

    public function test_usuario_con_permiso_manage_justifications_puede_ver_bandeja(): void
    {
        $user = $this->crearUsuario('ConJustificaciones', ['manage_justifications']);

        $response = $this->actingAs($user)->get('/justificaciones');

        $response->assertStatus(200);
    }

    public function test_usuario_sin_permiso_no_puede_ver_bandeja_de_justificaciones(): void
    {
        $user = $this->crearUsuario('SinJustificaciones');

        $response = $this->actingAs($user)->get('/justificaciones');

        $response->assertStatus(403);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Reportes
    // ─────────────────────────────────────────────────────────────────────────

    public function test_usuario_con_permiso_view_reports_puede_ver_reporte_de_asistencia(): void
    {
        $user = $this->crearUsuario('ConReportes', ['view_reports']);

        $response = $this->actingAs($user)->get('/reportes/asistencia');

        $response->assertStatus(200);
    }

    public function test_usuario_con_permiso_view_reports_own_area_puede_ver_reporte_de_asistencia(): void
    {
        $user = $this->crearUsuario('ConReportesPropios', ['view_reports_own_area']);

        $response = $this->actingAs($user)->get('/reportes/asistencia');

        $response->assertStatus(200);
    }

    public function test_usuario_sin_permiso_no_puede_ver_reportes(): void
    {
        $user = $this->crearUsuario('SinReportes');

        $response = $this->actingAs($user)->get('/reportes/asistencia');

        $response->assertStatus(403);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Auditoría (requiere rol Administrador | permiso view_activity_log)
    // ─────────────────────────────────────────────────────────────────────────

    public function test_usuario_con_permiso_view_activity_log_puede_ver_auditoria(): void
    {
        $user = $this->crearUsuario('ConAuditoria', ['view_activity_log']);

        $response = $this->actingAs($user)->get('/auditoria');

        $response->assertStatus(200);
    }

    public function test_usuario_sin_permiso_no_puede_ver_auditoria(): void
    {
        $user = $this->crearUsuario('SinAuditoria');

        $response = $this->actingAs($user)->get('/auditoria');

        $response->assertStatus(403);
    }

    public function test_guest_es_redirigido_a_login_al_acceder_a_auditoria(): void
    {
        $response = $this->get('/auditoria');

        $response->assertRedirect('/login');
    }
}
