@extends('layouts.app')

@section('title', 'Editar usuario · ' . $usuario->name)
@section('section_label', 'Seguridad')
@section('page_title', 'Editar ' . $usuario->name)
@section('page_description', 'Asignación de rol y estado del usuario.')

@section('content')
    <div class="dashboard-card p-4">
        <form method="POST" action="{{ route('seguridad.usuarios.update', $usuario) }}">
            @csrf @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Correo</label>
                    <input type="email" class="form-control" value="{{ $usuario->email }}" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Empleado vinculado</label>
                    <input type="text" class="form-control"
                           value="{{ $usuario->empleado?->nombreCompleto() ?? 'Sin empleado' }}" disabled>
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Rol del sistema *</label>
                    <select name="rol" required class="form-select @error('rol') is-invalid @enderror">
                        @foreach ($roles as $r)
                            <option value="{{ $r->name }}" @selected($usuario->roles->first()?->name === $r->name)>
                                {{ $r->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('rol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-semibold d-block">¿Activo?</label>
                    <div class="form-check form-switch mt-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" id="usuActivo"
                               class="form-check-input" @checked($usuario->is_active)>
                        <label class="form-check-label" for="usuActivo">Sí, puede iniciar sesión</label>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('seguridad.usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button class="btn btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>
@endsection
