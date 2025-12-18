<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProduccionLecheraUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // La autorización se maneja en el middleware de rutas
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $produccionId = $this->route('produccion_lechera') ?? $this->route('id');
        
        return [
            'id_vaca' => 'required|exists:vacas,id_vaca',
            'fecha' => 'required|date|before_or_equal:today',
            'turno' => 'required|in:AM,PM',
            'cantidad_leche' => 'required|numeric|min:0.01|max:999.99',
            'destino' => 'required|in:Agroindustria,Lechero,Particular,Consumo',
            'valor_unidad' => 'nullable|numeric|min:0|max:999999.99',
            'id_personal' => 'required|exists:personal,id_personal',
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
            'fecha.required' => 'La fecha de producción es obligatoria.',
            'fecha.date' => 'La fecha debe ser una fecha válida.',
            'fecha.before_or_equal' => 'La fecha no puede ser posterior al día actual.',
            'turno.required' => 'El turno es obligatorio.',
            'turno.in' => 'El turno debe ser AM o PM.',
            'cantidad_leche.required' => 'La cantidad de leche es obligatoria.',
            'cantidad_leche.numeric' => 'La cantidad debe ser un número válido.',
            'cantidad_leche.min' => 'La cantidad debe ser mayor a 0.',
            'cantidad_leche.max' => 'La cantidad no puede exceder 999.99 litros.',
            'destino.required' => 'El destino del producto es obligatorio.',
            'destino.in' => 'El destino debe ser válido.',
            'valor_unidad.numeric' => 'El valor unitario debe ser un número válido.',
            'valor_unidad.min' => 'El valor unitario no puede ser negativo.',
            'valor_unidad.max' => 'El valor unitario no puede exceder 999999.99.',
            'id_personal.required' => 'Debe seleccionar el personal responsable.',
            'id_personal.exists' => 'El personal seleccionado no existe.',
            'observaciones.max' => 'Las observaciones no pueden exceder 500 caracteres.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Asegurar que excluida_por_retiro sea false por defecto
        $this->merge([
            'excluida_por_retiro' => $this->excluida_por_retiro ?? false,
        ]);
    }
}
