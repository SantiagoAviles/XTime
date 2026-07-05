<?php

namespace Database\Seeders;

use App\Models\ReglaHoraExtra;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReglaHoraExtraSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        ReglaHoraExtra::firstOrCreate(
            ['nombre' => 'Ley N° 27671'],
            [
                'recargo_primeras_horas' => 25.00,
                'umbral_primeras_horas'  => 2,
                'recargo_adicional'      => 35.00,
                'aplica_nocturno'        => true,
                'is_active'              => true,
            ]
        );
    }
}
