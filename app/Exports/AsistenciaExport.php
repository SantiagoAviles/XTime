<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AsistenciaExport implements FromArray, WithHeadings, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(
        private readonly array $filas,
        private readonly string $titulo = 'Asistencia mensual',
    ) {}

    public function array(): array
    {
        return array_map(fn ($r) => [
            $r['dni'],
            $r['nombre'],
            $r['area'],
            $r['cargo'] ?? '—',
            $r['dias_normales'],
            $r['dias_tardanza'],
            $r['dias_ausencia'],
            $r['dias_justificada'],
            $r['horas_trabajadas'],
            $r['horas_extra'],
        ], $this->filas);
    }

    public function headings(): array
    {
        return [
            'DNI', 'Empleado', 'Área', 'Cargo',
            'Días normales', 'Tardanzas', 'Ausencias', 'Justificadas',
            'Horas trabajadas', 'Horas extra',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0F4C5C']],
            ],
        ];
    }

    public function title(): string
    {
        return $this->titulo;
    }
}
