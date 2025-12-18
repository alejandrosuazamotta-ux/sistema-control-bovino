<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InventarioBodegaUpdateRequest extends FormRequest
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
        $inventarioId = $this->route('inventario_bodega');
        
        return [
            'codigo' => ['required', 'string', 'max:50', Rule::unique('inventario_bodega', 'codigo')->ignore($inventarioId, 'id_inventario')],
            'nombre' => 'required|string|max:200',
            'tipo_producto' => ['required', Rule::in(['Medicamento', 'Insumo', 'Alimento', 'Equipo', 'Otro'])],
            'id_medicamento' => 'nullable|exists:medicamentos,id_medicamento',
            'unidad_medida' => 'required|string|max:20',
            'stock_actual' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'stock_maximo' => 'nullable|numeric|min:0|gte:stock_minimo',
            'precio_unitario' => 'required|numeric|min:0',
            'proveedor' => 'nullable|string|max:200',
            'fecha_vencimiento' => 'nullable|date',
            'lote' => 'nullable|string|max:50',
            'ubicacion_bodega' => 'nullable|string|max:500',
            'observaciones' => 'nullable|string|max:1000',
            'activo' => 'nullable|boolean',
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
            'codigo.required' => 'El código del producto es obligatorio.',
            'codigo.unique' => 'Ya existe un producto con ese código.',
            'codigo.max' => 'El código no puede exceder 50 caracteres.',
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 200 caracteres.',
            'tipo_producto.required' => 'El tipo de producto es obligatorio.',
            'tipo_producto.in' => 'El tipo de producto no es válido.',
            'id_medicamento.exists' => 'El medicamento seleccionado no existe.',
            'unidad_medida.required' => 'La unidad de medida es obligatoria.',
            'stock_actual.required' => 'El stock actual es obligatorio.',
            'stock_actual.numeric' => 'El stock actual debe ser un número.',
            'stock_actual.min' => 'El stock actual no puede ser negativo.',
            'stock_minimo.required' => 'El stock mínimo es obligatorio.',
            'stock_minimo.numeric' => 'El stock mínimo debe ser un número.',
            'stock_minimo.min' => 'El stock mínimo no puede ser negativo.',
            'stock_maximo.gte' => 'El stock máximo debe ser mayor o igual al stock mínimo.',
            'precio_unitario.required' => 'El precio unitario es obligatorio.',
            'precio_unitario.numeric' => 'El precio unitario debe ser un número.',
            'precio_unitario.min' => 'El precio unitario no puede ser negativo.',
            'fecha_vencimiento.date' => 'La fecha de vencimiento debe ser una fecha válida.',
        ];
    }
}
