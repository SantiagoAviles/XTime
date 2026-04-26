<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $areas = [
            ['nombre' => 'Administración',      'descripcion' => 'Área de administración general de la empresa.'],
            ['nombre' => 'Recursos Humanos',     'descripcion' => 'Área encargada de la gestión del personal.'],
            ['nombre' => 'Operaciones',          'descripcion' => 'Área de control y coordinación de operaciones.'],
            ['nombre' => 'Mantenimiento',        'descripcion' => 'Área de mantenimiento de equipos e instalaciones.'],
            ['nombre' => 'Logística',            'descripcion' => 'Área de gestión de almacén, transporte y distribución.'],
        ];

        foreach ($areas as $area) {
            Area::firstOrCreate(['nombre' => $area['nombre']], $area);
        }
    }
}
