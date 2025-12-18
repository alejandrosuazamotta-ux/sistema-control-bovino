<?php

namespace App\Imports;

use App\Models\ProduccionLechera;
use App\Models\Vaca;
use App\Models\Personal;
use App\Services\ProduccionLecheraService;
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

class ProduccionLecheraImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnFailure
{
    use Importable, SkipsFailures;

    protected ProduccionLecheraService $service;
    protected array $errors = [];

    public function __construct(ProduccionLecheraService $service)
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
            // Formato esperado: codigo_vaca, fecha, turno, cantidad_leche, destino, valor_unidad, observaciones
            
            // Buscar vaca por código
            $vaca = Vaca::where('codigo', $row['codigo_vaca'] ?? $row['codigo'] ?? null)->first();
            
            if (!$vaca) {
                $this->errors[] = "Vaca no encontrada: " . ($row['codigo_vaca'] ?? $row['codigo'] ?? 'N/A');
                return null;
            }

            // Buscar personal por nombre o usar el primero disponible
            $personal = null;
            if (isset($row['encargado']) || isset($row['personal'])) {
                $nombrePersonal = $row['encargado'] ?? $row['personal'] ?? null;
                if ($nombrePersonal) {
                    $personal = Personal::where('nombre', 'like', "%{$nombrePersonal}%")->first();
                }
            }

            // Parsear fecha
            $fecha = $this->parseFecha($row['fecha'] ?? null);
            if (!$fecha) {
                $this->errors[] = "Fecha inválida en fila: " . json_encode($row);
                return null;
            }

            // Validar turno
            $turno = strtoupper($row['turno'] ?? 'AM');
            if (!in_array($turno, ['AM', 'PM'])) {
                $turno = 'AM'; // Valor por defecto
            }

            // Validar destino
            $destino = $row['destino'] ?? 'Agroindustria';
            $destinosValidos = ['Agroindustria', 'Lechero', 'Particular', 'Consumo'];
            if (!in_array($destino, $destinosValidos)) {
                $destino = 'Agroindustria'; // Valor por defecto
            }

            // Preparar datos
            $data = [
                'id_vaca' => $vaca->id_vaca,
                'fecha' => $fecha,
                'turno' => $turno,
                'cantidad_leche' => (float) ($row['cantidad_leche'] ?? $row['litros'] ?? $row['cantidad'] ?? 0),
                'destino' => $destino,
                'valor_unidad' => isset($row['valor_unidad']) ? (float) $row['valor_unidad'] : null,
                'id_personal' => $personal ? $personal->id_personal : null,
                'observaciones' => $row['observaciones'] ?? $row['observacion'] ?? null,
            ];

            // Usar el servicio para crear el registro (con validaciones)
            return $this->service->create($data);

        } catch (\Exception $e) {
            Log::error('Error al importar producción lechera', [
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
                    // Intentar usar PhpSpreadsheet si está disponible
                    if (class_exists('\PhpOffice\PhpSpreadsheet\Shared\Date')) {
                        return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fecha));
                    } else {
                        // Fallback: calcular manualmente (Excel epoch: 1900-01-01)
                        $excelEpoch = Carbon::create(1899, 12, 30);
                        return $excelEpoch->addDays((int)$fecha);
                    }
                } catch (\Exception $e) {
                    // Si falla, intentar parsear como fecha normal
                }
            }

            // Intentar parsear como fecha string
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
            'codigo_vaca' => 'required',
            'fecha' => 'required',
            'cantidad_leche' => 'required|numeric|min:0',
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

