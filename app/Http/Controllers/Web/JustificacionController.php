<?php

namespace App\Http\Controllers\Web;

use App\Domain\Justificaciones\JustificacionService;
use App\Http\Controllers\Controller;
use App\Models\Justificacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JustificacionController extends Controller
{
    public function __construct(private readonly JustificacionService $svc)
    {
    }

    /**
     * Bandeja de revisión para Supervisor / RRHH / Administrador.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $estado = $request->input('estado', Justificacion::ESTADO_PENDIENTE);

        $query = Justificacion::query()->with(['empleado.area', 'revisor']);

        // Supervisor: solo su área.
        if ($user->hasRole('Supervisor') && ! $user->hasAnyRole(['Administrador', 'RRHH'])) {
            $areaId = $user->empleado?->areasSupervisadas->first()?->id
                ?? $user->empleado?->area_id;
            $query->whereHas('empleado', fn ($q) => $q->where('area_id', $areaId));
        }

        if (in_array($estado, ['pendiente', 'aprobada', 'rechazada'], true)) {
            $query->where('estado', $estado);
        }

        return view('justificaciones.index', [
            'justificaciones' => $query->latest('fecha')->paginate(20)->withQueryString(),
            'estado'          => $estado,
        ]);
    }

    public function aprobar(Request $request, Justificacion $justificacion): RedirectResponse
    {
        $data = $request->validate(['comentario' => ['nullable', 'string', 'max:500']]);

        $this->svc->aprobar($justificacion, $request->user(), $data['comentario'] ?? null);

        return back()->with('success', 'Justificación aprobada.');
    }

    public function rechazar(Request $request, Justificacion $justificacion): RedirectResponse
    {
        $data = $request->validate([
            'comentario' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'comentario.required' => 'Debe ingresar el motivo del rechazo.',
        ]);

        $this->svc->rechazar($justificacion, $request->user(), $data['comentario']);

        return back()->with('success', 'Justificación rechazada.');
    }
}
