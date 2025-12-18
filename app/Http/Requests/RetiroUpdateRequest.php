<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RetiroUpdateRequest extends FormRequest
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
            'id_vaca' => 'required|exists:vacas,id_vaca',
            'id_uso_medicamento' => 'nullable|exists:uso_medicamentos,id_uso',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'tipo_retiro' => 'required|in:Ordeño,Producción',
            'activo' => 'nullable|boolean',
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
            'id_vaca.required' => 'Debe seleccionar una vaca.',
            'id_vaca.exists' => 'La vaca seleccionada no existe.',
            'id_uso_medicamento.exists' => 'El uso de medicamento seleccionado no existe.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date' => 'La fecha de inicio debe ser válida.',
            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.date' => 'La fecha de fin debe ser válida.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
            'tipo_retiro.required' => 'El tipo de retiro es obligatorio.',
            'tipo_retiro.in' => 'El tipo de retiro debe ser Ordeño o Producción.',
            'observaciones.max' => 'Las observaciones no pueden exceder 500 caracteres.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'activo' => $this->activo ?? true,
        ]);
    }
}
