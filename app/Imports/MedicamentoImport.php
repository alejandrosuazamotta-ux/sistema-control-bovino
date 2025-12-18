<?php

namespace App\Imports;

use App\Models\Medicamento;
use App\Services\MedicamentoService;
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

class MedicamentoImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnFailure
{
    use Importable, SkipsFailures;

    protected MedicamentoService $service;
    protected array $errors = [];

    public function __construct(MedicamentoService $service)
    {
        $this->service = $service;
    }

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
                'tipo' => $row['tipo'] ?? 'Antibiótico',
                'principio_activo' => $row['principio_activo'] ?? null,
                'via_administracion' => $row['via_administracion'] ?? 'Intramuscular',
                'dosis' => $row['dosis'] ?? null,
                'periodo_retiro_dias' => isset($row['periodo_retiro_dias']) ? (int)$row['periodo_retiro_dias'] : 0,
                'activo' => isset($row['activo']) ? (bool)$row['activo'] : true,
                'observaciones' => $row['observaciones'] ?? null,
            ];

            if (empty($data['nombre'])) {
                $this->errors[] = "Fila con nombre vacío: El nombre del medicamento es requerido.";
                return null;
            }

            return $this->service->create($data);

        } catch (\Exception $e) {
            $this->errors[] = "Error al procesar fila: " . $e->getMessage();
            Log::error("Error en MedicamentoImport::model: " . $e->getMessage(), ['row' => $row]);
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['nullable', 'string', 'max:100'],
            'principio_activo' => ['nullable', 'string', 'max:255'],
            'via_administracion' => ['nullable', 'string', 'max:100'],
            'dosis' => ['nullable', 'string', 'max:100'],
            'periodo_retiro_dias' => ['nullable', 'integer', 'min:0'],
            'activo' => ['nullable', 'boolean'],
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

