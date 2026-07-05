<?php

namespace App\Http\Requests\Empleados;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create_employees')
            || $this->user()?->hasAnyRole(['Administrador', 'RRHH']);
    }

    public function rules(): array
    {
        return [
            'nombres'       => ['required', 'string', 'max:100'],
            'apellidos'     => ['required', 'string', 'max:100'],
            'dni'           => ['required', 'digits:8', Rule::unique('empleados', 'dni')],
            'cargo'         => ['nullable', 'string', 'max:120'],
            'telefono'      => ['nullable', 'digits:9'],
            'area_id'       => ['required', 'integer', Rule::exists('areas', 'id')],
            'fecha_ingreso' => ['nullable', 'date', 'before_or_equal:today'],
            'estado'        => ['nullable', Rule::in(['activo', 'inactivo'])],
            'email'         => ['nullable', 'email', 'max:120', Rule::unique('users', 'email')],
            'rol'           => ['nullable', 'string', 'max:50'],
            'password'      => ['nullable', 'string', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'dni.unique'         => 'El DNI ya está registrado en el sistema.',
            'dni.digits'         => 'El DNI debe tener exactamente 8 dígitos.',
            'telefono.digits'    => 'El teléfono debe tener 9 dígitos.',
            'area_id.required'   => 'Debe seleccionar un área para el empleado.',
            'area_id.exists'     => 'El área seleccionada no existe.',
            'email.unique'       => 'Ya existe un usuario con ese correo.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }
}
