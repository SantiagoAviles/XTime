@extends('layouts.app')
@section('title', 'Reporte de horas extra')
@section('content')
    <x-page-header title="Reporte de horas extra"
                   description="Detalle de horas extra acumuladas (Ley N° 27671)."
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
                    <th>DNI</th><th>Empleado</th><th>Área</th>
                    <th class="text-end">Horas trabajadas</th>
                    <th class="text-end">Horas extra</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($filas as $r)
                <tr>
                    <td><code>{{ $r['dni'] }}</code></td>
                    <td>{{ $r['nombre'] }}</td>
                    <td>{{ $r['area'] }}</td>
                    <td class="text-end">{{ $r['horas_trabajadas'] }}</td>
                    <td class="text-end fw-semibold">{{ $r['horas_extra'] }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Sin horas extra en el período.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
