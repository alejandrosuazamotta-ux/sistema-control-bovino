<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TareaPasanteStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'titulo' => 'required|string|max:200',
            'descripcion' => 'required|string',
            'prioridad' => ['required', Rule::in(['Baja', 'Media', 'Alta', 'Urgente'])],
            'fecha_asignacion' => 'required|date|before_or_equal:today',
            'fecha_limite' => 'nullable|date|after_or_equal:fecha_asignacion',
            'observaciones' => 'nullable|string|max:1000',
            'evidencia_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'evidencia_documento' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Debe seleccionar un pasante.',
            'titulo.required' => 'El título es obligatorio.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'prioridad.required' => 'La prioridad es obligatoria.',
            'fecha_limite.after_or_equal' => 'La fecha límite debe ser posterior o igual a la fecha de asignación.',
        ];
    }
}
