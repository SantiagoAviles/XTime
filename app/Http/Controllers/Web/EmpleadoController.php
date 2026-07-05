<?php

namespace App\Http\Controllers\Web;

use App\Domain\Empleados\EmpleadoService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Empleados\StoreEmpleadoRequest;
use App\Http\Requests\Empleados\UpdateEmpleadoRequest;
use App\Models\Area;
use App\Models\Empleado;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class EmpleadoController extends Controller
{
    public function __construct(private readonly EmpleadoService $empleados)
    {
    }

    public function index(Request $request): View
    {
        $empleados = $this->empleados->listar($request->only([
            'busqueda', 'area_id', 'estado', 'incluir_inactivos',
        ]));

        return view('empleados.index', [
            'empleados' => $empleados,
            'areas'     => Area::where('is_active', true)->orderBy('nombre')->get(),
            'filtros'   => $request->only(['busqueda', 'area_id', 'estado', 'incluir_inactivos']),
        ]);
    }

    public function create(): View
    {
        return view('empleados.create', [
            'areas' => Area::where('is_active', true)->orderBy('nombre')->get(),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function store(StoreEmpleadoRequest $request): RedirectResponse
    {
        $empleado = $this->empleados->crear($request->validated(), $request->user());

        return redirect()
            ->route('empleados.show', $empleado)
            ->with('success', 'Empleado registrado correctamente.');
    }

    public function show(Empleado $empleado): View
    {
        $empleado->load(['area', 'user.roles', 'asignacionesTurno.turno']);

        return view('empleados.show', ['empleado' => $empleado]);
    }

    public function edit(Empleado $empleado): View
    {
        $empleado->load(['area', 'user.roles']);

        return view('empleados.edit', [
            'empleado' => $empleado,
            'areas'    => Area::where('is_active', true)->orderBy('nombre')->get(),
            'roles'    => Role::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateEmpleadoRequest $request, Empleado $empleado): RedirectResponse
    {
        $this->empleados->actualizar($empleado, $request->validated(), $request->user());

        return redirect()
            ->route('empleados.show', $empleado)
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(Request $request, Empleado $empleado): RedirectResponse
    {
        $this->empleados->desactivar($empleado, $request->user());

        return redirect()
            ->route('empleados.index')
            ->with('success', 'Empleado dado de baja. Su historial se conserva.');
    }

    public function restore(Request $request, int $empleadoId): RedirectResponse
    {
        $empleado = Empleado::withTrashed()->findOrFail($empleadoId);
        $this->empleados->reactivar($empleado, $request->user());

        return redirect()
            ->route('empleados.show', $empleado)
            ->with('success', 'Empleado reactivado.');
    }
}
