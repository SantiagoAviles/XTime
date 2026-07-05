@extends('layouts.app')
@section('title', 'Bandeja de justificaciones')
@section('content')
    <x-page-header title="Justificaciones a revisar"
                   description="Aprueba o rechaza solicitudes con el motivo correspondiente."
                   badge="Sprint 8" />

    <ul class="nav nav-pills mb-3">
        @foreach (['pendiente' => 'Pendientes', 'aprobada' => 'Aprobadas', 'rechazada' => 'Rechazadas'] as $k => $l)
            <li class="nav-item">
                <a class="nav-link {{ $estado === $k ? 'active' : '' }}"
                   href="{{ route('justificaciones.index', ['estado' => $k]) }}">{{ $l }}</a>
            </li>
        @endforeach
    </ul>

    <div class="dashboard-card p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Empleado</th>
                        <th>Área</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Motivo</th>
                        <th>Adjunto</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($justificaciones as $j)
                    <tr>
                        <td>{{ $j->empleado?->nombreCompleto() }}</td>
                        <td>{{ $j->empleado?->area?->nombre ?? '—' }}</td>
                        <td>{{ $j->fecha->format('d/m/Y') }}</td>
                        <td><span class="badge text-bg-light text-dark border">{{ ucfirst($j->tipo) }}</span></td>
                        <td><small>{{ \Illuminate\Support\Str::limit($j->motivo, 80) }}</small></td>
                        <td>
                            @if ($j->adjunto_path)
                                <a href="{{ \Storage::url($j->adjunto_path) }}" target="_blank" class="small">Ver</a>
                            @else <small class="text-muted">—</small>
                            @endif
                        </td>
                        <td><x-status-badge :status="$j->estado" /></td>
                        <td class="text-end">
                            @if ($j->estaPendiente())
                                <form method="POST" action="{{ route('justificaciones.aprobar', $j) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-success" onclick="return confirm('¿Aprobar?');">Aprobar</button>
                                </form>
                                <button class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal" data-bs-target="#modal-rechazo-{{ $j->id }}">Rechazar</button>

                                <div class="modal fade" id="modal-rechazo-{{ $j->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form method="POST" action="{{ route('justificaciones.rechazar', $j) }}" class="modal-content">
                                            @csrf
                                            <div class="modal-header"><h5 class="modal-title">Rechazar justificación</h5></div>
                                            <div class="modal-body">
                                                <p class="small">Empleado: {{ $j->empleado?->nombreCompleto() }} — {{ $j->fecha->format('d/m/Y') }}</p>
                                                <textarea name="comentario" required minlength="5" rows="3" class="form-control"
                                                          placeholder="Motivo del rechazo (obligatorio)"></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button class="btn btn-danger">Rechazar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <small class="text-muted">Revisado por {{ $j->revisor?->name ?? '—' }}</small>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Sin solicitudes en este estado.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $justificaciones->links() }}</div>
@endsection
