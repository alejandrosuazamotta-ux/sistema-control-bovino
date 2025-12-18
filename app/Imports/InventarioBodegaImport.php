<?php

namespace App\Imports;

use App\Models\InventarioBodega;
use App\Models\Medicamento;
use App\Services\InventarioBodegaService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class InventarioBodegaImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnFailure
{
    use Importable, SkipsFailures;

    protected InventarioBodegaService $service;
    protected array $errors = [];

    public function __construct(InventarioBodegaService $service)
    {
        $this->service = $service;
    }

    public function model(array $row)
    {
        try {
            // Buscar medicamento si se proporciona
            $medicamento = null;
            if (isset($row['medicamento']) && !empty($row['medicamento'])) {
                $medicamento = Medicamento::where('nombre', $row['medicamento'])->first();
                if (!$medicamento) {
                    $this->errors[] = "Fila " . ($this->getReadRows() + 1) . ": Medicamento '{$row['medicamento']}' no encontrado.";
                    return null;
                }
            }

            $fechaVencimiento = $this->parseFecha($row['fecha_vencimiento'] ?? null);

            $data = [
                'codigo' => $row['codigo'] ?? null,
                'nombre' => $row['nombre'] ?? null,
                'tipo_producto' => $row['tipo_producto'] ?? 'Insumo',
                'id_medicamento' => $medicamento->id_medicamento ?? null,
                'unidad_medida' => $row['unidad_medida'] ?? 'Unidad',
                'stock_actual' => isset($row['stock_actual']) ? (float)$row['stock_actual'] : 0,
                'stock_minimo' => isset($row['stock_minimo']) ? (float)$row['stock_minimo'] : 0,
                'stock_maximo' => isset($row['stock_maximo']) ? (float)$row['stock_maximo'] : null,
                'precio_unitario' => isset($row['precio_unitario']) ? (float)$row['precio_unitario'] : 0,
                'proveedor' => $row['proveedor'] ?? null,
                'fecha_vencimiento' => $fechaVencimiento,
                'lote' => $row['lote'] ?? null,
                'ubicacion_bodega' => $row['ubicacion_bodega'] ?? null,
                'observaciones' => $row['observaciones'] ?? null,
                'activo' => isset($row['activo']) ? filter_var($row['activo'], FILTER_VALIDATE_BOOLEAN) : true,
            ];

            if (empty($data['codigo']) || empty($data['nombre'])) {
                $this->errors[] = "Fila " . ($this->getReadRows() + 1) . ": El código y nombre son requeridos.";
                return null;
            }

            // Verificar que el código no exista
            if (InventarioBodega::where('codigo', $data['codigo'])->exists()) {
                $this->errors[] = "Fila " . ($this->getReadRows() + 1) . ": El código '{$data['codigo']}' ya existe.";
                return null;
            }

            return $this->service->create($data);
        } catch (\Exception $e) {
            $this->errors[] = "Fila " . ($this->getReadRows() + 1) . ": Error - " . $e->getMessage();
            Log::error("Error en InventarioBodegaImport::model: " . $e->getMessage(), ['row' => $row]);
            return null;
        }
    }

    protected function parseFecha($fecha): ?string
    {
        if (empty($fecha)) {
            return null;
        }
        if (is_numeric($fecha)) { // Excel date
            return Carbon::createFromFormat('Y-m-d', gmdate('Y-m-d', ($fecha - 25569) * 86400))->format('Y-m-d');
        }
        try {
            return Carbon::parse($fecha)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'max:50', 'unique:inventario_bodega,codigo'],
            'nombre' => ['required', 'string', 'max:200'],
            'tipo_producto' => ['nullable', Rule::in(['Medicamento', 'Insumo', 'Alimento', 'Equipo', 'Otro'])],
            'medicamento' => ['nullable', 'string', 'max:100'],
            'unidad_medida' => ['nullable', 'string', 'max:20'],
            'stock_actual' => ['nullable', 'numeric', 'min:0'],
            'stock_minimo' => ['nullable', 'numeric', 'min:0'],
            'stock_maximo' => ['nullable', 'numeric', 'min:0'],
            'precio_unitario' => ['nullable', 'numeric', 'min:0'],
            'proveedor' => ['nullable', 'string', 'max:200'],
            'fecha_vencimiento' => ['nullable', 'date'],
            'lote' => ['nullable', 'string', 'max:50'],
            'ubicacion_bodega' => ['nullable', 'string', 'max:500'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
            'activo' => ['nullable', 'boolean'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'codigo.unique' => 'Ya existe un producto con este código.',
            'tipo_producto.in' => 'El tipo de producto no es válido.',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

