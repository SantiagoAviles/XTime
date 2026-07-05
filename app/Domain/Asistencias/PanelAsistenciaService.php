<?php

namespace App\Domain\Asistencias;

use App\Models\Empleado;
use App\Models\Marcacion;
use Carbon\CarbonImmutable;

class PanelAsistenciaService
{
    /**
     * Devuelve la foto del día: una fila por empleado activo
     * con su estado (Presente / Pendiente / Ausente / Tardanza).
     *
     * @param  int|null  $areaId  Filtra por área (Supervisor).
     */
    public function snapshotDelDia(?int $areaId = null, ?CarbonImmutable $fecha = null): array
    {
        $fecha = $fecha ?? CarbonImmutable::today();

        $empleados = Empleado::query()
            ->with(['area'])
            ->whereNull('deleted_at')
            ->where('estado', 'activo')
            ->when($areaId, fn ($q) => $q->where('area_id', $areaId))
            ->orderBy('apellidos')
            ->get();

        $marcaciones = Marcacion::query()
            ->whereDate('fecha', $fecha->toDateString())
            ->whereIn('empleado_id', $empleados->pluck('id'))
            ->get()
            ->keyBy('empleado_id');

        return $empleados->map(function (Empleado $e) use ($marcaciones) {
            $m = $marcaciones->get($e->id);

            return [
                'empleado_id'    => $e->id,
                'nombre'         => $e->nombreCompleto(),
                'dni'            => $e->dni,
                'area'           => $e->area?->nombre,
                'estado'         => $this->resolverEstado($m),
                'hora_entrada'   => $m?->hora_entrada?->format('H:i'),
                'hora_salida'    => $m?->hora_salida?->format('H:i'),
                'horas_trabajadas' => $m?->horas_trabajadas,
                'horas_extra'      => $m?->horas_extra,
            ];
        })->all();
    }

    private function resolverEstado(?Marcacion $m): string
    {
        if (! $m) {
            return 'pendiente';
        }

        if ($m->estado === Marcacion::ESTADO_AUSENCIA) {
            return 'ausencia';
        }

        if ($m->estado === Marcacion::ESTADO_JUSTIFICADA) {
            return 'justificada';
        }

        if ($m->estado === Marcacion::ESTADO_TARDANZA) {
            return 'tardanza';
        }

        return $m->tieneSalida() ? 'salida' : 'presente';
    }
}
