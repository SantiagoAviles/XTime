@extends('layouts.app')

@section('title', 'Empleados')
@section('section_label', 'Sprint 2')
@section('page_title', 'Gestión de empleados')
@section('page_description', 'Listado de empleados con filtros por área, estado y búsqueda.')

@section('content')
    <div class="dashboard-card p-4 mb-4">
        <form method="GET" action="{{ route('empleados.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Buscar</label>
                <input type="text" name="busqueda" value="{{ $filtros['busqueda'] ?? '' }}"
                       class="form-control" placeholder="Nombre, apellido o DNI">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Área</label>
                <select name="area_id" class="form-select">
                    <option value="">Todas</option>
                    @foreach ($areas as $area)
                        <option value="{{ $area->id }}" @selected(($filtros['area_id'] ?? null) == $area->id)>
                            {{ $area->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="activo"   @selected(($filtros['estado'] ?? null) === 'activo')>Activo</option>
                    <option value="inactivo" @selected(($filtros['estado'] ?? null) === 'inactivo')>Inactivo</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary flex-grow-1" type="submit">Filtrar</button>
                <a href="{{ route('empleados.index') }}" class="btn btn-outline-secondary">Limpiar</a>
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input type="checkbox" name="incluir_inactivos" value="1" id="incluir_inactivos"
                           class="form-check-input" @checked($filtros['incluir_inactivos'] ?? false)>
                    <label class="form-check-label small" for="incluir_inactivos">
                        Incluir empleados dados de baja
                    </label>
                </div>
            </div>
        </form>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="h6 mb-0">{{ $empleados->total() }} empleados</h3>
        @can('create_employees')
            <a href="{{ route('empleados.create') }}" class="btn btn-sm btn-primary">+ Nuevo empleado</a>
        @endcan
    </div>

    <div class="dashboard-card p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>DNI</th>
                        <th>Área</th>
                        <th>Cargo</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($empleados as $empleado)
                    <tr>
                        <td>{{ $empleado->nombreCompleto() }}</td>
                        <td><code>{{ $empleado->dni }}</code></td>
                        <td>{{ $empleado->area?->nombre ?? '—' }}</td>
                        <td>{{ $empleado->cargo ?? '—' }}</td>
                        <td>
                            @if ($empleado->trashed())
                                <span class="badge text-bg-secondary">Dado de baja</span>
                            @elseif ($empleado->estado === 'activo')
                                <span class="badge text-bg-success">Activo</span>
                            @else
                                <span class="badge text-bg-warning">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('empleados.show', $empleado) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                            @can('edit_employees')
                                @unless ($empleado->trashed())
                                    <a href="{{ route('empleados.edit', $empleado) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                @endunless
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Sin empleados que mostrar.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $empleados->links() }}</div>
@endsection
