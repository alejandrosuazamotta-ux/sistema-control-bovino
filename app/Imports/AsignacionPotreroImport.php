<?php

namespace App\Imports;

use App\Models\AsignacionPotrero;
use App\Models\Vaca;
use App\Models\Potrero;
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

class AsignacionPotreroImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnFailure
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
            // Buscar vaca por código
            $codigoVaca = $row['codigo_vaca'] ?? $row['codigo'] ?? null;
            $vaca = Vaca::where('codigo', $codigoVaca)->first();
            if (!$vaca) {
                $this->errors[] = "Vaca con código '{$codigoVaca}' no encontrada.";
                return null;
            }

            // Buscar potrero por nombre
            $nombrePotrero = $row['potrero'] ?? $row['nombre_potrero'] ?? null;
            $potrero = Potrero::where('nombre', $nombrePotrero)->first();
            if (!$potrero) {
                $this->errors[] = "Potrero '{$nombrePotrero}' no encontrado.";
                return null;
            }

            // Parsear fecha
            $fecha = $this->parseFecha($row['fecha_asignacion'] ?? $row['fecha'] ?? null);
            if (!$fecha) {
                $fecha = now()->format('Y-m-d');
            }

            $data = [
                'id_potrero' => $potrero->id_potrero,
                'id_vaca' => $vaca->id_vaca,
                'fecha_asignacion' => $fecha,
            ];

            return new AsignacionPotrero($data);

        } catch (\Exception $e) {
            $this->errors[] = "Error al procesar fila: " . $e->getMessage();
            Log::error("Error en AsignacionPotreroImport::model: " . $e->getMessage(), ['row' => $row]);
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
            'potrero' => ['required', 'exists:potreros,nombre'],
            'fecha_asignacion' => ['nullable', 'date'],
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

