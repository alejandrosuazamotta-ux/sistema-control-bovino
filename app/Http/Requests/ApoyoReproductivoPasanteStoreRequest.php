<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApoyoReproductivoPasanteStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_vaca' => 'required|exists:vacas,id_vaca',
            'fecha_actividad' => 'required|date|before_or_equal:today',
            'tipo_actividad' => ['required', Rule::in(['Palpación', 'Inseminación', 'Seguimiento Celo', 'Control Gestación', 'Parto', 'Otro'])],
            'observaciones' => 'nullable|string|max:1000',
            'evidencia_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'evidencia_documento' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'id_vaca.required' => 'Debe seleccionar una vaca.',
            'fecha_actividad.required' => 'La fecha de actividad es obligatoria.',
            'tipo_actividad.required' => 'El tipo de actividad es obligatorio.',
        ];
    }
}
