@extends('layouts.app')

@section('title', 'Turnos')
@section('content')
    <x-page-header title="Catálogo de turnos"
                   description="Tipos de jornada de la empresa: diurno, nocturno, rotativo y flexible."
                   badge="Sprint 4" />

    <div class="dashboard-card p-4 mb-4">
        <form method="GET" action="{{ route('turnos.index') }}" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Buscar</label>
                <input type="text" name="busqueda" value="{{ $filtros['busqueda'] ?? '' }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Tipo</label>
                <select name="tipo" class="form-select">
                    <option value="">Todos</option>
                    @foreach (['diurno', 'nocturno', 'rotativo', 'flexible'] as $t)
                        <option value="{{ $t }}" @selected(($filtros['tipo'] ?? null) === $t)>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary flex-grow-1">Filtrar</button>
                @can('manage_turnos')
                    <a href="{{ route('turnos.create') }}" class="btn btn-success">+ Turno</a>
                @endcan
            </div>
        </form>
    </div>

    <div class="dashboard-card p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Horario</th>
                        <th>Tolerancia</th>
                        <th>Asignaciones</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($turnos as $turno)
                    <tr>
                        <td class="fw-semibold">{{ $turno->nombre }}</td>
                        <td><span class="badge text-bg-light text-dark border">{{ ucfirst($turno->tipo) }}</span></td>
                        <td>
                            {{ \Carbon\Carbon::parse($turno->hora_entrada)->format('H:i') }}
                            →
                            {{ \Carbon\Carbon::parse($turno->hora_salida)->format('H:i') }}
                            @if ($turno->cruza_medianoche)
                                <small class="text-muted">(+1 día)</small>
                            @endif
                        </td>
                        <td>{{ $turno->tolerancia_minutos }} min</td>
                        <td><span class="badge text-bg-light text-dark">{{ $turno->asignaciones_count }}</span></td>
                        <td>
                            @if ($turno->is_active)
                                <span class="badge text-bg-success">Activo</span>
                            @else
                                <span class="badge text-bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('turnos.show', $turno) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                            @can('manage_turnos')
                                <a href="{{ route('turnos.edit', $turno) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Sin turnos registrados.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $turnos->links() }}</div>
@endsection
