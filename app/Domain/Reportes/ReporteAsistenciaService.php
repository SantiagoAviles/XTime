<?php

namespace App\Domain\Reportes;

use App\Models\Empleado;
use App\Models\Marcacion;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class ReporteAsistenciaService
{
    /**
     * Genera la matriz consolidada por empleado para el período.
     *
     * Filtros admitidos:
     *  - desde, hasta (YYYY-MM-DD)
     *  - area_id
     *  - turno_id
     *  - empleado_id
     *  - solo_area_id (fuerza el área del Supervisor, ignora area_id si se pasa otro valor)
     */
    public function consolidadoPorEmpleado(array $filtros): array
    {
        [$desde, $hasta] = $this->rango($filtros);

        $empleados = Empleado::query()
            ->with('area')
            ->whereNull('deleted_at')
            ->when($filtros['area_id'] ?? null, fn ($q, $id) => $q->where('area_id', $id))
            ->when($filtros['empleado_id'] ?? null, fn ($q, $id) => $q->where('id', $id))
            ->when($filtros['solo_area_id'] ?? null, fn ($q, $id) => $q->where('area_id', $id))
            ->orderBy('apellidos')
            ->get();

        $marcaciones = Marcacion::query()
            ->whereBetween('fecha', [$desde->toDateString(), $hasta->toDateString()])
            ->when($filtros['turno_id'] ?? null, fn ($q, $id) => $q->where('turno_id', $id))
            ->whereIn('empleado_id', $empleados->pluck('id'))
            ->get()
            ->groupBy('empleado_id');

        return $empleados->map(function (Empleado $e) use ($marcaciones, $desde, $hasta) {
            $m = $marcaciones->get($e->id, collect());
            return [
                'empleado_id'      => $e->id,
                'dni'              => $e->dni,
                'nombre'           => $e->nombreCompleto(),
                'area'             => $e->area?->nombre ?? '—',
                'cargo'            => $e->cargo,
                'desde'            => $desde->toDateString(),
                'hasta'            => $hasta->toDateString(),
                'dias_normales'    => $m->where('estado', Marcacion::ESTADO_NORMAL)->count(),
                'dias_tardanza'    => $m->where('estado', Marcacion::ESTADO_TARDANZA)->count(),
                'dias_ausencia'    => $m->where('estado', Marcacion::ESTADO_AUSENCIA)->count(),
                'dias_justificada' => $m->where('estado', Marcacion::ESTADO_JUSTIFICADA)->count(),
                'horas_trabajadas' => round($m->sum('horas_trabajadas'), 2),
                'horas_extra'      => round($m->sum('horas_extra'), 2),
            ];
        })->all();
    }

    public function horasExtraPorEmpleado(array $filtros): array
    {
        return collect($this->consolidadoPorEmpleado($filtros))
            ->filter(fn ($r) => $r['horas_extra'] > 0)
            ->sortByDesc('horas_extra')
            ->values()
            ->all();
    }

    public function incidencias(array $filtros): Collection
    {
        [$desde, $hasta] = $this->rango($filtros);

        return Marcacion::query()
            ->with(['empleado.area', 'justificaciones.revisor'])
            ->whereBetween('fecha', [$desde->toDateString(), $hasta->toDateString()])
            ->whereIn('estado', [Marcacion::ESTADO_TARDANZA, Marcacion::ESTADO_AUSENCIA, Marcacion::ESTADO_JUSTIFICADA])
            ->when($filtros['area_id'] ?? null, function ($q, $id) {
                $q->whereHas('empleado', fn ($qq) => $qq->where('area_id', $id));
            })
            ->when($filtros['solo_area_id'] ?? null, function ($q, $id) {
                $q->whereHas('empleado', fn ($qq) => $qq->where('area_id', $id));
            })
            ->orderByDesc('fecha')
            ->get();
    }

    /**
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}
     */
    private function rango(array $filtros): array
    {
        $desde = isset($filtros['desde'])
            ? CarbonImmutable::parse($filtros['desde'])
            : CarbonImmutable::now()->startOfMonth();
        $hasta = isset($filtros['hasta'])
            ? CarbonImmutable::parse($filtros['hasta'])
            : CarbonImmutable::now()->endOfMonth();

        return [$desde, $hasta];
    }
}
