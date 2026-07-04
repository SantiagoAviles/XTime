<?php

namespace App\Http\Requests\Areas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage_areas')
            || $this->user()?->hasAnyRole(['Administrador', 'RRHH']);
    }

    public function rules(): array
    {
        return [
            'nombre'      => ['required', 'string', 'max:100', Rule::unique('areas', 'nombre')],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'is_active'   => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe un área con ese nombre.',
        ];
    }
}
