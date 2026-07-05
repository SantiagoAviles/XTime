@extends('layouts.app')
@section('title', 'Reporte de incidencias')
@section('content')
    <x-page-header title="Reporte de incidencias y tardanzas"
                   description="Detalle con estado de cada justificación."
                   badge="Sprint 9" />

    <form method="GET" class="dashboard-card p-3 mb-3 row g-2 align-items-end">
        <div class="col-md-2">
            <label class="form-label small">Desde</label>
            <input type="date" name="desde" value="{{ $filtros['desde'] ?? now()->startOfMonth()->toDateString() }}" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label small">Hasta</label>
            <input type="date" name="hasta" value="{{ $filtros['hasta'] ?? now()->endOfMonth()->toDateString() }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label small">Área</label>
            <select name="area_id" class="form-select">
                <option value="">Todas</option>
                @foreach ($areas as $a)
                    <option value="{{ $a->id }}" @selected(($filtros['area_id'] ?? null) == $a->id)>{{ $a->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Filtrar</button></div>
    </form>

    <div class="dashboard-card p-0">
        <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>Empleado</th>
                    <th>Área</th>
                    <th>Estado</th>
                    <th>Justificación</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($incidencias as $m)
                <tr>
                    <td>{{ $m->fecha->format('d/m/Y') }}</td>
                    <td>{{ $m->empleado?->nombreCompleto() }}</td>
                    <td>{{ $m->empleado?->area?->nombre ?? '—' }}</td>
                    <td><x-status-badge :status="$m->estado" /></td>
                    <td>
                        @php $j = $m->justificaciones->first(); @endphp
                        @if ($j)
                            <x-status-badge :status="$j->estado" />
                            <small class="text-muted ms-2">por {{ $j->revisor?->name ?? 'Pendiente' }}</small>
                        @else
                            <small class="text-muted">Sin solicitud</small>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Sin incidencias en el período.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
