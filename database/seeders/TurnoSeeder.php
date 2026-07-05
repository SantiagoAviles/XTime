<?php

namespace Database\Seeders;

use App\Models\Turno;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TurnoSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $turnos = [
            [
                'nombre' => 'Diurno estándar',
                'tipo' => 'diurno',
                'hora_entrada' => '08:00',
                'hora_salida' => '17:00',
                'tolerancia_minutos' => 10,
                'cruza_medianoche' => false,
                'descripcion' => 'Turno administrativo 08:00 a 17:00 con tolerancia de 10 minutos.',
            ],
            [
                'nombre' => 'Diurno operaciones',
                'tipo' => 'diurno',
                'hora_entrada' => '07:00',
                'hora_salida' => '16:00',
                'tolerancia_minutos' => 5,
                'cruza_medianoche' => false,
                'descripcion' => 'Turno de operaciones 07:00 a 16:00 con tolerancia ajustada.',
            ],
            [
                'nombre' => 'Nocturno',
                'tipo' => 'nocturno',
                'hora_entrada' => '22:00',
                'hora_salida' => '06:00',
                'tolerancia_minutos' => 10,
                'cruza_medianoche' => true,
                'descripcion' => 'Turno nocturno 22:00 a 06:00 del día siguiente.',
            ],
            [
                'nombre' => 'Rotativo A/B',
                'tipo' => 'rotativo',
                'hora_entrada' => '08:00',
                'hora_salida' => '17:00',
                'tolerancia_minutos' => 10,
                'cruza_medianoche' => false,
                'patron_rotativo' => ['semana_a' => 'mañana', 'semana_b' => 'tarde'],
                'descripcion' => 'Rotación semanal A (mañana) / B (tarde) según calendario.',
            ],
        ];

        foreach ($turnos as $t) {
            Turno::firstOrCreate(['nombre' => $t['nombre']], $t);
        }
    }
}
