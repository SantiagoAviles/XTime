<?php

namespace Database\Seeders;

use App\Models\Feriado;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeriadoSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $year = (int) now()->year;

        $feriados = [
            ['fecha' => "$year-01-01", 'descripcion' => 'Año Nuevo',                      'nacional' => true],
            ['fecha' => "$year-05-01", 'descripcion' => 'Día del Trabajo',                'nacional' => true],
            ['fecha' => "$year-06-29", 'descripcion' => 'San Pedro y San Pablo',          'nacional' => true],
            ['fecha' => "$year-07-28", 'descripcion' => 'Fiestas Patrias',                'nacional' => true],
            ['fecha' => "$year-07-29", 'descripcion' => 'Fiestas Patrias',                'nacional' => true],
            ['fecha' => "$year-08-30", 'descripcion' => 'Santa Rosa de Lima',             'nacional' => true],
            ['fecha' => "$year-10-08", 'descripcion' => 'Combate de Angamos',             'nacional' => true],
            ['fecha' => "$year-11-01", 'descripcion' => 'Día de Todos los Santos',        'nacional' => true],
            ['fecha' => "$year-12-08", 'descripcion' => 'Inmaculada Concepción',          'nacional' => true],
            ['fecha' => "$year-12-25", 'descripcion' => 'Navidad',                        'nacional' => true],
        ];

        foreach ($feriados as $f) {
            Feriado::firstOrCreate(['fecha' => $f['fecha']], $f + ['is_active' => true]);
        }
    }
}
