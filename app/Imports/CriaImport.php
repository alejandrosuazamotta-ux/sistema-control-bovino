<?php

namespace App\Imports;

use App\Models\Cria;
use App\Models\Vaca;
use App\Services\CriaService;
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

class CriaImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnFailure
{
    use Importable, SkipsFailures;

    protected CriaService $service;
    protected array $errors = [];

    public function __construct(CriaService $service)
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
            // Mapear columnas del Excel a campos de la base de datos
            // Formato esperado: codigo_madre, nombre_cria, sexo, fecha_nacimiento, peso, fecha_tatuado, concepcion, sinigan
            
            // Buscar vaca madre por código
            $vacaMadre = Vaca::where('codigo', $row['codigo_madre'] ?? $row['madre'] ?? $row['codigo_vaca'] ?? null)->first();
            
            if (!$vacaMadre) {
                $this->errors[] = "Vaca madre no encontrada: " . ($row['codigo_madre'] ?? $row['madre'] ?? 'N/A');
                return null;
            }

            // Parsear fecha de nacimiento
            $fechaNacimiento = $this->parseFecha($row['fecha_nacimiento'] ?? $row['fecha'] ?? null);
            if (!$fechaNacimiento) {
                $this->errors[] = "Fecha de nacimiento inválida en fila: " . json_encode($row);
                return null;
            }

            // Validar sexo
            $sexo = ucfirst(strtolower($row['sexo'] ?? 'Macho'));
            if (!in_array($sexo, ['Macho', 'Hembra'])) {
                $sexo = 'Macho'; // Valor por defecto
            }

            // Validar concepción
            $concepcion = $row['concepcion'] ?? 'IA';
            $concepcionesValidas = ['IA', 'Monta Natural', 'Transferencia Embrionaria'];
            if (!in_array($concepcion, $concepcionesValidas)) {
                $concepcion = 'IA'; // Valor por defecto
            }

            // Parsear fecha de tatuado si existe
            $fechaTatuado = null;
            if (isset($row['fecha_tatuado']) && $row['fecha_tatuado']) {
                $fechaTatuado = $this->parseFecha($row['fecha_tatuado']);
            }

            // Preparar datos
            $data = [
                'id_vaca_madre' => $vacaMadre->id_vaca,
                'nombre_cria' => $row['nombre_cria'] ?? $row['nombre'] ?? null,
                'sexo' => $sexo,
                'fecha_nacimiento' => $fechaNacimiento,
                'peso' => isset($row['peso']) ? (float) $row['peso'] : null,
                'fecha_tatuado' => $fechaTatuado,
                'concepcion' => $concepcion,
                'sinigan' => $row['sinigan'] ?? null,
                'observaciones' => $row['observaciones'] ?? $row['observacion'] ?? null,
            ];

            // Usar el servicio para crear el registro (con validaciones)
            return $this->service->create($data);

        } catch (\Exception $e) {
            Log::error('Error al importar cría', [
                'row' => $row,
                'error' => $e->getMessage()
            ]);
            $this->errors[] = "Error en fila: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Parsear fecha desde diferentes formatos
     */
    protected function parseFecha($fecha)
    {
        if (!$fecha) {
            return null;
        }

        try {
            // Si es un número de Excel (días desde 1900)
            if (is_numeric($fecha) && $fecha > 1) {
                try {
                    if (class_exists('\PhpOffice\PhpSpreadsheet\Shared\Date')) {
                        return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fecha));
                    } else {
                        $excelEpoch = Carbon::create(1899, 12, 30);
                        return $excelEpoch->addDays((int)$fecha);
                    }
                } catch (\Exception $e) {
                    // Continuar con parseo normal
                }
            }

            return Carbon::parse($fecha);
        } catch (\Exception $e) {
            Log::warning('Error al parsear fecha en importación', [
                'fecha' => $fecha,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Reglas de validación
     */
    public function rules(): array
    {
        return [
            'codigo_madre' => 'required',
            'fecha_nacimiento' => 'required',
            'sexo' => 'required|in:Macho,Hembra',
        ];
    }

    /**
     * Tamaño del batch para inserción
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Tamaño del chunk para lectura
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Obtener errores acumulados
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}

