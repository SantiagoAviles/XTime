<?php

namespace App\Http\Requests\Justificaciones;

use App\Models\Justificacion;
use App\Models\Marcacion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJustificacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('request_justifications')
            || $this->user()?->hasRole('Empleado');
    }

    public function rules(): array
    {
        return [
            'fecha'    => ['required', 'date', 'before_or_equal:today'],
            'tipo'     => ['required', Rule::in([Justificacion::TIPO_AUSENCIA, Justificacion::TIPO_TARDANZA])],
            'motivo'   => ['required', 'string', 'min:10', 'max:1000'],
            'adjunto'  => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $empleadoId = $this->user()->empleado?->id;
            if (! $empleadoId) {
                $v->errors()->add('empleado', 'Tu usuario no está vinculado a un empleado.');
                return;
            }

            $fecha = $this->input('fecha');
            $tipo  = $this->input('tipo');

            // RN-007: requiere una marcación previa (ausencia o tardanza) en esa fecha.
            $marcacion = Marcacion::where('empleado_id', $empleadoId)
                ->whereDate('fecha', $fecha)
                ->first();

            if (! $marcacion) {
                $v->errors()->add('fecha', 'No hay una marcación (ausencia o tardanza) registrada para esa fecha.');
                return;
            }

            $estadosValidos = $tipo === Justificacion::TIPO_AUSENCIA
                ? [Marcacion::ESTADO_AUSENCIA]
                : [Marcacion::ESTADO_TARDANZA];

            if (! in_array($marcacion->estado, $estadosValidos, true)) {
                $v->errors()->add('fecha', 'La marcación de esa fecha no es del tipo seleccionado.');
            }

            // No duplicados.
            $existe = Justificacion::where('empleado_id', $empleadoId)
                ->whereDate('fecha', $fecha)
                ->where('tipo', $tipo)
                ->exists();
            if ($existe) {
                $v->errors()->add('fecha', 'Ya existe una solicitud para esa fecha y tipo.');
            }
        });
    }
}
