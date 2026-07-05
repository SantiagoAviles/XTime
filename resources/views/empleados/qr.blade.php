@extends('layouts.app')

@section('title', 'QR · ' . $empleado->nombreCompleto())
@section('content')
    <x-page-header title="Código QR del empleado"
                   :description="$empleado->nombreCompleto() . ' · DNI ' . $empleado->dni"
                   badge="QR" />

    <div class="dashboard-card p-4 text-center" style="max-width: 420px; margin: 0 auto;">
        <div class="my-3 d-inline-block p-3 bg-white border rounded">
            {!! $svg !!}
        </div>
        <p class="fw-semibold mb-1">{{ $empleado->nombreCompleto() }}</p>
        <p class="text-muted small mb-3">DNI {{ $empleado->dni }} · {{ $empleado->area?->nombre }}</p>
        <p class="text-muted" style="font-family: monospace; font-size: 0.8rem;">{{ $codigo }}</p>
        <button onclick="window.print()" class="btn btn-outline-primary mt-2">Imprimir</button>
    </div>

    <style>
        @media print {
            body * { visibility: hidden; }
            .dashboard-card, .dashboard-card * { visibility: visible; }
            .dashboard-card { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none; }
            button { display: none !important; }
        }
    </style>
@endsection
