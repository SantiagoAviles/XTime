<?php

namespace App\Http\Controllers\Web;

use App\Domain\Justificaciones\JustificacionService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Justificaciones\StoreJustificacionRequest;
use App\Models\Justificacion;
use App\Models\Marcacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutogestionJustificacionController extends Controller
{
    public function __construct(private readonly JustificacionService $svc)
    {
    }

    public function index(Request $request): View
    {
        $empleado = $request->user()->empleado;
        abort_unless($empleado, 403);

        $solicitudes = Justificacion::where('empleado_id', $empleado->id)
            ->latest('fecha')
            ->paginate(15);

        return view('autogestion.justificaciones.index', [
            'solicitudes' => $solicitudes,
        ]);
    }

    public function create(Request $request): View
    {
        $empleado = $request->user()->empleado;
        abort_unless($empleado, 403);

        // Incidencias sin justificación todavía (ausencias y tardanzas)
        $incidencias = Marcacion::where('empleado_id', $empleado->id)
            ->whereIn('estado', [Marcacion::ESTADO_AUSENCIA, Marcacion::ESTADO_TARDANZA])
            ->whereDate('fecha', '<=', now())
            ->whereDoesntHave('justificaciones', fn ($q) => $q->whereIn('estado', ['pendiente', 'aprobada']))
            ->orderByDesc('fecha')
            ->limit(20)
            ->get();

        return view('autogestion.justificaciones.create', [
            'incidencias' => $incidencias,
        ]);
    }

    public function store(StoreJustificacionRequest $request): RedirectResponse
    {
        $empleado = $request->user()->empleado;

        $this->svc->crear(
            empleado: $empleado,
            data: $request->only(['fecha', 'tipo', 'motivo']),
            adjunto: $request->file('adjunto'),
        );

        return redirect()
            ->route('autogestion.justificaciones.index')
            ->with('success', 'Solicitud enviada. Recibirás una notificación cuando sea revisada.');
    }
}
