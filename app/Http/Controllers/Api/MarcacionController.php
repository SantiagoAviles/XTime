<?php

namespace App\Http\Controllers\Api;

use App\Domain\Asistencias\MarcacionService;
use App\Domain\Asistencias\QrService;
use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\MetodoMarcacion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MarcacionController extends Controller
{
    public function __construct(
        private readonly MarcacionService $marcacion,
        private readonly QrService $qr,
    ) {}

    public function registrar(Request $request): JsonResponse
    {
        $data = $request->validate([
            'identificador' => ['required', 'string', 'max:120'],
            'metodo'        => ['required', Rule::in([
                MetodoMarcacion::CODIGO_QR,
                MetodoMarcacion::CODIGO_DNI,
            ])],
        ], [
            'identificador.required' => 'Debe enviar el identificador (QR o DNI).',
        ]);

        $empleado = $data['metodo'] === MetodoMarcacion::CODIGO_QR
            ? $this->qr->resolverEmpleadoPorCodigo($data['identificador'])
            : Empleado::where('dni', $data['identificador'])->whereNull('deleted_at')->first();

        if (! $empleado) {
            return response()->json([
                'ok'      => false,
                'codigo'  => 'EMPLEADO_NO_ENCONTRADO',
                'mensaje' => 'No se encontró un empleado con ese ' . $data['metodo'] . '.',
            ], 404);
        }

        $resultado = $this->marcacion->registrar(
            empleado: $empleado,
            codigoMetodo: $data['metodo'],
            registradoPor: null,
            ip: $request->ip(),
        );

        $http = $resultado['ok'] ? 200 : 422;

        return response()->json([
            'ok'        => $resultado['ok'],
            'codigo'    => $resultado['codigo'],
            'mensaje'   => $resultado['mensaje'],
            'empleado'  => $empleado ? [
                'id'             => $empleado->id,
                'nombre'         => $empleado->nombreCompleto(),
                'area'           => $empleado->area?->nombre,
            ] : null,
            'marcacion' => $resultado['marcacion'] ? [
                'fecha'        => $resultado['marcacion']->fecha?->toDateString(),
                'hora_entrada' => optional($resultado['marcacion']->hora_entrada)->format('H:i:s'),
                'hora_salida'  => optional($resultado['marcacion']->hora_salida)->format('H:i:s'),
                'estado'       => $resultado['marcacion']->estado,
            ] : null,
        ], $http);
    }
}
