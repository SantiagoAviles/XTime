@extends('layouts.app')
@section('title', 'Mis justificaciones')
@section('content')
    <x-page-header title="Mis justificaciones"
                   description="Seguimiento de tus solicitudes."
                   badge="Mi cuenta" />

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('autogestion.justificaciones.create') }}" class="btn btn-primary">+ Nueva justificación</a>
    </div>

    <div class="dashboard-card p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Fecha incidencia</th>
                        <th>Tipo</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th>Revisado</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($solicitudes as $s)
                    <tr>
                        <td>{{ $s->fecha->format('d/m/Y') }}</td>
                        <td><span class="badge text-bg-light text-dark border">{{ ucfirst($s->tipo) }}</span></td>
                        <td><small>{{ \Illuminate\Support\Str::limit($s->motivo, 80) }}</small></td>
                        <td><x-status-badge :status="$s->estado" /></td>
                        <td>
                            @if ($s->revisada_at)
                                <small>{{ $s->revisada_at->format('d/m/Y H:i') }}<br>
                                    por {{ $s->revisor?->name }}</small>
                                @if ($s->comentario_revisor)
                                    <br><em class="small text-muted">{{ \Illuminate\Support\Str::limit($s->comentario_revisor, 60) }}</em>
                                @endif
                            @else
                                <small class="text-muted">—</small>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No has enviado solicitudes todavía.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $solicitudes->links() }}</div>
@endsection
