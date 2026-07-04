@extends('layouts.app')

@section('title', 'Área · ' . $area->nombre)
@section('section_label', 'Áreas')
@section('page_title', $area->nombre)
@section('page_description', $area->descripcion ?? '')

@section('content')
    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="dashboard-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h6 mb-0">Empleados ({{ $area->empleados->count() }})</h3>
                </div>
                @if ($area->empleados->isEmpty())
                    <p class="text-muted small mb-0">Sin empleados asignados.</p>
                @else
                    <ul class="list-group list-group-flush small">
                        @foreach ($area->empleados as $emp)
                            <li class="list-group-item d-flex justify-content-between">
                                <a href="{{ route('empleados.show', $emp) }}">{{ $emp->nombreCompleto() }}</a>
                                <span class="text-muted">{{ $emp->cargo ?? '—' }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="dashboard-card p-4">
                <h3 class="h6 mb-3">Acciones</h3>
                @can('manage_areas')
                    <a href="{{ route('areas.edit', $area) }}" class="btn btn-outline-primary btn-sm w-100 mb-2">Editar</a>
                    <form method="POST" action="{{ route('areas.destroy', $area) }}"
                          onsubmit="return confirm('¿Eliminar esta área? No se puede deshacer.');">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm w-100">Eliminar</button>
                    </form>
                @endcan
            </div>

            <div class="dashboard-card p-4 mt-3">
                <h3 class="h6 mb-3">Supervisores</h3>
                @if ($area->supervisores->isEmpty())
                    <p class="text-muted small mb-0">Sin supervisores asignados.</p>
                @else
                    <ul class="list-unstyled mb-0 small">
                        @foreach ($area->supervisores as $sup)
                            <li>· {{ $sup->nombreCompleto() }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection
