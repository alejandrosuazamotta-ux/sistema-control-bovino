<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CriaUpdateRequest extends FormRequest
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
            'id_vaca_madre' => 'required|exists:vacas,id_vaca',
            'nombre_cria' => 'nullable|string|max:100',
            'sexo' => 'required|in:Macho,Hembra',
            'fecha_nacimiento' => 'required|date|before_or_equal:today',
            'peso' => 'required|numeric|min:0.01|max:100',
            'fecha_tatuado' => 'nullable|date|before_or_equal:today|after_or_equal:fecha_nacimiento',
            'concepcion' => 'nullable|in:IA,Monta Natural,Transferencia Embrionaria',
            'sinigan' => 'nullable|string|max:50',
            'estado_destete' => 'required|in:No destetada,Destetada',
            'fecha_destete' => 'nullable|date|before_or_equal:today|after_or_equal:fecha_nacimiento|required_if:estado_destete,Destetada',
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
            'id_vaca_madre.required' => 'Debe seleccionar la vaca madre.',
            'id_vaca_madre.exists' => 'La vaca madre seleccionada no existe.',
            'sexo.required' => 'El sexo de la cría es obligatorio.',
            'sexo.in' => 'El sexo debe ser Macho o Hembra.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.date' => 'La fecha debe ser válida.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'peso.required' => 'El peso es obligatorio.',
            'peso.numeric' => 'El peso debe ser un número válido.',
            'peso.min' => 'El peso debe ser mayor a 0.',
            'peso.max' => 'El peso no puede exceder 100 kg.',
            'fecha_tatuado.date' => 'La fecha de tatuado debe ser válida.',
            'fecha_tatuado.before_or_equal' => 'La fecha de tatuado no puede ser futura.',
            'fecha_tatuado.after_or_equal' => 'La fecha de tatuado no puede ser anterior a la fecha de nacimiento.',
            'concepcion.in' => 'El método de concepción debe ser válido.',
            'sinigan.max' => 'El código SINIGAN no puede exceder 50 caracteres.',
            'estado_destete.required' => 'El estado de destete es obligatorio.',
            'estado_destete.in' => 'El estado de destete debe ser válido.',
            'fecha_destete.date' => 'La fecha de destete debe ser válida.',
            'fecha_destete.before_or_equal' => 'La fecha de destete no puede ser futura.',
            'fecha_destete.after_or_equal' => 'La fecha de destete no puede ser anterior a la fecha de nacimiento.',
            'fecha_destete.required_if' => 'La fecha de destete es obligatoria cuando el estado es Destetada.',
            'observaciones.max' => 'Las observaciones no pueden exceder 500 caracteres.',
        ];
    }
}

