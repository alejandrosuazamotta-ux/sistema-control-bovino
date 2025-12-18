<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsoMedicamentoUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_medicamento' => 'required|exists:medicamentos,id_medicamento',
            'id_vaca' => 'required|exists:vacas,id_vaca',
            'fecha_aplicacion' => 'required|date|before_or_equal:today',
            'dosis_aplicada' => 'nullable|numeric|min:0|max:99999.99',
            'unidad_dosis' => 'nullable|string|max:20',
            'id_personal' => 'nullable|exists:personal,id_personal',
            'observaciones' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id_medicamento.required' => 'Debe seleccionar un medicamento.',
            'id_medicamento.exists' => 'El medicamento seleccionado no existe.',
            'id_vaca.required' => 'Debe seleccionar una vaca.',
            'id_vaca.exists' => 'La vaca seleccionada no existe.',
            'fecha_aplicacion.required' => 'La fecha de aplicación es obligatoria.',
            'fecha_aplicacion.date' => 'La fecha debe ser válida.',
            'fecha_aplicacion.before_or_equal' => 'La fecha no puede ser posterior al día actual.',
            'dosis_aplicada.numeric' => 'La dosis debe ser un número válido.',
            'dosis_aplicada.min' => 'La dosis no puede ser negativa.',
            'id_personal.exists' => 'El personal seleccionado no existe.',
            'observaciones.max' => 'Las observaciones no pueden exceder 500 caracteres.',
        ];
    }
}
