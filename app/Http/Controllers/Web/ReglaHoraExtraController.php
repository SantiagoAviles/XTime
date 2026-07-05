<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reglas\StoreReglaHoraExtraRequest;
use App\Models\ReglaHoraExtra;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReglaHoraExtraController extends Controller
{
    public function index(): View
    {
        return view('reglas-horas-extra.index', [
            'reglas' => ReglaHoraExtra::orderByDesc('id')->paginate(15),
        ]);
    }

    public function store(StoreReglaHoraExtraRequest $request): RedirectResponse
    {
        ReglaHoraExtra::create($request->validated());

        return back()->with('success', 'Regla de horas extra registrada.');
    }

    public function update(StoreReglaHoraExtraRequest $request, ReglaHoraExtra $regla): RedirectResponse
    {
        $regla->update($request->validated());

        return back()->with('success', 'Regla actualizada.');
    }

    public function destroy(ReglaHoraExtra $regla): RedirectResponse
    {
        $regla->delete();

        return back()->with('success', 'Regla eliminada.');
    }
}
