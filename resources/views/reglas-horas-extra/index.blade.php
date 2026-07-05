@extends('layouts.app')
@section('title', 'Reglas de horas extra')
@section('content')
    <x-page-header title="Reglas de horas extra"
                   description="Configuración conforme a la Ley N° 27671 (25% / 35%)."
                   badge="Sprint 4" />

    <div class="row g-4">
        <div class="col-12 col-lg-5">
            @can('manage_reglas_horas_extra')
                <div class="dashboard-card p-4">
                    <h3 class="h6 mb-3">Nueva regla</h3>
                    <form method="POST" action="{{ route('reglas-horas-extra.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nombre</label>
                            <input type="text" name="nombre" value="{{ old('nombre', 'Ley N° 27671') }}" class="form-control" required>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small fw-semibold">Recargo primeras (%)</label>
                                <input type="number" step="0.01" name="recargo_primeras_horas" value="25.00" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold">Umbral primeras (h)</label>
                                <input type="number" name="umbral_primeras_horas" value="2" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Recargo adicional (%)</label>
                                <input type="number" step="0.01" name="recargo_adicional" value="35.00" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-check form-switch mt-3">
                            <input type="hidden" name="aplica_nocturno" value="0">
                            <input type="checkbox" name="aplica_nocturno" value="1" id="apnoc" class="form-check-input" checked>
                            <label class="form-check-label" for="apnoc">Aplica también en turno nocturno</label>
                        </div>
                        <div class="form-check form-switch mt-2">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" id="rgact" class="form-check-input" checked>
                            <label class="form-check-label" for="rgact">Regla activa (vigente)</label>
                        </div>
                        <button class="btn btn-primary w-100 mt-3">Guardar regla</button>
                    </form>
                </div>
            @endcan
        </div>

        <div class="col-12 col-lg-7">
            <div class="dashboard-card p-4">
                <h3 class="h6 mb-3">Reglas configuradas</h3>
                @if ($reglas->isEmpty())
                    <p class="text-muted small mb-0">Sin reglas configuradas.</p>
                @else
                    <table class="table table-sm small">
                        <thead>
                            <tr>
                                <th>Nombre</th><th>Primeras</th><th>Umbral</th><th>Adicional</th><th>Estado</th><th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($reglas as $r)
                            <tr>
                                <td>{{ $r->nombre }}</td>
                                <td>{{ $r->recargo_primeras_horas }}%</td>
                                <td>{{ $r->umbral_primeras_horas }} h</td>
                                <td>{{ $r->recargo_adicional }}%</td>
                                <td>
                                    @if ($r->is_active)
                                        <span class="badge text-bg-success">Vigente</span>
                                    @else
                                        <span class="badge text-bg-secondary">Inactiva</span>
                                    @endif
                                </td>
                                <td>
                                    @can('manage_reglas_horas_extra')
                                        <form method="POST" action="{{ route('reglas-horas-extra.destroy', $r) }}"
                                              onsubmit="return confirm('¿Eliminar regla?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">×</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
