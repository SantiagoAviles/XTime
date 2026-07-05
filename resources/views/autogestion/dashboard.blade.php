@extends('layouts.app')

@section('title', 'Mi portal')
@section('content')
    <x-page-header title="Hola, {{ $empleado->nombres }}"
                   :description="$empleado->cargo ?? 'Empleado'"
                   badge="Mi cuenta" />

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <x-stat-card label="Mis días asistidos" :value="$resumen['dias_normales']"
                         tone="success" icon="✓" />
        </div>
        <div class="col-md-6 col-xl-3">
            <x-stat-card label="Tardanzas" :value="$resumen['dias_tardanza']"
                         tone="warning" icon="!" />
        </div>
        <div class="col-md-6 col-xl-3">
            <x-stat-card label="Ausencias" :value="$resumen['dias_ausencia']"
                         tone="danger" icon="—" />
        </div>
        <div class="col-md-6 col-xl-3">
            <x-stat-card label="Horas extra mes" :value="$resumen['horas_extra']"
                         tone="info" icon="+" />
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <div class="dashboard-card p-4">
                <h3 class="h6 mb-3">Mi turno vigente</h3>
                @if ($turno)
                    <p class="mb-1"><strong>{{ $turno->nombre }}</strong> ({{ ucfirst($turno->tipo) }})</p>
                    <p class="mb-1">
                        Horario:
                        {{ \Carbon\Carbon::parse($turno->hora_entrada)->format('H:i') }} →
                        {{ \Carbon\Carbon::parse($turno->hora_salida)->format('H:i') }}
                    </p>
                    <p class="mb-0 text-muted small">Tolerancia: {{ $turno->tolerancia_minutos }} min</p>
                @else
                    <p class="text-muted mb-0">Sin turno asignado.</p>
                @endif
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="dashboard-card p-4">
                <h3 class="h6 mb-3">Mi marcación de hoy ({{ now()->format('d/m/Y') }})</h3>
                @if ($hoy)
                    <p class="mb-1"><strong>Estado:</strong> <x-status-badge :status="$hoy->estado" /></p>
                    <p class="mb-1"><strong>Entrada:</strong> {{ optional($hoy->hora_entrada)->format('H:i') ?? '—' }}</p>
                    <p class="mb-0"><strong>Salida:</strong> {{ optional($hoy->hora_salida)->format('H:i') ?? '—' }}</p>
                @else
                    <p class="text-muted mb-0">No has marcado todavía hoy.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="dashboard-card p-4 mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="h6 mb-1">Mi resumen mensual</h3>
                <p class="text-muted small mb-0">Período: {{ $resumen['periodo'] }} · Horas trabajadas: {{ $resumen['horas_trabajadas'] }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('autogestion.historial') }}" class="btn btn-outline-primary btn-sm">Ver historial</a>
                <a href="{{ route('autogestion.reporte') }}" class="btn btn-outline-secondary btn-sm">Descargar PDF</a>
            </div>
        </div>
    </div>
@endsection
