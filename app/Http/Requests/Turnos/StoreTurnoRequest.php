<?php

namespace App\Http\Requests\Turnos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTurnoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage_turnos')
            || $this->user()?->hasAnyRole(['Administrador', 'RRHH']);
    }

    public function rules(): array
    {
        return [
            'nombre'             => ['required', 'string', 'max:100', Rule::unique('turnos', 'nombre')],
            'tipo'               => ['required', Rule::in(['diurno', 'nocturno', 'rotativo', 'flexible'])],
            'hora_entrada'       => ['required', 'date_format:H:i'],
            'hora_salida'        => ['required', 'date_format:H:i'],
            'tolerancia_minutos' => ['nullable', 'integer', 'min:0', 'max:120'],
            'cruza_medianoche'   => ['nullable', 'boolean'],
            'descripcion'        => ['nullable', 'string', 'max:500'],
            'is_active'          => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe un turno con ese nombre.',
        ];
    }
}
