<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistroReproductivoUpdateRequest extends FormRequest
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
            'id_vaca' => 'required|exists:vacas,id_vaca',
            'tipo_evento' => 'required|in:Inseminación,Parto,Celo,Días abiertos,Palpación',
            'fecha_evento' => 'required|date|before_or_equal:today',
            'resultado_palpacion' => 'nullable|required_if:tipo_evento,Palpación|in:Vacia,Preñada',
            'tiempo_gestacion_dias' => 'nullable|integer|min:0|max:283|required_if:resultado_palpacion,Preñada',
            'fecha_probable_parto' => 'nullable|date|after:fecha_evento',
            'especialista' => 'nullable|string|max:100',
            'dias_abiertos' => 'nullable|integer|min:0',
            'observaciones' => 'nullable|string|max:500',
            'id_personal' => 'nullable|exists:personal,id_personal',
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
            'tipo_evento.required' => 'El tipo de evento es obligatorio.',
            'tipo_evento.in' => 'El tipo de evento seleccionado no es válido.',
            'fecha_evento.required' => 'La fecha del evento es obligatoria.',
            'fecha_evento.date' => 'La fecha debe ser válida.',
            'fecha_evento.before_or_equal' => 'La fecha no puede ser posterior al día actual.',
            'resultado_palpacion.required_if' => 'El resultado de la palpación es obligatorio cuando el tipo de evento es Palpación.',
            'resultado_palpacion.in' => 'El resultado de la palpación debe ser Vacia o Preñada.',
            'tiempo_gestacion_dias.required_if' => 'El tiempo de gestación es obligatorio cuando el resultado es Preñada.',
            'tiempo_gestacion_dias.integer' => 'El tiempo de gestación debe ser un número entero.',
            'tiempo_gestacion_dias.min' => 'El tiempo de gestación no puede ser negativo.',
            'tiempo_gestacion_dias.max' => 'El tiempo de gestación no puede exceder 283 días.',
            'fecha_probable_parto.date' => 'La fecha probable de parto debe ser válida.',
            'fecha_probable_parto.after' => 'La fecha probable de parto debe ser posterior a la fecha del evento.',
            'especialista.max' => 'El nombre del especialista no puede exceder 100 caracteres.',
            'dias_abiertos.integer' => 'Los días abiertos deben ser un número entero.',
            'dias_abiertos.min' => 'Los días abiertos no pueden ser negativos.',
            'observaciones.max' => 'Las observaciones no pueden exceder 500 caracteres.',
            'id_personal.exists' => 'El personal seleccionado no existe.',
        ];
    }
}

