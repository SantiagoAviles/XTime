<?php

namespace App\Domain\Horarios;

use App\Models\AsignacionTurno;
use App\Models\Empleado;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class AsignacionTurnoService
{
    public function asignar(array $data, ?User $autor = null): AsignacionTurno
    {
        return DB::transaction(function () use ($data, $autor) {
            $empleado = Empleado::findOrFail($data['empleado_id']);
            $desde = CarbonImmutable::parse($data['vigente_desde']);

            $this->cerrarAsignacionesVigentes($empleado, $desde);

            $asignacion = AsignacionTurno::create([
                'empleado_id'          => $empleado->id,
                'turno_id'             => $data['turno_id'],
                'vigente_desde'        => $desde->toDateString(),
                'vigente_hasta'        => $data['vigente_hasta'] ?? null,
                'asignada_por_user_id' => $autor?->id,
                'observacion'          => $data['observacion'] ?? null,
            ]);

            activity('asignaciones_turno')
                ->causedBy($autor)
                ->performedOn($asignacion)
                ->withProperties([
                    'empleado_id' => $empleado->id,
                    'turno_id'    => $data['turno_id'],
                ])
                ->event('turno_asignado')
                ->log('turno_asignado');

            return $asignacion->fresh(['empleado', 'turno']);
        });
    }

    private function cerrarAsignacionesVigentes(Empleado $empleado, CarbonImmutable $nuevaDesde): void
    {
        $empleado->asignacionesTurno()
            ->where(function ($q) use ($nuevaDesde) {
                $q->whereNull('vigente_hasta')
                  ->orWhere('vigente_hasta', '>=', $nuevaDesde->toDateString());
            })
            ->where('vigente_desde', '<', $nuevaDesde->toDateString())
            ->update(['vigente_hasta' => $nuevaDesde->subDay()->toDateString()]);
    }
}
