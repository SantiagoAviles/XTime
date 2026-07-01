@extends('layouts.app')

@section('title', 'Usuarios y roles')
@section('section_label', 'Seguridad')
@section('page_title', 'Usuarios y roles del sistema')
@section('page_description', 'Asigna el rol de cada usuario. El cambio se aplica en la siguiente sesión.')

@section('content')
    <div class="dashboard-card p-4 mb-4">
        <form method="GET" action="{{ route('seguridad.usuarios.index') }}" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Buscar</label>
                <input type="text" name="busqueda" value="{{ $filtros['busqueda'] ?? '' }}"
                       class="form-control" placeholder="Nombre o correo">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Rol</label>
                <select name="rol" class="form-select">
                    <option value="">Todos</option>
                    @foreach ($roles as $r)
                        <option value="{{ $r->name }}" @selected(($filtros['rol'] ?? null) === $r->name)>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary flex-grow-1">Filtrar</button>
                <a href="{{ route('seguridad.usuarios.index') }}" class="btn btn-outline-secondary">Limpiar</a>
            </div>
        </form>
    </div>

    <div class="dashboard-card p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Área (empleado)</th>
                        <th>Rol actual</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($usuarios as $u)
                    <tr>
                        <td>{{ $u->name }}</td>
                        <td><small>{{ $u->email }}</small></td>
                        <td>{{ $u->empleado?->area?->nombre ?? '—' }}</td>
                        <td>
                            @foreach ($u->roles as $r)
                                <span class="badge text-bg-light text-dark border">{{ $r->name }}</span>
                            @endforeach
                            @if ($u->roles->isEmpty())
                                <span class="text-muted small">Sin rol</span>
                            @endif
                        </td>
                        <td>
                            @if ($u->is_active)
                                <span class="badge text-bg-success">Activo</span>
                            @else
                                <span class="badge text-bg-warning">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('seguridad.usuarios.edit', $u) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Sin usuarios.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $usuarios->links() }}</div>
@endsection
