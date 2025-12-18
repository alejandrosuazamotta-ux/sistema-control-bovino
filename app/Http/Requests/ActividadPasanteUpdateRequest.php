<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActividadPasanteUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
            'descripcion.required' => 'La descripción es obligatoria.',
            'fecha_actividad.required' => 'La fecha de actividad es obligatoria.',
        ];
    }
}
