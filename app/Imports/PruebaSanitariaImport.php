<?php

namespace App\Imports;

use App\Models\PruebaSanitaria;
use App\Models\Vaca;
use App\Services\PruebaSanitariaService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Log;

class PruebaSanitariaImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    protected PruebaSanitariaService $service;
    protected array $errors = [];

    public function __construct(PruebaSanitariaService $service)
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
            $vaca = Vaca::where('codigo', $row['codigo_vaca'])->first();
            
            if (!$vaca) {
                $this->errors[] = "Fila " . ($row['__row'] ?? 'N/A') . ": Vaca con código '{$row['codigo_vaca']}' no encontrada.";
                return null;
            }

            // Preparar datos
            $data = [
                'id_vaca' => $vaca->id_vaca,
                'tipo_prueba' => $row['tipo_prueba'] ?? null,
                'fecha_prueba' => $row['fecha_prueba'] ?? null,
                'resultado' => $row['resultado'] ?? 'Pendiente',
                'fecha_resultado' => $row['fecha_resultado'] ?? null,
                'severidad' => $row['severidad'] ?? null,
                'acta' => $row['acta'] ?? null,
                'responsable_prueba' => $row['responsable_prueba'] ?? null,
                'observaciones' => $row['observaciones'] ?? null,
            ];

            // Validar campos requeridos
            if (empty($data['tipo_prueba']) || !in_array($data['tipo_prueba'], ['Mastitis', 'Brucelosis', 'Tuberculosis'])) {
                $this->errors[] = "Fila " . ($row['__row'] ?? 'N/A') . ": Tipo de prueba inválido o faltante.";
                return null;
            }

            if (empty($data['fecha_prueba'])) {
                $this->errors[] = "Fila " . ($row['__row'] ?? 'N/A') . ": Fecha de prueba es obligatoria.";
                return null;
            }

            // Si es Pasante, asignar user_id
            if (auth()->check() && auth()->user()->hasRole('pasante')) {
                $data['user_id'] = auth()->id();
            }

            // Crear prueba sanitaria
            return $this->service->create($data);
        } catch (\Exception $e) {
            $this->errors[] = "Fila " . ($row['__row'] ?? 'N/A') . ": " . $e->getMessage();
            Log::error('Error al importar prueba sanitaria', [
                'row' => $row,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'codigo_vaca' => 'required',
            'tipo_prueba' => 'required|in:Mastitis,Brucelosis,Tuberculosis',
            'fecha_prueba' => 'required|date',
            'resultado' => 'nullable|in:Positivo,Negativo,Pendiente',
            'fecha_resultado' => 'nullable|date',
            'severidad' => 'nullable|in:Leve,Moderada,Severa',
        ];
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Obtener errores de importación
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}

