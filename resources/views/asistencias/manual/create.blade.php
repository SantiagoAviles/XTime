@extends('layouts.app')

@section('title', 'Marcación manual')
@section('content')
    <x-page-header title="Marcación manual"
                   description="Registra una entrada o salida para un empleado. La acción queda auditada."
                   badge="RRHH" />

    <div class="dashboard-card p-4" style="max-width: 720px;">
        <form method="POST" action="{{ route('asistencias.manual.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label small fw-semibold">Empleado *</label>
                <select name="empleado_id" required class="form-select @error('empleado_id') is-invalid @enderror">
                    <option value="">— Seleccione —</option>
                    @foreach ($empleados as $e)
                        <option value="{{ $e->id }}" @selected(old('empleado_id') == $e->id)>
                            {{ $e->apellidos }}, {{ $e->nombres }} — {{ $e->dni }}
                        </option>
                    @endforeach
                </select>
                @error('empleado_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Fecha y hora *</label>
                <input type="datetime-local" name="fecha_hora" required
                       value="{{ old('fecha_hora', now()->format('Y-m-d\TH:i')) }}"
                       class="form-control @error('fecha_hora') is-invalid @enderror">
                @error('fecha_hora') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Motivo / justificación *</label>
                <textarea name="motivo" required rows="3" minlength="5" maxlength="300"
                          class="form-control @error('motivo') is-invalid @enderror">{{ old('motivo') }}</textarea>
                @error('motivo') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="alert alert-warning small">
                ⚠️ Esta acción será registrada en la bitácora de auditoría con tu usuario,
                la justificación, fecha/hora e IP de origen.
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('asistencias.panel') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button class="btn btn-primary">Registrar marcación</button>
            </div>
        </form>
    </div>
@endsection
