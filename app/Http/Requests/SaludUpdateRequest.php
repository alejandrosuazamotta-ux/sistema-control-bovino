<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaludUpdateRequest extends FormRequest
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
        $rules = [
            'id_vaca' => 'required|exists:vacas,id_vaca',
            'tipo_registro' => 'required|in:Vacunación,Tratamiento,Prueba mastitis,Prueba Brucelosis,Prueba Tuberculosis,Otro',
            'fecha' => 'required|date|before_or_equal:today',
            'descripcion' => 'nullable|string|max:500',
            'id_personal' => 'nullable|exists:personal,id_personal',
        ];

        // Si es una prueba sanitaria, validar campos adicionales
        if (in_array($this->tipo_registro, ['Prueba mastitis', 'Prueba Brucelosis', 'Prueba Tuberculosis'])) {
            $rules['tipo_prueba'] = 'required|in:Mastitis,Brucelosis,Tuberculosis,Otra';
            $rules['resultado'] = 'required|in:Positivo,Negativo,Pendiente';
            $rules['fecha_resultado'] = 'nullable|date|after_or_equal:fecha|before_or_equal:today';
            $rules['responsable_prueba'] = 'nullable|string|max:100';
            $rules['observaciones'] = 'nullable|string|max:1000';
            
            // Si es mastitis, validar severidad y tratamiento
            if ($this->tipo_prueba === 'Mastitis') {
                $rules['severidad'] = 'nullable|in:Leve,Moderada,Severa';
                $rules['tratamiento_sugerido'] = 'nullable|string|max:500';
            }
            
            // Si es brucelosis o tuberculosis, validar acta
            if (in_array($this->tipo_prueba, ['Brucelosis', 'Tuberculosis'])) {
                $rules['acta'] = 'nullable|string|max:100';
            }
        }

        return $rules;
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
            'tipo_registro.required' => 'El tipo de registro es obligatorio.',
            'tipo_registro.in' => 'El tipo de registro debe ser válido.',
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha debe ser válida.',
            'fecha.before_or_equal' => 'La fecha no puede ser posterior al día actual.',
            'tipo_prueba.required' => 'El tipo de prueba es obligatorio para pruebas sanitarias.',
            'tipo_prueba.in' => 'El tipo de prueba debe ser válido.',
            'resultado.required' => 'El resultado de la prueba es obligatorio.',
            'resultado.in' => 'El resultado debe ser Positivo, Negativo o Pendiente.',
            'fecha_resultado.date' => 'La fecha de resultado debe ser válida.',
            'fecha_resultado.after_or_equal' => 'La fecha de resultado no puede ser anterior a la fecha de la prueba.',
            'fecha_resultado.before_or_equal' => 'La fecha de resultado no puede ser posterior al día actual.',
            'severidad.in' => 'La severidad debe ser Leve, Moderada o Severa.',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres.',
            'tratamiento_sugerido.max' => 'El tratamiento sugerido no puede exceder 500 caracteres.',
            'acta.max' => 'El número de acta no puede exceder 100 caracteres.',
            'responsable_prueba.max' => 'El nombre del responsable no puede exceder 100 caracteres.',
            'observaciones.max' => 'Las observaciones no pueden exceder 1000 caracteres.',
            'id_personal.exists' => 'El personal seleccionado no existe.',
        ];
    }
}
