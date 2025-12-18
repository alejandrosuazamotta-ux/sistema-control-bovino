<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApoyoOrdeñoPasanteUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_vaca' => 'required|exists:vacas,id_vaca',
            'fecha_ordeno' => 'required|date|before_or_equal:today',
            'turno' => ['required', Rule::in(['AM', 'PM'])],
            'cantidad_leche' => 'nullable|numeric|min:0|max:1000',
            'calidad_leche' => ['nullable', Rule::in(['Excelente', 'Buena', 'Regular', 'Deficiente'])],
            'observaciones' => 'nullable|string|max:1000',
            'evidencia_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ];
    }
}

