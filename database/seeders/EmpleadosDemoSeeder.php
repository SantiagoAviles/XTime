<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\AsignacionTurno;
use App\Models\Empleado;
use App\Models\Turno;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmpleadosDemoSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $demos = [
            [
                'email'    => 'rrhh@altermec.com',
                'password' => 'Rrhh1234!',
                'rol'      => 'RRHH',
                'area'     => 'Recursos Humanos',
                'turno'    => 'Diurno estándar',
                'nombres'  => 'Becsy Dalia',
                'apellidos'=> 'Rivero Flores',
                'dni'      => '11111111',
                'cargo'    => 'Jefa de Recursos Humanos',
            ],
            [
                'email'    => 'supervisor@altermec.com',
                'password' => 'Super1234!',
                'rol'      => 'Supervisor',
                'area'     => 'Operaciones',
                'turno'    => 'Diurno operaciones',
                'nombres'  => 'Carla',
                'apellidos'=> 'Mendoza Quispe',
                'dni'      => '22222222',
                'cargo'    => 'Supervisora de Operaciones',
            ],
            [
                'email'    => 'jefeops@altermec.com',
                'password' => 'Jefe1234!',
                'rol'      => 'Jefe de Operaciones',
                'area'     => 'Operaciones',
                'turno'    => 'Diurno operaciones',
                'nombres'  => 'Juan Pablo',
                'apellidos'=> 'García López',
                'dni'      => '33333333',
                'cargo'    => 'Jefe de Operaciones',
            ],
            [
                'email'    => 'empleado1@altermec.com',
                'password' => 'Emp1234!',
                'rol'      => 'Empleado',
                'area'     => 'Mantenimiento',
                'turno'    => 'Diurno estándar',
                'nombres'  => 'Pedro',
                'apellidos'=> 'Ramírez Soto',
                'dni'      => '44444444',
                'cargo'    => 'Técnico de Mantenimiento',
            ],
            [
                'email'    => 'empleado2@altermec.com',
                'password' => 'Emp1234!',
                'rol'      => 'Empleado',
                'area'     => 'Logística',
                'turno'    => 'Nocturno',
                'nombres'  => 'Lucía',
                'apellidos'=> 'Vargas Cruz',
                'dni'      => '55555555',
                'cargo'    => 'Operadora de Logística',
            ],
            [
                'email'    => 'empleado3@altermec.com',
                'password' => 'Emp1234!',
                'rol'      => 'Empleado',
                'area'     => 'Operaciones',
                'turno'    => 'Diurno operaciones',
                'nombres'  => 'Andrés',
                'apellidos'=> 'Torres Castillo',
                'dni'      => '66666666',
                'cargo'    => 'Operario Senior',
            ],
        ];

        foreach ($demos as $d) {
            $user = User::firstOrCreate(
                ['email' => $d['email']],
                [
                    'name'      => trim($d['nombres'] . ' ' . $d['apellidos']),
                    'password'  => Hash::make($d['password']),
                    'is_active' => true,
                ]
            );

            if (! $user->hasRole($d['rol'])) {
                $user->assignRole($d['rol']);
            }

            $area = Area::where('nombre', $d['area'])->first();

            $empleado = Empleado::firstOrCreate(
                ['dni' => $d['dni']],
                [
                    'user_id'       => $user->id,
                    'area_id'       => $area?->id,
                    'nombres'       => $d['nombres'],
                    'apellidos'     => $d['apellidos'],
                    'cargo'         => $d['cargo'],
                    'fecha_ingreso' => now()->subYears(2)->toDateString(),
                    'estado'        => 'activo',
                ]
            );

            $turno = Turno::where('nombre', $d['turno'])->first();

            if ($turno && ! $empleado->asignacionesTurno()->exists()) {
                AsignacionTurno::create([
                    'empleado_id'   => $empleado->id,
                    'turno_id'      => $turno->id,
                    'vigente_desde' => now()->subYears(1)->toDateString(),
                ]);
            }
        }
    }
}
