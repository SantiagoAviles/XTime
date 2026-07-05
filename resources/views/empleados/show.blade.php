@extends('layouts.app')

@section('title', 'Empleado · ' . $empleado->nombreCompleto())
@section('section_label', 'Empleados')
@section('page_title', $empleado->nombreCompleto())
@section('page_description', 'Detalle del empleado y su asignación actual.')

@section('content')
    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="dashboard-card p-4">
                <h3 class="h6 mb-3">Datos personales</h3>
                <dl class="row mb-0 small">
                    <dt class="col-sm-4 fw-semibold">DNI</dt>
                    <dd class="col-sm-8"><code>{{ $empleado->dni }}</code></dd>

                    <dt class="col-sm-4 fw-semibold">Área</dt>
                    <dd class="col-sm-8">{{ $empleado->area?->nombre ?? '—' }}</dd>

                    <dt class="col-sm-4 fw-semibold">Cargo</dt>
                    <dd class="col-sm-8">{{ $empleado->cargo ?? '—' }}</dd>

                    <dt class="col-sm-4 fw-semibold">Teléfono</dt>
                    <dd class="col-sm-8">{{ $empleado->telefono ?? '—' }}</dd>

                    <dt class="col-sm-4 fw-semibold">Fecha de ingreso</dt>
                    <dd class="col-sm-8">{{ optional($empleado->fecha_ingreso)->format('d/m/Y') ?? '—' }}</dd>

                    <dt class="col-sm-4 fw-semibold">Estado</dt>
                    <dd class="col-sm-8">
                        @if ($empleado->trashed())
                            <span class="badge text-bg-secondary">Dado de baja</span>
                        @elseif ($empleado->estado === 'activo')
                            <span class="badge text-bg-success">Activo</span>
                        @else
                            <span class="badge text-bg-warning">Inactivo</span>
                        @endif
                    </dd>
                </dl>
            </div>

            <div class="dashboard-card p-4 mt-4">
                <h3 class="h6 mb-3">Asignaciones de turno</h3>
                @if ($empleado->asignacionesTurno->isEmpty())
                    <p class="text-muted small mb-0">Sin turnos asignados todavía.</p>
                @else
                    <ul class="list-group list-group-flush small">
                        @foreach ($empleado->asignacionesTurno as $asig)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>{{ $asig->turno->nombre ?? '—' }}</span>
                                <span class="text-muted">
                                    Desde {{ optional($asig->vigente_desde)->format('d/m/Y') }}
                                    @if ($asig->vigente_hasta) → {{ $asig->vigente_hasta->format('d/m/Y') }} @endif
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="dashboard-card p-4">
                <h3 class="h6 mb-3">Usuario del sistema</h3>
                @if ($empleado->user)
                    <p class="mb-1 small"><strong>Correo:</strong> {{ $empleado->user->email }}</p>
                    <p class="mb-1 small"><strong>Rol:</strong> {{ $empleado->user->roles->first()?->name ?? 'Sin rol' }}</p>
                    <p class="mb-0 small"><strong>Activo:</strong> {{ $empleado->user->is_active ? 'Sí' : 'No' }}</p>
                @else
                    <p class="text-muted small mb-0">Sin usuario de sistema vinculado.</p>
                @endif
            </div>

            <div class="dashboard-card p-4 mt-4">
                <h3 class="h6 mb-3">Acciones</h3>
                @can('edit_employees')
                    @unless ($empleado->trashed())
                        <a href="{{ route('empleados.edit', $empleado) }}" class="btn btn-outline-primary btn-sm w-100 mb-2">Editar</a>
                    @endunless
                @endcan

                @if (auth()->user()->hasAnyRole(['Administrador', 'RRHH']))
                    @unless ($empleado->trashed())
                        <a href="{{ route('asistencias.empleados.qr', $empleado) }}" class="btn btn-outline-secondary btn-sm w-100 mb-2" target="_blank">
                            Ver código QR
                        </a>
                    @endunless
                @endif

                @can('delete_employees')
                    @if (! $empleado->trashed())
                        <form method="POST" action="{{ route('empleados.destroy', $empleado) }}"
                              onsubmit="return confirm('¿Dar de baja a este empleado?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm w-100">Dar de baja</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('empleados.restore', $empleado->id) }}">
                            @csrf
                            <button class="btn btn-outline-success btn-sm w-100">Reactivar</button>
                        </form>
                    @endif
                @endcan
            </div>
        </div>
    </div>
@endsection
