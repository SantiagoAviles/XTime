@extends('layouts.app')

@section('title', 'Mi historial')
@section('content')
    <x-page-header title="Mi historial de asistencia"
                   :description="'Mes: ' . str_pad($mes, 2, '0', STR_PAD_LEFT) . '/' . $año"
                   badge="Mi cuenta" />

    <form method="GET" class="dashboard-card p-3 mb-3 d-flex gap-2 align-items-end">
        <div>
            <label class="form-label small fw-semibold mb-1">Mes</label>
            <select name="mes" class="form-select">
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" @selected($mes == $m)>{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Año</label>
            <input type="number" name="año" min="2020" max="2100" value="{{ $año }}" class="form-control">
        </div>
        <button class="btn btn-primary">Consultar</button>
        <a href="{{ route('autogestion.reporte', ['mes' => $mes, 'año' => $año]) }}"
           class="btn btn-outline-secondary ms-auto">⬇ PDF</a>
    </form>

    <div class="row g-3 mb-3 small">
        <div class="col"><strong>Normales:</strong> {{ $resumen['dias_normales'] }}</div>
        <div class="col"><strong>Tardanzas:</strong> {{ $resumen['dias_tardanza'] }}</div>
        <div class="col"><strong>Ausencias:</strong> {{ $resumen['dias_ausencia'] }}</div>
        <div class="col"><strong>Justificadas:</strong> {{ $resumen['dias_justificada'] }}</div>
        <div class="col"><strong>Horas trabajadas:</strong> {{ $resumen['horas_trabajadas'] }}</div>
        <div class="col"><strong>Horas extra:</strong> {{ $resumen['horas_extra'] }}</div>
    </div>

    <div class="dashboard-card p-0">
        <table class="table table-sm mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th><th>Entrada</th><th>Salida</th>
                    <th>Horas</th><th>Extra</th><th>Estado</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($marcaciones as $m)
                <tr>
                    <td>{{ $m->fecha->format('d/m/Y') }}</td>
                    <td>{{ optional($m->hora_entrada)->format('H:i') ?? '—' }}</td>
                    <td>{{ optional($m->hora_salida)->format('H:i') ?? '—' }}</td>
                    <td>{{ $m->horas_trabajadas }}</td>
                    <td>{{ $m->horas_extra }}</td>
                    <td><x-status-badge :status="$m->estado" /></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Sin marcaciones en este período.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
