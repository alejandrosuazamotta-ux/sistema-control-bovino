<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PruebaSanitariaStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // La autorización se maneja en Policies
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
            'tipo_prueba' => 'required|in:Mastitis,Brucelosis,Tuberculosis',
            'fecha_prueba' => 'required|date|before_or_equal:today',
            'resultado' => 'required|in:Positivo,Negativo,Pendiente',
            'fecha_resultado' => 'nullable|date|after_or_equal:fecha_prueba|before_or_equal:today',
            'severidad' => 'nullable|in:Leve,Moderada,Severa',
            'tratamiento_sugerido' => 'nullable|string|max:1000',
            'acta' => 'nullable|string|max:100',
            'responsable_prueba' => 'nullable|string|max:100',
            'id_personal' => 'nullable|exists:personal,id_personal',
            'observaciones' => 'nullable|string|max:1000',
            'evidencia_archivo' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240', // 10MB max
        ];

        // Si es Pasante, evidencia es obligatoria
        if (auth()->user()->hasRole('pasante')) {
            $rules['evidencia_archivo'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:10240';
        }

        // Si el resultado es Positivo y es Mastitis, severidad es obligatoria
        if ($this->input('resultado') === 'Positivo' && $this->input('tipo_prueba') === 'Mastitis') {
            $rules['severidad'] = 'required|in:Leve,Moderada,Severa';
        }

        return $rules;
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
            'tipo_prueba.required' => 'El tipo de prueba es obligatorio.',
            'tipo_prueba.in' => 'El tipo de prueba debe ser: Mastitis, Brucelosis o Tuberculosis.',
            'fecha_prueba.required' => 'La fecha de la prueba es obligatoria.',
            'fecha_prueba.date' => 'La fecha debe ser una fecha válida.',
            'fecha_prueba.before_or_equal' => 'La fecha no puede ser posterior al día actual.',
            'resultado.required' => 'El resultado es obligatorio.',
            'resultado.in' => 'El resultado debe ser: Positivo, Negativo o Pendiente.',
            'fecha_resultado.after_or_equal' => 'La fecha del resultado no puede ser anterior a la fecha de la prueba.',
            'fecha_resultado.before_or_equal' => 'La fecha del resultado no puede ser posterior al día actual.',
            'severidad.required' => 'La severidad es obligatoria para pruebas de Mastitis positivas.',
            'severidad.in' => 'La severidad debe ser: Leve, Moderada o Severa.',
            'evidencia_archivo.required' => 'La evidencia (imagen o PDF) es obligatoria para pasantes.',
            'evidencia_archivo.file' => 'El archivo de evidencia debe ser válido.',
            'evidencia_archivo.mimes' => 'La evidencia debe ser una imagen (jpg, jpeg, png) o un PDF.',
            'evidencia_archivo.max' => 'El archivo de evidencia no puede exceder 10MB.',
        ];
    }
}
