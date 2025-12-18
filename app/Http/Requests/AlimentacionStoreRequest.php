<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AlimentacionStoreRequest extends FormRequest
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
            'id_vaca' => ['required', 'exists:vacas,id_vaca'],
            'fecha' => ['required', 'date', 'before_or_equal:today'],
            'tipo_alimento' => ['required', 'in:Ensilaje,Pasto,Concentrado,Subproducto,Otro'],
            'cantidad' => ['nullable', 'numeric', 'min:0', 'max:999.99'],
            'observaciones' => ['nullable', 'string', 'max:500'],
            'id_personal' => ['nullable', 'exists:personal,id_personal'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'id_vaca.required' => 'Debe seleccionar una vaca.',
            'id_vaca.exists' => 'La vaca seleccionada no existe.',
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha debe ser válida.',
            'fecha.before_or_equal' => 'La fecha no puede ser posterior al día actual.',
            'tipo_alimento.required' => 'El tipo de alimento es obligatorio.',
            'tipo_alimento.in' => 'El tipo de alimento debe ser válido.',
            'cantidad.numeric' => 'La cantidad debe ser un número válido.',
            'cantidad.min' => 'La cantidad debe ser mayor o igual a 0.',
            'cantidad.max' => 'La cantidad no puede exceder 999.99 kg.',
            'observaciones.max' => 'Las observaciones no pueden exceder 500 caracteres.',
            'id_personal.exists' => 'El personal seleccionado no existe.',
        ];
    }
}
