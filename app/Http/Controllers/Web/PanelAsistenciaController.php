<?php

namespace App\Http\Controllers\Web;

use App\Domain\Asistencias\PanelAsistenciaService;
use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PanelAsistenciaController extends Controller
{
    public function __construct(private readonly PanelAsistenciaService $panel)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $areaForzada = null;

        // Supervisor: solo su área (la primera de las que supervisa).
        if ($user->hasRole('Supervisor') && ! $user->hasAnyRole(['Administrador', 'RRHH'])) {
            $areaForzada = $user->empleado?->areasSupervisadas->first()?->id
                ?? $user->empleado?->area_id;
        }

        $areaId = $areaForzada ?? ($request->integer('area_id') ?: null);

        return view('asistencias.panel', [
            'snapshot'    => $this->panel->snapshotDelDia($areaId),
            'areas'       => Area::where('is_active', true)->orderBy('nombre')->get(),
            'area_id'     => $areaId,
            'area_forzada'=> ! is_null($areaForzada),
        ]);
    }

    public function json(Request $request): JsonResponse
    {
        $user = $request->user();
        $areaForzada = null;
        if ($user->hasRole('Supervisor') && ! $user->hasAnyRole(['Administrador', 'RRHH'])) {
            $areaForzada = $user->empleado?->areasSupervisadas->first()?->id
                ?? $user->empleado?->area_id;
        }

        $areaId = $areaForzada ?? ($request->integer('area_id') ?: null);

        return response()->json([
            'snapshot'   => $this->panel->snapshotDelDia($areaId),
            'updated_at' => now()->toIso8601String(),
        ]);
    }
}
