@props(['empleado' => null, 'areas', 'roles' => null])

@csrf

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label small fw-semibold">Nombres *</label>
        <input type="text" name="nombres" required
               value="{{ old('nombres', $empleado?->nombres) }}"
               class="form-control @error('nombres') is-invalid @enderror" maxlength="100">
        @error('nombres') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-semibold">Apellidos *</label>
        <input type="text" name="apellidos" required
               value="{{ old('apellidos', $empleado?->apellidos) }}"
               class="form-control @error('apellidos') is-invalid @enderror" maxlength="100">
        @error('apellidos') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-semibold">DNI *</label>
        <input type="text" name="dni" required pattern="\d{8}" inputmode="numeric"
               value="{{ old('dni', $empleado?->dni) }}"
               class="form-control @error('dni') is-invalid @enderror">
        @error('dni') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-semibold">Teléfono</label>
        <input type="text" name="telefono" pattern="\d{9}" inputmode="numeric"
               value="{{ old('telefono', $empleado?->telefono) }}"
               class="form-control @error('telefono') is-invalid @enderror">
        @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-semibold">Fecha de ingreso</label>
        <input type="date" name="fecha_ingreso"
               value="{{ old('fecha_ingreso', optional($empleado?->fecha_ingreso)->toDateString()) }}"
               max="{{ now()->toDateString() }}"
               class="form-control @error('fecha_ingreso') is-invalid @enderror">
        @error('fecha_ingreso') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-semibold">Área *</label>
        <select name="area_id" required class="form-select @error('area_id') is-invalid @enderror">
            <option value="">— Seleccione —</option>
            @foreach ($areas as $area)
                <option value="{{ $area->id }}" @selected(old('area_id', $empleado?->area_id) == $area->id)>
                    {{ $area->nombre }}
                </option>
            @endforeach
        </select>
        @error('area_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-semibold">Cargo</label>
        <input type="text" name="cargo" maxlength="120"
               value="{{ old('cargo', $empleado?->cargo) }}"
               class="form-control @error('cargo') is-invalid @enderror">
        @error('cargo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-semibold">Estado</label>
        <select name="estado" class="form-select">
            <option value="activo"   @selected(old('estado', $empleado?->estado ?? 'activo') === 'activo')>Activo</option>
            <option value="inactivo" @selected(old('estado', $empleado?->estado) === 'inactivo')>Inactivo</option>
        </select>
    </div>

    @if (! $empleado)
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Correo (usuario)</label>
            <input type="email" name="email" maxlength="120"
                   value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Contraseña inicial</label>
            <input type="text" name="password" minlength="8"
                   value="{{ old('password') }}"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Mínimo 8 caracteres">
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    @endif

    @if ($roles)
        <div class="col-md-6">
            <label class="form-label small fw-semibold">Rol del sistema</label>
            <select name="rol" class="form-select">
                <option value="">— Sin asignar —</option>
                @foreach ($roles as $r)
                    @php $rolActual = $empleado?->user?->roles?->first()?->name; @endphp
                    <option value="{{ $r->name }}" @selected(old('rol', $rolActual) === $r->name)>{{ $r->name }}</option>
                @endforeach
            </select>
        </div>
    @endif
</div>

<hr class="my-4">

<div class="d-flex gap-2 justify-content-end">
    <a href="{{ route('empleados.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
