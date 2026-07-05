<?php

namespace App\Domain\Asistencias;

use App\Models\Empleado;
use Illuminate\Support\Str;

class QrService
{
    /**
     * Genera (si hace falta) y devuelve el código QR único del empleado.
     * El código se persiste en empleados.qr_code.
     */
    public function asegurarCodigo(Empleado $empleado): string
    {
        if (! empty($empleado->qr_code)) {
            return $empleado->qr_code;
        }

        // Formato: ALTM-{id}-{random8} — corto, único e imprimible.
        $codigo = sprintf('ALTM-%d-%s', $empleado->id, Str::upper(Str::random(8)));
        $empleado->update(['qr_code' => $codigo]);

        return $codigo;
    }

    /**
     * Resuelve un empleado a partir del contenido leído del QR.
     */
    public function resolverEmpleadoPorCodigo(string $codigo): ?Empleado
    {
        return Empleado::query()
            ->where('qr_code', trim($codigo))
            ->whereNull('deleted_at')
            ->first();
    }

    /**
     * Renderiza el QR en formato SVG inline (no requiere imagick).
     * Si la librería simple-qrcode no está disponible, devuelve un
     * SVG de fallback con el texto plano del código.
     */
    public function renderSvg(string $codigo, int $size = 220): string
    {
        if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
            return (string) \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size($size)
                ->margin(1)
                ->errorCorrection('H')
                ->generate($codigo);
        }

        $w = $size;
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="$w" height="$w" viewBox="0 0 $w $w">
    <rect width="$w" height="$w" fill="#fff" stroke="#333"/>
    <text x="50%" y="50%" font-family="monospace" font-size="14"
          text-anchor="middle" dominant-baseline="middle">$codigo</text>
</svg>
SVG;
    }
}
