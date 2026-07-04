@extends('layouts.app')

@section('title', 'Áreas')
@section('section_label', 'Sprint 2')
@section('page_title', 'Áreas / Departamentos')
@section('page_description', 'Gestión de las áreas organizacionales.')

@section('content')
    <div class="dashboard-card p-4 mb-4">
        <form method="GET" action="{{ route('areas.index') }}" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Buscar por nombre</label>
                <input type="text" name="busqueda" value="{{ $filtros['busqueda'] ?? '' }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="activo"   @selected(($filtros['estado'] ?? null) === 'activo')>Activo</option>
                    <option value="inactivo" @selected(($filtros['estado'] ?? null) === 'inactivo')>Inactivo</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary flex-grow-1">Filtrar</button>
                <a href="{{ route('areas.index') }}" class="btn btn-outline-secondary">Limpiar</a>
            </div>
        </form>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="h6 mb-0">{{ $areas->total() }} áreas</h3>
        @can('manage_areas')
            <a href="{{ route('areas.create') }}" class="btn btn-sm btn-primary">+ Nueva área</a>
        @endcan
    </div>

    <div class="dashboard-card p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Empleados</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($areas as $area)
                    <tr>
                        <td class="fw-semibold">{{ $area->nombre }}</td>
                        <td class="text-secondary small">{{ \Illuminate\Support\Str::limit($area->descripcion, 80) }}</td>
                        <td><span class="badge text-bg-light text-dark">{{ $area->empleados_count }}</span></td>
                        <td>
                            @if ($area->is_active)
                                <span class="badge text-bg-success">Activo</span>
                            @else
                                <span class="badge text-bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('areas.show', $area) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                            @can('manage_areas')
                                <a href="{{ route('areas.edit', $area) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Sin áreas registradas.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $areas->links() }}</div>
@endsection
