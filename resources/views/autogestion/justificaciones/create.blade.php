@extends('layouts.app')
@section('title', 'Nueva justificación')
@section('content')
    <x-page-header title="Solicitar justificación"
                   description="Solo puedes justificar fechas con ausencia o tardanza registrada."
                   badge="Mi cuenta" />

    <div class="dashboard-card p-4" style="max-width: 720px;">
        @if ($incidencias->isEmpty())
            <p class="text-muted">No tienes incidencias sin justificar en los últimos días.</p>
            <a href="{{ route('autogestion.justificaciones.index') }}" class="btn btn-outline-secondary">Volver</a>
        @else
            <form method="POST" action="{{ route('autogestion.justificaciones.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Selecciona la incidencia *</label>
                    <select name="fecha" required class="form-select @error('fecha') is-invalid @enderror"
                            onchange="document.getElementById('tipo-input').value = this.options[this.selectedIndex].dataset.tipo">
                        <option value="">— Selecciona —</option>
                        @foreach ($incidencias as $inc)
                            <option value="{{ $inc->fecha->toDateString() }}" data-tipo="{{ $inc->estado }}">
                                {{ $inc->fecha->format('d/m/Y') }} —
                                {{ $inc->estado === 'ausencia' ? 'Ausencia' : 'Tardanza' }}
                            </option>
                        @endforeach
                    </select>
                    @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <input type="hidden" name="tipo" id="tipo-input" value="{{ old('tipo') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Motivo *</label>
                    <textarea name="motivo" required minlength="10" maxlength="1000" rows="4"
                              class="form-control @error('motivo') is-invalid @enderror">{{ old('motivo') }}</textarea>
                    @error('motivo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">Sustento (PDF / JPG / PNG, máx 5 MB)</label>
                    <input type="file" name="adjunto" accept=".pdf,.jpg,.jpeg,.png"
                           class="form-control @error('adjunto') is-invalid @enderror">
                    @error('adjunto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('autogestion.justificaciones.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    <button class="btn btn-primary">Enviar solicitud</button>
                </div>
            </form>
        @endif
    </div>
@endsection
