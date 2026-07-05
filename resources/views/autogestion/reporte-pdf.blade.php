<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte personal {{ $empleado->nombreCompleto() }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        .header { border-bottom: 2px solid #0f4c5c; padding-bottom: 10px; margin-bottom: 16px; }
        .meta { font-size: 10px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 4px 6px; text-align: left; }
        th { background: #f1f5f9; }
        .resumen td { background: #f8fafc; font-weight: bold; }
        .estado-normal     { color: #166534; }
        .estado-tardanza   { color: #92400e; }
        .estado-ausencia   { color: #991b1b; }
        .estado-justificada{ color: #1e40af; }
    </style>
</head>
<body>
    <div class="header">
        <h1>ALTERMEC — Reporte personal de asistencia</h1>
        <div class="meta">
            <strong>{{ $empleado->nombreCompleto() }}</strong> ·
            DNI {{ $empleado->dni }} ·
            Área: {{ $empleado->area?->nombre ?? '—' }} ·
            Cargo: {{ $empleado->cargo ?? '—' }}<br>
            Período: {{ str_pad($mes, 2, '0', STR_PAD_LEFT) }}/{{ $año }} —
            Generado: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <table class="resumen">
        <tr>
            <td>Días normales</td><td>{{ $resumen['dias_normales'] }}</td>
            <td>Tardanzas</td><td>{{ $resumen['dias_tardanza'] }}</td>
            <td>Ausencias</td><td>{{ $resumen['dias_ausencia'] }}</td>
            <td>Justificadas</td><td>{{ $resumen['dias_justificada'] }}</td>
        </tr>
        <tr>
            <td>Horas trabajadas</td><td colspan="3">{{ $resumen['horas_trabajadas'] }}</td>
            <td>Horas extra</td><td colspan="3">{{ $resumen['horas_extra'] }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr><th>Fecha</th><th>Entrada</th><th>Salida</th><th>Horas</th><th>Extra</th><th>Estado</th></tr>
        </thead>
        <tbody>
        @foreach ($marcaciones as $m)
            <tr>
                <td>{{ $m->fecha->format('d/m/Y') }}</td>
                <td>{{ optional($m->hora_entrada)->format('H:i') ?? '—' }}</td>
                <td>{{ optional($m->hora_salida)->format('H:i') ?? '—' }}</td>
                <td>{{ $m->horas_trabajadas }}</td>
                <td>{{ $m->horas_extra }}</td>
                <td class="estado-{{ $m->estado }}">{{ ucfirst($m->estado) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
