<?php

namespace App\Http\Requests\Empleados;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('edit_employees')
            || $this->user()?->hasAnyRole(['Administrador', 'RRHH']);
    }

    public function rules(): array
    {
        $empleadoId = $this->route('empleado')?->id ?? $this->route('empleado');

        return [
            'nombres'       => ['required', 'string', 'max:100'],
            'apellidos'     => ['required', 'string', 'max:100'],
            'dni'           => ['required', 'digits:8', Rule::unique('empleados', 'dni')->ignore($empleadoId)],
            'cargo'         => ['nullable', 'string', 'max:120'],
            'telefono'      => ['nullable', 'digits:9'],
            'area_id'       => ['required', 'integer', Rule::exists('areas', 'id')],
            'fecha_ingreso' => ['nullable', 'date', 'before_or_equal:today'],
            'estado'        => ['nullable', Rule::in(['activo', 'inactivo'])],
            'rol'           => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'dni.unique'      => 'El DNI ya está registrado en el sistema.',
            'dni.digits'      => 'El DNI debe tener exactamente 8 dígitos.',
            'telefono.digits' => 'El teléfono debe tener 9 dígitos.',
            'area_id.required'=> 'Debe seleccionar un área para el empleado.',
        ];
    }
}
