<?php

namespace App\Imports;

use App\Models\Potrero;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;

class PotreroImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnFailure
{
    use Importable, SkipsFailures;

    protected array $errors = [];

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            $data = [
                'nombre' => $row['nombre'] ?? null,
                'ubicacion' => $row['ubicacion'] ?? null,
                'capacidad' => isset($row['capacidad']) ? (int)$row['capacidad'] : 10,
                'descripcion' => $row['descripcion'] ?? null,
            ];

            if (empty($data['nombre'])) {
                $this->errors[] = "Fila con nombre vacío: El nombre del potrero es requerido.";
                return null;
            }

            return new Potrero($data);

        } catch (\Exception $e) {
            $this->errors[] = "Error al procesar fila: " . $e->getMessage();
            Log::error("Error en PotreroImport::model: " . $e->getMessage(), ['row' => $row]);
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'capacidad' => ['nullable', 'integer', 'min:1'],
            'descripcion' => ['nullable', 'string'],
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

