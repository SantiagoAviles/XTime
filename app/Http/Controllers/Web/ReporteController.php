<?php

namespace App\Http\Controllers\Web;

use App\Domain\Reportes\ReporteAsistenciaService;
use App\Exports\AsistenciaExport;
use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Turno;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReporteController extends Controller
{
    public function __construct(private readonly ReporteAsistenciaService $svc)
    {
    }

    public function asistencia(Request $request): View
    {
        $filtros = $this->limitarFiltrosASupervisor($request);

        return view('reportes.asistencia.index', [
            'filtros' => $filtros,
            'areas'   => Area::where('is_active', true)->orderBy('nombre')->get(),
            'turnos'  => Turno::where('is_active', true)->orderBy('nombre')->get(),
            'filas'   => $request->boolean('previsualizar') ? $this->svc->consolidadoPorEmpleado($filtros) : [],
        ]);
    }

    public function asistenciaPdf(Request $request): Response
    {
        $filtros = $this->limitarFiltrosASupervisor($request);
        $filas   = $this->svc->consolidadoPorEmpleado($filtros);

        $pdf = Pdf::loadView('reportes.asistencia.pdf', [
            'filas'   => $filas,
            'filtros' => $filtros,
            'meta'    => ['titulo' => 'Reporte mensual de asistencia'],
        ])->setPaper('a4', 'landscape');

        return $pdf->download('reporte-asistencia-' . now()->format('Ymd-His') . '.pdf');
    }

    public function asistenciaExcel(Request $request): BinaryFileResponse
    {
        $filtros = $this->limitarFiltrosASupervisor($request);
        $filas   = $this->svc->consolidadoPorEmpleado($filtros);

        return Excel::download(
            new AsistenciaExport($filas, 'Asistencia ' . ($filtros['desde'] ?? 'periodo')),
            'reporte-asistencia-' . now()->format('Ymd-His') . '.xlsx'
        );
    }

    public function horasExtra(Request $request): View
    {
        $filtros = $this->limitarFiltrosASupervisor($request);

        return view('reportes.horas-extra.index', [
            'filtros' => $filtros,
            'areas'   => Area::where('is_active', true)->orderBy('nombre')->get(),
            'filas'   => $this->svc->horasExtraPorEmpleado($filtros),
        ]);
    }

    public function incidencias(Request $request): View
    {
        $filtros = $this->limitarFiltrosASupervisor($request);

        return view('reportes.incidencias.index', [
            'filtros'    => $filtros,
            'areas'      => Area::where('is_active', true)->orderBy('nombre')->get(),
            'incidencias' => $this->svc->incidencias($filtros),
        ]);
    }

    private function limitarFiltrosASupervisor(Request $request): array
    {
        $filtros = $request->only(['desde', 'hasta', 'area_id', 'turno_id', 'empleado_id']);

        $user = $request->user();
        if ($user->hasRole('Supervisor') && ! $user->hasAnyRole(['Administrador', 'RRHH'])) {
            $filtros['solo_area_id'] = $user->empleado?->areasSupervisadas->first()?->id
                ?? $user->empleado?->area_id;
        }

        return $filtros;
    }
}
