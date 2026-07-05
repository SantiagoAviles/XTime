<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Feriados\StoreFeriadoRequest;
use App\Models\Feriado;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeriadoController extends Controller
{
    public function index(Request $request): View
    {
        $año = (int) ($request->input('año', now()->year));

        $feriados = Feriado::query()
            ->whereYear('fecha', $año)
            ->orderBy('fecha')
            ->paginate(50)
            ->withQueryString();

        return view('feriados.index', [
            'feriados' => $feriados,
            'año'      => $año,
        ]);
    }

    public function store(StoreFeriadoRequest $request): RedirectResponse
    {
        Feriado::create([
            'fecha'       => $request->validated()['fecha'],
            'descripcion' => $request->validated()['descripcion'],
            'nacional'    => (bool) ($request->validated()['nacional'] ?? false),
            'is_active'   => (bool) ($request->validated()['is_active'] ?? true),
        ]);

        return back()->with('success', 'Feriado registrado.');
    }

    public function update(StoreFeriadoRequest $request, Feriado $feriado): RedirectResponse
    {
        $feriado->update($request->validated());
        return back()->with('success', 'Feriado actualizado.');
    }

    public function destroy(Feriado $feriado): RedirectResponse
    {
        $feriado->delete();
        return back()->with('success', 'Feriado eliminado.');
    }
}
