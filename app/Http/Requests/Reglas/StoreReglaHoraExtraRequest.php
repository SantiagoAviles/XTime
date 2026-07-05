<?php

namespace App\Http\Requests\Reglas;

use Illuminate\Foundation\Http\FormRequest;

class StoreReglaHoraExtraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage_reglas_horas_extra')
            || $this->user()?->hasAnyRole(['Administrador', 'RRHH']);
    }

    public function rules(): array
    {
        return [
            'nombre'                 => ['required', 'string', 'max:100'],
            'recargo_primeras_horas' => ['required', 'numeric', 'min:0', 'max:200'],
            'umbral_primeras_horas'  => ['required', 'integer', 'min:1', 'max:24'],
            'recargo_adicional'      => ['required', 'numeric', 'min:0', 'max:200'],
            'aplica_nocturno'        => ['nullable', 'boolean'],
            'is_active'              => ['nullable', 'boolean'],
        ];
    }
}
