<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $meta['titulo'] }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1a1a1a; }
        h1 { font-size: 14px; margin: 0 0 4px; }
        .header { border-bottom: 2px solid #0f4c5c; padding-bottom: 8px; margin-bottom: 12px; }
        .meta { font-size: 9px; color: #555; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 3px 5px; }
        th { background: #f1f5f9; }
        .num { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>ALTERMEC — Reporte de asistencia consolidado</h1>
        <div class="meta">
            Período: {{ $filtros['desde'] ?? '—' }} a {{ $filtros['hasta'] ?? '—' }} ·
            Empleados: {{ count($filas) }} ·
            Generado: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>DNI</th><th>Empleado</th><th>Área</th>
                <th>Normales</th><th>Tardanzas</th><th>Ausencias</th><th>Justif.</th>
                <th>Horas</th><th>H. extra</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($filas as $r)
            <tr>
                <td>{{ $r['dni'] }}</td>
                <td>{{ $r['nombre'] }}</td>
                <td>{{ $r['area'] }}</td>
                <td class="num">{{ $r['dias_normales'] }}</td>
                <td class="num">{{ $r['dias_tardanza'] }}</td>
                <td class="num">{{ $r['dias_ausencia'] }}</td>
                <td class="num">{{ $r['dias_justificada'] }}</td>
                <td class="num">{{ $r['horas_trabajadas'] }}</td>
                <td class="num">{{ $r['horas_extra'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
