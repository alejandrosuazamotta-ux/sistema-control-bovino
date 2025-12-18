<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RotacionPotrerosPasanteUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_potrero_origen' => 'required|exists:potreros,id_potrero',
            'id_potrero_destino' => 'required|exists:potreros,id_potrero|different:id_potrero_origen',
            'id_vaca' => 'required|exists:vacas,id_vaca',
            'fecha_rotacion' => 'required|date|before_or_equal:today',
            'motivo' => 'nullable|string|max:500',
            'observaciones' => 'nullable|string|max:1000',
            'evidencia_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ];
    }
}
