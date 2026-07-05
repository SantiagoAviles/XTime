<?php

namespace App\Http\Controllers\Web;

use App\Domain\Asistencias\MarcacionService;
use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\MetodoMarcacion;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MarcacionManualController extends Controller
{
    public function __construct(private readonly MarcacionService $marcacion)
    {
    }

    public function create(Request $request): View
    {
        return view('asistencias.manual.create', [
            'empleados' => Empleado::query()
                ->whereNull('deleted_at')
                ->where('estado', 'activo')
                ->orderBy('apellidos')
                ->get(['id', 'nombres', 'apellidos', 'dni']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'empleado_id'  => ['required', 'integer', Rule::exists('empleados', 'id')],
            'fecha_hora'   => ['required', 'date'],
            'motivo'       => ['required', 'string', 'min:5', 'max:300'],
        ], [
            'motivo.required' => 'La justificación es obligatoria para marcación manual.',
            'motivo.min'      => 'La justificación debe tener al menos 5 caracteres.',
        ]);

        $empleado = Empleado::findOrFail($data['empleado_id']);
        $momento  = CarbonImmutable::parse($data['fecha_hora']);

        $resultado = $this->marcacion->registrar(
            empleado: $empleado,
            codigoMetodo: MetodoMarcacion::CODIGO_MANUAL,
            momento: $momento,
            registradoPor: $request->user(),
            motivoManual: $data['motivo'],
            ip: $request->ip(),
        );

        if (! $resultado['ok']) {
            return back()->withInput()->with('error', $resultado['mensaje']);
        }

        return redirect()
            ->route('asistencias.panel')
            ->with('success', 'Marcación manual registrada y auditada.');
    }
}
