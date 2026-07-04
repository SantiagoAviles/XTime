<?php

namespace App\Domain\Horarios;

use App\Models\ReglaHoraExtra;
use App\Models\Turno;

class CalculadoraHorasExtraService
{
    public function calcular(Turno $turno, float $horasTrabajadas): array
    {
        $jornadaTeorica = $turno->duracionEnHoras();
        $extras = max(0, $horasTrabajadas - $jornadaTeorica);

        $regla = ReglaHoraExtra::vigente() ?? new ReglaHoraExtra([
            'recargo_primeras_horas' => 25.00,
            'umbral_primeras_horas'  => 2,
            'recargo_adicional'      => 35.00,
        ]);

        $primeras   = min($extras, $regla->umbral_primeras_horas);
        $adicionales = max(0, $extras - $regla->umbral_primeras_horas);

        return [
            'horas_jornada'      => round($jornadaTeorica, 2),
            'horas_extra_total'  => round($extras, 2),
            'horas_primeras'     => round($primeras, 2),
            'horas_adicionales'  => round($adicionales, 2),
            'recargo_primeras'   => (float) $regla->recargo_primeras_horas,
            'recargo_adicional'  => (float) $regla->recargo_adicional,
        ];
    }
}
