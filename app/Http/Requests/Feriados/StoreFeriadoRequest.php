<?php

namespace App\Http\Requests\Feriados;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFeriadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage_feriados')
            || $this->user()?->hasAnyRole(['Administrador', 'RRHH']);
    }

    public function rules(): array
    {
        $id = $this->route('feriado')?->id ?? $this->route('feriado');

        return [
            'fecha'       => ['required', 'date', Rule::unique('feriados', 'fecha')->ignore($id)],
            'descripcion' => ['required', 'string', 'max:120'],
            'nacional'    => ['nullable', 'boolean'],
            'is_active'   => ['nullable', 'boolean'],
        ];
    }
}
