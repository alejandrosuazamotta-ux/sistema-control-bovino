<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VacaUpdateRequest extends FormRequest
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
        // Obtener el ID de la ruta (puede ser 'vaca' o 'id' dependiendo de la ruta)
        $vacaId = $this->route('vaca') ?? $this->route('id');
        
        // Si es un objeto Vaca, obtener el ID
        if (is_object($vacaId)) {
            $vacaId = $vacaId->id_vaca;
        }
        
        return [
            'codigo' => [
                'required',
                'string',
                'max:20',
                Rule::unique('vacas', 'codigo')->ignore($vacaId, 'id_vaca')
            ],
            'fecha_nacimiento' => 'nullable|date|before_or_equal:today',
            'raza' => 'nullable|string|max:50',
            'peso_kg' => 'nullable|numeric|min:0|max:2000',
            'estado_salud' => 'required|in:Sana,En tratamiento,En observación',
            'estado_reproductivo' => 'required|in:Celo,Preñada,Lactancia,Descanso',
            'id_potrero' => 'nullable|exists:potreros,id_potrero',
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
            'codigo.required' => 'El código de identificación es obligatorio.',
            'codigo.unique' => 'Ya existe una vaca con ese código.',
            'codigo.max' => 'El código no puede tener más de 20 caracteres.',
            'raza.max' => 'La raza no puede tener más de 50 caracteres.',
            'fecha_nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'estado_salud.required' => 'El estado de salud es obligatorio.',
            'estado_salud.in' => 'El estado de salud debe ser válido.',
            'estado_reproductivo.required' => 'El estado reproductivo es obligatorio.',
            'estado_reproductivo.in' => 'El estado reproductivo debe ser válido.',
            'id_potrero.exists' => 'El potrero seleccionado no existe.',
        ];
    }
}
