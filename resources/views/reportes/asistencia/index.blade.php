@extends('layouts.app')
@section('title', 'Reporte de asistencia')
@section('content')
    <x-page-header title="Reporte mensual de asistencia"
                   description="Filtra por período, área, turno o empleado. Exporta a PDF o Excel."
                   badge="Sprint 9" />

    <form method="GET" action="{{ route('reportes.asistencia') }}" class="dashboard-card p-3 mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small">Desde</label>
                <input type="date" name="desde" value="{{ $filtros['desde'] ?? now()->startOfMonth()->toDateString() }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Hasta</label>
                <input type="date" name="hasta" value="{{ $filtros['hasta'] ?? now()->endOfMonth()->toDateString() }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Área</label>
                <select name="area_id" class="form-select">
                    <option value="">Todas</option>
                    @foreach ($areas as $a)
                        <option value="{{ $a->id }}" @selected(($filtros['area_id'] ?? null) == $a->id)>{{ $a->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Turno</label>
                <select name="turno_id" class="form-select">
                    <option value="">Todos</option>
                    @foreach ($turnos as $t)
                        <option value="{{ $t->id }}" @selected(($filtros['turno_id'] ?? null) == $t->id)>{{ $t->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button name="previsualizar" value="1" class="btn btn-outline-primary flex-grow-1">👁 Previsualizar</button>
            </div>
        </div>
        <div class="d-flex gap-2 mt-3">
            <a class="btn btn-danger"
               href="{{ route('reportes.asistencia.pdf', $filtros) }}">⬇ PDF</a>
            <a class="btn btn-success"
               href="{{ route('reportes.asistencia.excel', $filtros) }}">⬇ Excel</a>
        </div>
    </form>

    @if (! empty($filas))
        <div class="dashboard-card p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>DNI</th><th>Empleado</th><th>Área</th>
                            <th class="text-end">Normales</th>
                            <th class="text-end">Tardanzas</th>
                            <th class="text-end">Ausencias</th>
                            <th class="text-end">Justif.</th>
                            <th class="text-end">Horas</th>
                            <th class="text-end">Extra</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($filas as $r)
                        <tr>
                            <td><code>{{ $r['dni'] }}</code></td>
                            <td>{{ $r['nombre'] }}</td>
                            <td>{{ $r['area'] }}</td>
                            <td class="text-end">{{ $r['dias_normales'] }}</td>
                            <td class="text-end">{{ $r['dias_tardanza'] }}</td>
                            <td class="text-end">{{ $r['dias_ausencia'] }}</td>
                            <td class="text-end">{{ $r['dias_justificada'] }}</td>
                            <td class="text-end">{{ $r['horas_trabajadas'] }}</td>
                            <td class="text-end">{{ $r['horas_extra'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <p class="text-muted small">Aplica filtros y presiona "Previsualizar" para ver datos antes de descargar.</p>
    @endif
@endsection
