<?php

namespace App\Imports;

use App\Models\Mortalidad;
use App\Models\Vaca;
use App\Models\Cria;
use App\Services\MortalidadService;
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

class MortalidadImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnFailure
{
    use Importable, SkipsFailures;

    protected MortalidadService $service;
    protected array $errors = [];

    public function __construct(MortalidadService $service)
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
            // Formato esperado: identificacion, tipo_animal, fecha, hora, clasificacion, peso, causa, acta
            
            $identificacion = $row['identificacion'] ?? $row['codigo'] ?? null;
            $tipoAnimal = strtolower($row['tipo_animal'] ?? $row['tipo'] ?? 'vaca');
            
            if (!$identificacion) {
                $this->errors[] = "Identificación no proporcionada en fila: " . json_encode($row);
                return null;
            }

            // Buscar animal (Vaca o Cria)
            $animal = null;
            $animalType = null;
            $animalId = null;

            if ($tipoAnimal === 'cria' || $tipoAnimal === 'ternero') {
                // Buscar como cría por nombre o código
                $cria = Cria::where('nombre_cria', $identificacion)
                    ->orWhere('sinigan', $identificacion)
                    ->first();
                
                if ($cria) {
                    $animal = $cria;
                    $animalType = Cria::class;
                    $animalId = $cria->id_cria;
                }
            } else {
                // Buscar como vaca
                $vaca = Vaca::where('codigo', $identificacion)->first();
                if ($vaca) {
                    $animal = $vaca;
                    $animalType = Vaca::class;
                    $animalId = $vaca->id_vaca;
                }
            }
            
            // Si no se encontró y no se especificó tipo, intentar buscar en ambos
            if (!$animal) {
                // Intentar primero como vaca
                $vaca = Vaca::where('codigo', $identificacion)->first();
                if ($vaca) {
                    $animal = $vaca;
                    $animalType = Vaca::class;
                    $animalId = $vaca->id_vaca;
                } else {
                    // Intentar como cría
                    $cria = Cria::where('nombre_cria', $identificacion)
                        ->orWhere('sinigan', $identificacion)
                        ->first();
                    if ($cria) {
                        $animal = $cria;
                        $animalType = Cria::class;
                        $animalId = $cria->id_cria;
                    }
                }
            }

            if (!$animal) {
                $this->errors[] = "Animal no encontrado: " . $identificacion;
                return null;
            }

            // Parsear fecha
            $fecha = $this->parseFecha($row['fecha'] ?? null);
            if (!$fecha) {
                $this->errors[] = "Fecha inválida en fila: " . json_encode($row);
                return null;
            }

            // Parsear hora si existe
            $hora = null;
            if (isset($row['hora']) && $row['hora']) {
                try {
                    $hora = Carbon::parse($row['hora'])->format('H:i:s');
                } catch (\Exception $e) {
                    // Ignorar error de hora
                }
            }

            // Validar clasificación
            $clasificacion = $row['clasificacion'] ?? 'Vaca';
            $clasificacionesValidas = ['Ternero', 'Novilla', 'Vaca', 'Toro', 'Becerro'];
            if (!in_array($clasificacion, $clasificacionesValidas)) {
                $clasificacion = 'Vaca'; // Valor por defecto
            }

            // Preparar datos
            $data = [
                'fecha' => $fecha,
                'hora' => $hora,
                'animal_type' => $animalType,
                'animal_id' => $animalId,
                'clasificacion' => $clasificacion,
                'peso' => isset($row['peso']) ? (float) $row['peso'] : null,
                'causa' => $row['causa'] ?? 'No especificada',
                'acta' => $row['acta'] ?? null,
                'observaciones' => $row['observaciones'] ?? $row['observacion'] ?? null,
            ];

            // Usar el servicio para crear el registro (con validaciones)
            return $this->service->create($data);

        } catch (\Exception $e) {
            Log::error('Error al importar mortalidad', [
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
            'identificacion' => 'required',
            'fecha' => 'required',
            'causa' => 'required',
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

