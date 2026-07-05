@extends('layouts.app')

@section('title', 'Panel de asistencia')
@section('content')
    <x-page-header title="Panel de asistencia en tiempo real"
                   description="Estado actual de los empleados del día. Se actualiza automáticamente cada 30 segundos."
                   badge="Sprint 6" />

    @if (! $area_forzada)
        <form method="GET" class="dashboard-card p-3 mb-3 d-flex gap-2 align-items-end">
            <div class="flex-grow-1">
                <label class="form-label small fw-semibold mb-1">Filtrar por área</label>
                <select name="area_id" class="form-select">
                    <option value="">Todas las áreas</option>
                    @foreach ($areas as $a)
                        <option value="{{ $a->id }}" @selected($area_id == $a->id)>{{ $a->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-primary">Aplicar</button>
        </form>
    @endif

    <div class="dashboard-card p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle" id="panel-tabla">
                <thead class="table-light">
                    <tr>
                        <th>Empleado</th>
                        <th>Área</th>
                        <th>Estado</th>
                        <th>Entrada</th>
                        <th>Salida</th>
                        <th>Horas</th>
                    </tr>
                </thead>
                <tbody id="panel-tbody">
                @forelse ($snapshot as $row)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $row['nombre'] }}</div>
                            <small class="text-muted">{{ $row['dni'] }}</small>
                        </td>
                        <td>{{ $row['area'] ?? '—' }}</td>
                        <td><x-status-badge :status="$row['estado']" /></td>
                        <td>{{ $row['hora_entrada'] ?? '—' }}</td>
                        <td>{{ $row['hora_salida'] ?? '—' }}</td>
                        <td>{{ $row['horas_trabajadas'] ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Sin empleados en esta vista.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <p class="text-muted small mt-3">
        Última actualización: <span id="updated-at">{{ now()->format('H:i:s') }}</span>
    </p>

    <script>
        (function () {
            const url = "{{ route('asistencias.panel.json', ['area_id' => $area_id]) }}";
            const tbody = document.getElementById('panel-tbody');
            const upd = document.getElementById('updated-at');

            async function refresh() {
                try {
                    const r = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    if (!r.ok) return;
                    const data = await r.json();
                    tbody.innerHTML = '';
                    if (data.snapshot.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Sin empleados.</td></tr>';
                    } else {
                        data.snapshot.forEach(row => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><div class="fw-semibold">${row.nombre}</div><small class="text-muted">${row.dni ?? ''}</small></td>
                                <td>${row.area ?? '—'}</td>
                                <td><span class="badge ${badgeClass(row.estado)}">${estadoLabel(row.estado)}</span></td>
                                <td>${row.hora_entrada ?? '—'}</td>
                                <td>${row.hora_salida ?? '—'}</td>
                                <td>${row.horas_trabajadas ?? '—'}</td>`;
                            tbody.appendChild(tr);
                        });
                    }
                    upd.textContent = new Date(data.updated_at).toLocaleTimeString();
                } catch (_) {}
            }
            function badgeClass(s) {
                return ({
                    presente: 'text-bg-success',
                    salida: 'text-bg-success',
                    tardanza: 'text-bg-warning',
                    ausencia: 'text-bg-danger',
                    justificada: 'text-bg-info',
                    pendiente: 'text-bg-secondary',
                })[s] ?? 'text-bg-light text-dark';
            }
            function estadoLabel(s) {
                return ({
                    presente: 'Presente',
                    salida: 'Jornada cerrada',
                    tardanza: 'Tardanza',
                    ausencia: 'Ausente',
                    justificada: 'Justificada',
                    pendiente: 'Pendiente',
                })[s] ?? s;
            }
            setInterval(refresh, 30000);
        })();
    </script>
@endsection
