<?php

namespace App\Imports;

use App\Models\Alimentacion;
use App\Models\Vaca;
use App\Models\Personal;
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

class AlimentacionImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnFailure
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

            // Buscar personal si se proporciona
            $personal = null;
            if (isset($row['personal']) && !empty($row['personal'])) {
                $personal = Personal::where('nombre', 'like', '%' . $row['personal'] . '%')->first();
            }

            // Parsear fecha
            $fecha = $this->parseFecha($row['fecha'] ?? null);
            if (!$fecha) {
                $this->errors[] = "Fecha inválida para la vaca '{$vaca->codigo}'.";
                return null;
            }

            $data = [
                'id_vaca' => $vaca->id_vaca,
                'fecha' => $fecha,
                'tipo_alimento' => $row['tipo_alimento'] ?? 'Concentrado',
                'cantidad' => isset($row['cantidad']) ? (float)$row['cantidad'] : 0,
                'observaciones' => $row['observaciones'] ?? null,
                'id_personal' => $personal->id_personal ?? null,
            ];

            return new Alimentacion($data);

        } catch (\Exception $e) {
            $this->errors[] = "Error al procesar fila: " . $e->getMessage();
            Log::error("Error en AlimentacionImport::model: " . $e->getMessage(), ['row' => $row]);
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
            'fecha' => ['required', 'date'],
            'tipo_alimento' => ['nullable', 'string', 'max:100'],
            'cantidad' => ['nullable', 'numeric', 'min:0'],
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

