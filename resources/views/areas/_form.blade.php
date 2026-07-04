@props(['area' => null])

@csrf

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label small fw-semibold">Nombre *</label>
        <input type="text" name="nombre" required maxlength="100"
               value="{{ old('nombre', $area?->nombre) }}"
               class="form-control @error('nombre') is-invalid @enderror">
        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-semibold d-block">¿Activa?</label>
        <div class="form-check form-switch">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" class="form-check-input"
                   id="areaActiva" @checked(old('is_active', $area?->is_active ?? true))>
            <label class="form-check-label" for="areaActiva">Sí, está activa</label>
        </div>
    </div>
    <div class="col-12">
        <label class="form-label small fw-semibold">Descripción</label>
        <textarea name="descripcion" rows="3" maxlength="500"
                  class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $area?->descripcion) }}</textarea>
        @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<hr class="my-4">

<div class="d-flex gap-2 justify-content-end">
    <a href="{{ route('areas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
