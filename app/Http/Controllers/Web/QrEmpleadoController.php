<?php

namespace App\Http\Controllers\Web;

use App\Domain\Asistencias\QrService;
use App\Http\Controllers\Controller;
use App\Models\Empleado;
use Illuminate\Http\Response;
use Illuminate\View\View;

class QrEmpleadoController extends Controller
{
    public function __construct(private readonly QrService $qr)
    {
    }

    public function imprimible(Empleado $empleado): View
    {
        $codigo = $this->qr->asegurarCodigo($empleado);
        $svg    = $this->qr->renderSvg($codigo, 220);

        return view('empleados.qr', [
            'empleado' => $empleado,
            'codigo'   => $codigo,
            'svg'      => $svg,
        ]);
    }

    public function svg(Empleado $empleado): Response
    {
        $codigo = $this->qr->asegurarCodigo($empleado);
        $svg    = $this->qr->renderSvg($codigo, 320);

        return response($svg, 200, ['Content-Type' => 'image/svg+xml']);
    }
}
