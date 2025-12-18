<?php

namespace App\Imports;

use App\Models\UsoMedicamento;
use App\Models\Vaca;
use App\Models\Medicamento;
use App\Models\Personal;
use App\Services\UsoMedicamentoService;
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

class UsoMedicamentoImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnFailure
{
    use Importable, SkipsFailures;

    protected UsoMedicamentoService $service;
    protected array $errors = [];

    public function __construct(UsoMedicamentoService $service)
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

            // Buscar medicamento por nombre
            $nombreMedicamento = $row['medicamento'] ?? $row['nombre_medicamento'] ?? null;
            $medicamento = Medicamento::where('nombre', $nombreMedicamento)->first();
            if (!$medicamento) {
                $this->errors[] = "Medicamento '{$nombreMedicamento}' no encontrado.";
                return null;
            }

            // Buscar personal si se proporciona
            $personal = null;
            if (isset($row['personal']) && !empty($row['personal'])) {
                $personal = Personal::where('nombre', 'like', '%' . $row['personal'] . '%')->first();
            }

            // Parsear fecha
            $fecha = $this->parseFecha($row['fecha_aplicacion'] ?? $row['fecha'] ?? null);
            if (!$fecha) {
                $this->errors[] = "Fecha inválida para la vaca '{$vaca->codigo}'.";
                return null;
            }

            $data = [
                'id_medicamento' => $medicamento->id_medicamento,
                'id_vaca' => $vaca->id_vaca,
                'fecha_aplicacion' => $fecha,
                'dosis_aplicada' => isset($row['dosis_aplicada']) ? (float)$row['dosis_aplicada'] : null,
                'unidad_dosis' => $row['unidad_dosis'] ?? 'ml',
                'id_personal' => $personal->id_personal ?? null,
                'observaciones' => $row['observaciones'] ?? null,
            ];

            // El servicio se encargará de crear el retiro automático si aplica
            return $this->service->create($data);

        } catch (\Exception $e) {
            $this->errors[] = "Error al procesar fila: " . $e->getMessage();
            Log::error("Error en UsoMedicamentoImport::model: " . $e->getMessage(), ['row' => $row]);
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
            'medicamento' => ['required', 'exists:medicamentos,nombre'],
            'fecha_aplicacion' => ['required', 'date'],
            'dosis_aplicada' => ['nullable', 'numeric', 'min:0'],
            'unidad_dosis' => ['nullable', 'string', 'max:50'],
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

