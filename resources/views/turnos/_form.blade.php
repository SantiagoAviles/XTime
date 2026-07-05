@props(['turno' => null])
@csrf

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label small fw-semibold">Nombre *</label>
        <input type="text" name="nombre" required maxlength="100"
               value="{{ old('nombre', $turno?->nombre) }}"
               class="form-control @error('nombre') is-invalid @enderror">
        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-semibold">Tipo *</label>
        <select name="tipo" required class="form-select">
            @foreach (['diurno', 'nocturno', 'rotativo', 'flexible'] as $t)
                <option value="{{ $t }}" @selected(old('tipo', $turno?->tipo) === $t)>{{ ucfirst($t) }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label small fw-semibold">Hora entrada *</label>
        <input type="time" name="hora_entrada" required
               value="{{ old('hora_entrada', $turno ? \Carbon\Carbon::parse($turno->hora_entrada)->format('H:i') : '') }}"
               class="form-control @error('hora_entrada') is-invalid @enderror">
        @error('hora_entrada') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label small fw-semibold">Hora salida *</label>
        <input type="time" name="hora_salida" required
               value="{{ old('hora_salida', $turno ? \Carbon\Carbon::parse($turno->hora_salida)->format('H:i') : '') }}"
               class="form-control @error('hora_salida') is-invalid @enderror">
        @error('hora_salida') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-3">
        <label class="form-label small fw-semibold">Tolerancia (min)</label>
        <input type="number" name="tolerancia_minutos" min="0" max="120"
               value="{{ old('tolerancia_minutos', $turno?->tolerancia_minutos ?? 10) }}"
               class="form-control">
    </div>
    <div class="col-md-3">
        <label class="form-label small fw-semibold d-block">Cruza medianoche</label>
        <div class="form-check form-switch mt-2">
            <input type="hidden" name="cruza_medianoche" value="0">
            <input type="checkbox" name="cruza_medianoche" value="1" id="crucetn"
                   class="form-check-input" @checked(old('cruza_medianoche', $turno?->cruza_medianoche))>
            <label class="form-check-label" for="crucetn">Sí (ej. 22:00 → 06:00)</label>
        </div>
    </div>

    <div class="col-12">
        <label class="form-label small fw-semibold">Descripción</label>
        <textarea name="descripcion" rows="2" maxlength="500"
                  class="form-control">{{ old('descripcion', $turno?->descripcion) }}</textarea>
    </div>

    <div class="col-md-4">
        <div class="form-check form-switch mt-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="actst"
                   class="form-check-input" @checked(old('is_active', $turno?->is_active ?? true))>
            <label class="form-check-label" for="actst">Turno activo</label>
        </div>
    </div>
</div>

<hr class="my-4">
<div class="d-flex gap-2 justify-content-end">
    <a href="{{ route('turnos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
