<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permisos = [
            'access_dashboard',
            'view_employees', 'create_employees', 'edit_employees', 'delete_employees',
            'manage_areas',
            'assign_roles',
            'view_activity_log',
            'manage_turnos', 'view_turnos',
            'manage_feriados', 'manage_reglas_horas_extra',
            'mark_attendance_self', 'mark_attendance_manual',
            'view_attendance_panel', 'view_attendance_own_area',
            'view_attendance_history',
            'view_self_portal', 'request_justifications',
            'approve_justifications', 'manage_justifications',
            'view_reports', 'view_reports_own_area', 'view_self_report',
        ];

        foreach ($permisos as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        $roles = [
            'Administrador' => $permisos,
            'RRHH' => [
                'access_dashboard',
                'view_employees', 'create_employees', 'edit_employees', 'delete_employees',
                'manage_areas',
                'manage_turnos', 'view_turnos', 'manage_feriados', 'manage_reglas_horas_extra',
                'mark_attendance_manual',
                'view_attendance_panel', 'view_attendance_history',
                'manage_justifications',
                'view_reports',
            ],
            'Supervisor' => [
                'access_dashboard',
                'view_employees',
                'view_turnos',
                'view_attendance_own_area',
                'approve_justifications',
                'view_reports_own_area',
            ],
            'Jefe de Operaciones' => [
                'access_dashboard',
                'view_employees',
                'view_turnos',
                'view_attendance_panel',
                'view_reports',
            ],
            'Empleado' => [
                'view_self_portal',
                'mark_attendance_self',
                'request_justifications',
                'view_self_report',
            ],
        ];

        foreach ($roles as $rolNombre => $perms) {
            $rol = Role::firstOrCreate(['name' => $rolNombre, 'guard_name' => 'web']);
            $rol->syncPermissions($perms);
        }
    }
}
