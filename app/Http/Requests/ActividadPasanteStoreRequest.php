<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActividadPasanteStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización se maneja en policies
    }

    public function rules(): array
    {
        return [
            'titulo' => 'required|string|max:200',
            'descripcion' => 'required|string',
            'tipo_actividad' => ['required', Rule::in(['Ordeño', 'Reproductivo', 'Rotación Potreros', 'Alimentación', 'Salud', 'General'])],
            'fecha_actividad' => 'required|date|before_or_equal:today',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin' => 'nullable|date_format:H:i|after:hora_inicio',
            'estado' => ['nullable', Rule::in(['Pendiente', 'En Progreso', 'Completada', 'Cancelada'])],
            'observaciones' => 'nullable|string|max:1000',
            'evidencia_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'evidencia_documento' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'El título es obligatorio.',
            'titulo.max' => 'El título no puede tener más de 200 caracteres.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'tipo_actividad.required' => 'El tipo de actividad es obligatorio.',
            'fecha_actividad.required' => 'La fecha de actividad es obligatoria.',
            'fecha_actividad.before_or_equal' => 'La fecha no puede ser futura.',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            'evidencia_foto.image' => 'La evidencia debe ser una imagen.',
            'evidencia_foto.max' => 'La imagen no puede ser mayor a 5MB.',
            'evidencia_documento.max' => 'El documento no puede ser mayor a 10MB.',
        ];
    }
}
