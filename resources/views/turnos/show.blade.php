@extends('layouts.app')
@section('title', 'Turno · ' . $turno->nombre)
@section('content')
    <x-page-header :title="$turno->nombre"
                   :description="ucfirst($turno->tipo) . ' · ' . \Carbon\Carbon::parse($turno->hora_entrada)->format('H:i') . ' → ' . \Carbon\Carbon::parse($turno->hora_salida)->format('H:i')"
                   badge="Turnos" />

    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="dashboard-card p-4">
                <h3 class="h6 mb-3">Asignaciones vigentes</h3>
                @php
                    $vigentes = $turno->asignaciones->filter(fn ($a) =>
                        is_null($a->vigente_hasta) || $a->vigente_hasta->isFuture()
                    );
                @endphp
                @if ($vigentes->isEmpty())
                    <p class="text-muted small mb-0">Sin asignaciones vigentes.</p>
                @else
                    <ul class="list-group list-group-flush small">
                        @foreach ($vigentes as $a)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>{{ $a->empleado?->nombreCompleto() ?? '—' }}</span>
                                <span class="text-muted">
                                    Desde {{ optional($a->vigente_desde)->format('d/m/Y') }}
                                    @if ($a->vigente_hasta) → {{ $a->vigente_hasta->format('d/m/Y') }} @endif
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="dashboard-card p-4">
                <h3 class="h6 mb-3">Detalles</h3>
                <dl class="row small mb-0">
                    <dt class="col-6">Duración</dt>
                    <dd class="col-6">{{ $turno->duracionEnHoras() }} h</dd>

                    <dt class="col-6">Tolerancia</dt>
                    <dd class="col-6">{{ $turno->tolerancia_minutos }} min</dd>

                    <dt class="col-6">Cruza medianoche</dt>
                    <dd class="col-6">{{ $turno->cruza_medianoche ? 'Sí' : 'No' }}</dd>
                </dl>
                @if ($turno->descripcion)
                    <hr>
                    <p class="small text-muted mb-0">{{ $turno->descripcion }}</p>
                @endif
            </div>

            @can('manage_turnos')
                <div class="dashboard-card p-4 mt-3">
                    <h3 class="h6 mb-3">Acciones</h3>
                    <a href="{{ route('turnos.edit', $turno) }}" class="btn btn-outline-primary btn-sm w-100 mb-2">Editar</a>
                    <form method="POST" action="{{ route('turnos.destroy', $turno) }}"
                          onsubmit="return confirm('¿Eliminar este turno?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm w-100">Eliminar</button>
                    </form>
                </div>
            @endcan
        </div>
    </div>
@endsection
