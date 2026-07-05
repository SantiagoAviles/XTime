<?php

namespace App\Domain\Autogestion;

use App\Models\Empleado;
use App\Models\Marcacion;
use Carbon\CarbonImmutable;

class ResumenMensualService
{
    public function resumen(Empleado $empleado, int $año, int $mes): array
    {
        $desde = CarbonImmutable::create($año, $mes, 1)->startOfMonth();
        $hasta = $desde->endOfMonth();

        $marcaciones = Marcacion::query()
            ->where('empleado_id', $empleado->id)
            ->whereBetween('fecha', [$desde->toDateString(), $hasta->toDateString()])
            ->get();

        return [
            'periodo'              => $desde->format('m/Y'),
            'dias_normales'        => $marcaciones->where('estado', Marcacion::ESTADO_NORMAL)->count(),
            'dias_tardanza'        => $marcaciones->where('estado', Marcacion::ESTADO_TARDANZA)->count(),
            'dias_ausencia'        => $marcaciones->where('estado', Marcacion::ESTADO_AUSENCIA)->count(),
            'dias_justificada'     => $marcaciones->where('estado', Marcacion::ESTADO_JUSTIFICADA)->count(),
            'horas_trabajadas'     => round($marcaciones->sum('horas_trabajadas'), 2),
            'horas_extra'          => round($marcaciones->sum('horas_extra'), 2),
        ];
    }
}
