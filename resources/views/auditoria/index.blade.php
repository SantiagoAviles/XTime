@extends('layouts.app')
@section('title', 'Auditoría')
@section('content')
    <x-page-header title="Bitácora de auditoría"
                   description="Registros inmutables de acciones sensibles del sistema."
                   badge="Sprint 10" />

    <form method="GET" class="dashboard-card p-3 mb-3 row g-2 align-items-end">
        <div class="col-md-2">
            <label class="form-label small">Módulo</label>
            <select name="log_name" class="form-select">
                <option value="">Todos</option>
                @foreach ($log_names as $n)
                    <option value="{{ $n }}" @selected(($filtros['log_name'] ?? '') === $n)>{{ $n }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small">Evento</label>
            <input type="text" name="event" value="{{ $filtros['event'] ?? '' }}" class="form-control" placeholder="login_success">
        </div>
        <div class="col-md-3">
            <label class="form-label small">Usuario (email)</label>
            <input type="text" name="causer_email" value="{{ $filtros['causer_email'] ?? '' }}" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label small">Desde</label>
            <input type="date" name="desde" value="{{ $filtros['desde'] ?? '' }}" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label small">Hasta</label>
            <input type="date" name="hasta" value="{{ $filtros['hasta'] ?? '' }}" class="form-control">
        </div>
        <div class="col-md-1"><button class="btn btn-primary w-100">Filtrar</button></div>
    </form>

    <div class="dashboard-card p-0">
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Módulo</th>
                        <th>Evento</th>
                        <th>Usuario</th>
                        <th>Entidad</th>
                        <th>IP</th>
                        <th>Propiedades</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td><small>{{ $log->created_at->format('d/m/Y H:i:s') }}</small></td>
                        <td><span class="badge text-bg-light text-dark border">{{ $log->log_name }}</span></td>
                        <td><code class="small">{{ $log->event ?? $log->description }}</code></td>
                        <td>{{ $log->causer?->email ?? 'sistema' }}</td>
                        <td><small>{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</small></td>
                        <td><small>{{ $log->properties['ip'] ?? '—' }}</small></td>
                        <td>
                            <details>
                                <summary class="small text-muted">Ver</summary>
                                <pre class="small mb-0" style="max-width: 360px; white-space: pre-wrap;">{{ json_encode($log->properties, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Sin registros con esos filtros.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $logs->links() }}</div>

    <p class="text-muted small mt-3">
        ⚠️ Los registros de auditoría son <strong>inmutables</strong>: ningún usuario puede editarlos o eliminarlos.
    </p>
@endsection
