<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TareaPasanteUpdateRequest extends FormRequest
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
            'prioridad' => ['required', Rule::in(['Baja', 'Media', 'Alta', 'Urgente'])],
            'estado' => ['required', Rule::in(['Pendiente', 'En Progreso', 'Completada', 'Cancelada'])],
            'fecha_limite' => 'nullable|date',
            'observaciones' => 'nullable|string|max:1000',
            'evidencia_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'evidencia_documento' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ];
    }
}
