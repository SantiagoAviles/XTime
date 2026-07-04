<?php

namespace App\Http\Controllers\Web;

use App\Domain\Empleados\AreaService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Areas\StoreAreaRequest;
use App\Http\Requests\Areas\UpdateAreaRequest;
use App\Models\Area;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AreaController extends Controller
{
    public function __construct(private readonly AreaService $areas)
    {
    }

    public function index(Request $request): View
    {
        $areas = $this->areas->listar($request->only(['busqueda', 'estado']));

        return view('areas.index', [
            'areas'   => $areas,
            'filtros' => $request->only(['busqueda', 'estado']),
        ]);
    }

    public function create(): View
    {
        return view('areas.create');
    }

    public function store(StoreAreaRequest $request): RedirectResponse
    {
        $area = $this->areas->crear($request->validated());

        return redirect()
            ->route('areas.show', $area)
            ->with('success', 'Área creada correctamente.');
    }

    public function show(Area $area): View
    {
        $area->load(['empleados' => fn ($q) => $q->orderBy('apellidos'), 'supervisores']);

        return view('areas.show', ['area' => $area]);
    }

    public function edit(Area $area): View
    {
        return view('areas.edit', ['area' => $area]);
    }

    public function update(UpdateAreaRequest $request, Area $area): RedirectResponse
    {
        $this->areas->actualizar($area, $request->validated());

        return redirect()
            ->route('areas.show', $area)
            ->with('success', 'Área actualizada correctamente.');
    }

    public function destroy(Area $area): RedirectResponse
    {
        try {
            $this->areas->eliminar($area);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('areas.index')
            ->with('success', 'Área eliminada.');
    }
}
