<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MortalidadUpdateRequest extends FormRequest
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
            'fecha' => 'required|date|before_or_equal:today',
            'hora' => 'nullable|date_format:H:i',
            'clasificacion' => 'required|in:Ternero,Novilla,Vaca,Toro,Becerro,Becerra',
            'peso' => 'nullable|numeric|min:0|max:2000',
            'causa' => 'required|string|max:500',
            'acta' => 'nullable|string|max:1000',
            'acta_archivo' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240', // 10MB
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
            'fecha.required' => 'La fecha de muerte es obligatoria.',
            'fecha.date' => 'La fecha debe ser válida.',
            'fecha.before_or_equal' => 'La fecha de muerte no puede ser futura.',
            'hora.date_format' => 'La hora debe tener el formato HH:mm.',
            'clasificacion.required' => 'La clasificación es obligatoria.',
            'clasificacion.in' => 'La clasificación debe ser válida.',
            'peso.numeric' => 'El peso debe ser un número válido.',
            'peso.min' => 'El peso debe ser mayor o igual a 0.',
            'peso.max' => 'El peso no puede exceder 2000 kg.',
            'causa.required' => 'La causa de muerte es obligatoria.',
            'causa.max' => 'La causa no puede exceder 500 caracteres.',
            'acta.max' => 'El acta no puede exceder 1000 caracteres.',
            'acta_archivo.file' => 'La evidencia debe ser un archivo.',
            'acta_archivo.mimes' => 'La evidencia debe ser un archivo de tipo: jpg, jpeg, png, pdf.',
            'acta_archivo.max' => 'La evidencia no debe pesar más de 10MB.',
            'observaciones.max' => 'Las observaciones no pueden exceder 1000 caracteres.',
        ];
    }
}

