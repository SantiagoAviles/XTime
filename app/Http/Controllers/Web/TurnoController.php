<?php

namespace App\Http\Controllers\Web;

use App\Domain\Horarios\AsignacionTurnoService;
use App\Domain\Horarios\TurnoService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Turnos\AsignarTurnoRequest;
use App\Http\Requests\Turnos\StoreTurnoRequest;
use App\Http\Requests\Turnos\UpdateTurnoRequest;
use App\Models\Empleado;
use App\Models\Turno;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TurnoController extends Controller
{
    public function __construct(
        private readonly TurnoService $turnos,
        private readonly AsignacionTurnoService $asignaciones,
    ) {}

    public function index(Request $request): View
    {
        return view('turnos.index', [
            'turnos'  => $this->turnos->listar($request->only(['busqueda', 'tipo'])),
            'filtros' => $request->only(['busqueda', 'tipo']),
        ]);
    }

    public function create(): View
    {
        return view('turnos.create');
    }

    public function store(StoreTurnoRequest $request): RedirectResponse
    {
        $turno = $this->turnos->crear($request->validated());

        return redirect()->route('turnos.show', $turno)->with('success', 'Turno creado.');
    }

    public function show(Turno $turno): View
    {
        $turno->load(['asignaciones.empleado']);

        return view('turnos.show', ['turno' => $turno]);
    }

    public function edit(Turno $turno): View
    {
        return view('turnos.edit', ['turno' => $turno]);
    }

    public function update(UpdateTurnoRequest $request, Turno $turno): RedirectResponse
    {
        $this->turnos->actualizar($turno, $request->validated());

        return redirect()->route('turnos.show', $turno)->with('success', 'Turno actualizado.');
    }

    public function destroy(Turno $turno): RedirectResponse
    {
        try {
            $this->turnos->eliminar($turno);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('turnos.index')->with('success', 'Turno eliminado.');
    }

    public function asignar(AsignarTurnoRequest $request): RedirectResponse
    {
        $asignacion = $this->asignaciones->asignar($request->validated(), $request->user());

        return back()->with('success', 'Turno asignado a ' . $asignacion->empleado->nombreCompleto());
    }
}
