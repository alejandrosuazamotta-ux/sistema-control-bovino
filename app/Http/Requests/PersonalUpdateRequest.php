<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PersonalUpdateRequest extends FormRequest
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
        $personalId = $this->route('personal');
        $personal = \App\Models\Personal::find($personalId);
        $userId = $personal?->user?->id;

        return [
            'nombre' => ['required', 'string', 'max:100'],
            'rol' => ['required', 'in:Pasante,Supervisor,Otro'],
            'fecha_contratacion' => ['nullable', 'date'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:6'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:200'],
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
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres.',
            'rol.required' => 'El rol es obligatorio.',
            'rol.in' => 'El rol debe ser válido.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe ser válido.',
            'email.unique' => 'El email ya está registrado.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'telefono.max' => 'El teléfono no puede exceder 20 caracteres.',
            'direccion.max' => 'La dirección no puede exceder 200 caracteres.',
        ];
    }
}
