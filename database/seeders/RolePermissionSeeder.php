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

        $permissions = [
            'access_dashboard',
            'view_employees',
            'create_employees',
            'edit_employees',
            'delete_employees',
            'manage_areas',
            'assign_roles',
            'view_activity_log',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $roles = [
            'Administrador' => $permissions,
            'RRHH' => [
                'access_dashboard',
                'view_employees',
                'create_employees',
                'edit_employees',
                'delete_employees',
                'manage_areas',
            ],
            'Supervisor' => [
                'access_dashboard',
                'view_employees',
            ],
            'Jefe de Operaciones' => [
                'access_dashboard',
                'view_employees',
            ],
            'Empleado' => [
                'access_dashboard',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }
    }
}
