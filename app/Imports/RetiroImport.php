<?php

namespace App\Imports;

use App\Models\Retiro;
use App\Models\Vaca;
use App\Models\UsoMedicamento;
use App\Services\RetiroService;
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

class RetiroImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnFailure
{
    use Importable, SkipsFailures;

    protected RetiroService $service;
    protected array $errors = [];

    public function __construct(RetiroService $service)
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
            // Buscar vaca por código
            $codigoVaca = $row['codigo_vaca'] ?? $row['codigo'] ?? null;
            $vaca = Vaca::where('codigo', $codigoVaca)->first();
            if (!$vaca) {
                $this->errors[] = "Vaca con código '{$codigoVaca}' no encontrada.";
                return null;
            }

            // Buscar uso de medicamento si se proporciona (opcional)
            $usoMedicamento = null;
            if (isset($row['id_uso_medicamento']) && !empty($row['id_uso_medicamento'])) {
                $usoMedicamento = UsoMedicamento::find($row['id_uso_medicamento']);
            }

            // Parsear fechas
            $fechaInicio = $this->parseFecha($row['fecha_inicio'] ?? $row['fecha'] ?? null);
            if (!$fechaInicio) {
                $this->errors[] = "Fecha de inicio inválida para la vaca '{$vaca->codigo}'.";
                return null;
            }

            $fechaFin = $this->parseFecha($row['fecha_fin'] ?? null);
            if (!$fechaFin) {
                // Si no hay fecha fin, calcular basado en período de retiro del medicamento
                if ($usoMedicamento && $usoMedicamento->medicamento) {
                    $fechaFin = Carbon::parse($fechaInicio)->addDays($usoMedicamento->medicamento->periodo_retiro_dias)->format('Y-m-d');
                } else {
                    $this->errors[] = "Fecha de fin requerida para la vaca '{$vaca->codigo}'.";
                    return null;
                }
            }

            $data = [
                'id_vaca' => $vaca->id_vaca,
                'id_uso_medicamento' => $usoMedicamento->id_uso ?? null,
                'fecha_inicio' => $fechaInicio,
                'fecha_fin' => $fechaFin,
                'tipo_retiro' => $row['tipo_retiro'] ?? 'Ordeño',
                'activo' => isset($row['activo']) ? (bool)$row['activo'] : true,
                'observaciones' => $row['observaciones'] ?? null,
            ];

            // El servicio se encargará de validar y crear el retiro
            return $this->service->create($data);

        } catch (\Exception $e) {
            $this->errors[] = "Error al procesar fila: " . $e->getMessage();
            Log::error("Error en RetiroImport::model: " . $e->getMessage(), ['row' => $row]);
            return null;
        }
    }

    /**
     * Parsear fecha desde diferentes formatos
     */
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
            'codigo_vaca' => ['required', 'exists:vacas,codigo'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after:fecha_inicio'],
            'tipo_retiro' => ['nullable', 'in:Ordeño,Producción'],
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

