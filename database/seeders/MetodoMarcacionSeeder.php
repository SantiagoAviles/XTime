<?php

namespace Database\Seeders;

use App\Models\MetodoMarcacion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MetodoMarcacionSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $metodos = [
            ['codigo' => MetodoMarcacion::CODIGO_QR,     'descripcion' => 'Marcación por código QR desde tablet'],
            ['codigo' => MetodoMarcacion::CODIGO_DNI,    'descripcion' => 'Marcación manual por DNI (fallback)'],
            ['codigo' => MetodoMarcacion::CODIGO_MANUAL, 'descripcion' => 'Marcación manual realizada por RRHH'],
        ];

        foreach ($metodos as $m) {
            MetodoMarcacion::firstOrCreate(
                ['codigo' => $m['codigo']],
                ['descripcion' => $m['descripcion'], 'is_active' => true]
            );
        }
    }
}
