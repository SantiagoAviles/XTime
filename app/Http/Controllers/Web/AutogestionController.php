<?php

namespace App\Http\Controllers\Web;

use App\Domain\Autogestion\ResumenMensualService;
use App\Http\Controllers\Controller;
use App\Models\Marcacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutogestionController extends Controller
{
    public function __construct(private readonly ResumenMensualService $resumen)
    {
    }

    public function dashboard(Request $request): View
    {
        $empleado = $request->user()->empleado;
        abort_unless($empleado, 403, 'Tu usuario no está vinculado a un empleado.');

        $turno    = $empleado->turnoVigente();
        $hoy      = Marcacion::where('empleado_id', $empleado->id)
            ->whereDate('fecha', now()->toDateString())
            ->first();

        $resumen = $this->resumen->resumen($empleado, (int) now()->year, (int) now()->month);

        return view('autogestion.dashboard', [
            'empleado' => $empleado,
            'turno'    => $turno,
            'hoy'      => $hoy,
            'resumen'  => $resumen,
        ]);
    }

    public function historial(Request $request): View
    {
        $empleado = $request->user()->empleado;
        abort_unless($empleado, 403);

        $mes = (int) $request->input('mes', now()->month);
        $año = (int) $request->input('año', now()->year);

        $marcaciones = Marcacion::query()
            ->where('empleado_id', $empleado->id)
            ->whereYear('fecha', $año)
            ->whereMonth('fecha', $mes)
            ->orderBy('fecha')
            ->get();

        return view('autogestion.historial', [
            'empleado'    => $empleado,
            'marcaciones' => $marcaciones,
            'mes'         => $mes,
            'año'         => $año,
            'resumen'     => $this->resumen->resumen($empleado, $año, $mes),
        ]);
    }

    public function reportePersonalPdf(Request $request): Response
    {
        $empleado = $request->user()->empleado;
        abort_unless($empleado, 403);

        $mes = (int) $request->input('mes', now()->month);
        $año = (int) $request->input('año', now()->year);

        $marcaciones = Marcacion::query()
            ->where('empleado_id', $empleado->id)
            ->whereYear('fecha', $año)
            ->whereMonth('fecha', $mes)
            ->orderBy('fecha')
            ->get();

        $resumen = $this->resumen->resumen($empleado, $año, $mes);

        $pdf = Pdf::loadView('autogestion.reporte-pdf', [
            'empleado'    => $empleado,
            'marcaciones' => $marcaciones,
            'mes'         => $mes,
            'año'         => $año,
            'resumen'     => $resumen,
        ]);

        $filename = "reporte-{$empleado->dni}-{$año}-{$mes}.pdf";

        return $pdf->download($filename);
    }
}
