<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MedicamentoUpdateRequest extends FormRequest
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
        $medicamentoId = $this->route('medicamento') ?? $this->route('id');
        
        return [
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('medicamentos', 'nombre')->ignore($medicamentoId, 'id_medicamento')
            ],
            'tipo' => 'nullable|string|max:50',
            'principio_activo' => 'nullable|string|max:100',
            'via_administracion' => 'nullable|string|max:50',
            'dosis' => 'nullable|string|max:100',
            'periodo_retiro_dias' => 'required|integer|min:0|max:365',
            'activo' => 'nullable|boolean',
            'observaciones' => 'nullable|string|max:1000',
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
            'nombre.required' => 'El nombre del medicamento es obligatorio.',
            'nombre.unique' => 'Ya existe un medicamento con ese nombre.',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres.',
            'periodo_retiro_dias.required' => 'El período de retiro es obligatorio.',
            'periodo_retiro_dias.integer' => 'El período de retiro debe ser un número entero.',
            'periodo_retiro_dias.min' => 'El período de retiro no puede ser negativo.',
            'periodo_retiro_dias.max' => 'El período de retiro no puede exceder 365 días.',
            'observaciones.max' => 'Las observaciones no pueden exceder 1000 caracteres.',
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
