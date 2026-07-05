@extends('layouts.app')
@section('title', 'Feriados')
@section('content')
    <x-page-header title="Calendario de feriados"
                   description="Días no laborables. No generan ausencia automática."
                   badge="Sprint 4" />

    <div class="row g-4">
        <div class="col-12 col-lg-5">
            @can('manage_feriados')
                <div class="dashboard-card p-4">
                    <h3 class="h6 mb-3">Registrar feriado</h3>
                    <form method="POST" action="{{ route('feriados.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Fecha *</label>
                            <input type="date" name="fecha" required value="{{ old('fecha') }}"
                                   class="form-control @error('fecha') is-invalid @enderror">
                            @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Descripción *</label>
                            <input type="text" name="descripcion" required maxlength="120"
                                   value="{{ old('descripcion') }}"
                                   class="form-control @error('descripcion') is-invalid @enderror">
                            @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input type="hidden" name="nacional" value="0">
                            <input type="checkbox" name="nacional" value="1" id="fNacional" class="form-check-input" checked>
                            <label class="form-check-label" for="fNacional">Feriado nacional</label>
                        </div>
                        <button class="btn btn-primary w-100">Registrar</button>
                    </form>
                </div>
            @endcan
        </div>

        <div class="col-12 col-lg-7">
            <div class="dashboard-card p-4">
                <form method="GET" class="d-flex gap-2 mb-3">
                    <input type="number" name="año" min="2020" max="2100" value="{{ $año }}" class="form-control" style="max-width: 120px;">
                    <button class="btn btn-outline-secondary">Cambiar año</button>
                </form>

                @if ($feriados->isEmpty())
                    <p class="text-muted small mb-0">Sin feriados registrados en {{ $año }}.</p>
                @else
                    <ul class="list-group list-group-flush small">
                        @foreach ($feriados as $f)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $f->fecha->format('d/m/Y') }}</strong>
                                    <span class="text-muted">— {{ $f->descripcion }}</span>
                                    @if ($f->nacional)
                                        <span class="badge text-bg-info ms-2">Nacional</span>
                                    @endif
                                </div>
                                @can('manage_feriados')
                                    <form method="POST" action="{{ route('feriados.destroy', $f) }}"
                                          onsubmit="return confirm('¿Eliminar este feriado?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">×</button>
                                    </form>
                                @endcan
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection
