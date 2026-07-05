<?php

namespace App\Http\Requests\Turnos;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AsignarTurnoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage_turnos')
            || $this->user()?->hasAnyRole(['Administrador', 'RRHH']);
    }

    public function rules(): array
    {
        return [
            'empleado_id'   => ['required', 'integer', Rule::exists('empleados', 'id')],
            'turno_id'      => ['required', 'integer', Rule::exists('turnos', 'id')],
            'vigente_desde' => ['required', 'date'],
            'vigente_hasta' => ['nullable', 'date', 'after_or_equal:vigente_desde'],
            'observacion'   => ['nullable', 'string', 'max:300'],
        ];
    }
}
