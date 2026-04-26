<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Empleado;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@altermec.com'],
            [
                'name'     => 'Administrador Sistema',
                'password' => Hash::make('Admin1234!'),
                'is_active' => true,
            ]
        );

        $user->assignRole('Administrador');

        $area = Area::where('nombre', 'Administración')->first();

        if (! $user->empleado) {
            Empleado::create([
                'user_id'       => $user->id,
                'area_id'       => $area?->id,
                'nombres'       => 'Administrador',
                'apellidos'     => 'Sistema',
                'dni'           => '00000000',
                'cargo'         => 'Administrador del Sistema',
                'fecha_ingreso' => now()->toDateString(),
                'estado'        => 'activo',
            ]);
        }
    }
}
