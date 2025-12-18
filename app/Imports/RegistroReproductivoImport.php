<?php

namespace App\Imports;

use App\Models\RegistroReproductivo;
use App\Models\Vaca;
use App\Services\RegistroReproductivoService;
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

class RegistroReproductivoImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnFailure
{
    use Importable, SkipsFailures;

    protected RegistroReproductivoService $service;
    protected array $errors = [];

    public function __construct(RegistroReproductivoService $service)
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
            // Formato esperado: codigo_vaca, tipo_evento, fecha_evento, resultado_palpacion, tiempo_gestacion_dias, especialista
            
            // Buscar vaca por código
            $vaca = Vaca::where('codigo', $row['codigo_vaca'] ?? $row['codigo'] ?? $row['chapeta'] ?? null)->first();
            
            if (!$vaca) {
                $this->errors[] = "Vaca no encontrada: " . ($row['codigo_vaca'] ?? $row['codigo'] ?? $row['chapeta'] ?? 'N/A');
                return null;
            }

            // Determinar tipo de evento
            $tipoEvento = $row['tipo_evento'] ?? 'Palpación';
            $tiposValidos = ['Palpación', 'Parto', 'Celo', 'Inseminación', 'Monta'];
            if (!in_array($tipoEvento, $tiposValidos)) {
                $tipoEvento = 'Palpación'; // Valor por defecto
            }

            // Parsear fecha del evento
            $fechaEvento = $this->parseFecha($row['fecha_evento'] ?? $row['fecha'] ?? null);
            if (!$fechaEvento) {
                // Intentar con día, mes, año separados
                if (isset($row['dia']) && isset($row['mes']) && isset($row['anio'])) {
                    try {
                        $fechaEvento = Carbon::create($row['anio'], $row['mes'], $row['dia']);
                    } catch (\Exception $e) {
                        $this->errors[] = "Fecha inválida en fila: " . json_encode($row);
                        return null;
                    }
                } else {
                    $this->errors[] = "Fecha inválida en fila: " . json_encode($row);
                    return null;
                }
            }

            // Si es palpación, validar resultado
            $resultadoPalpacion = null;
            $tiempoGestacionDias = null;
            
            if ($tipoEvento === 'Palpación') {
                // Determinar resultado de palpación
                if (isset($row['preñes']) && $row['preñes']) {
                    $resultadoPalpacion = 'Preñada';
                    $tiempoGestacionDias = isset($row['tiempo_gestacion']) ? (int) $row['tiempo_gestacion'] : null;
                } elseif (isset($row['vacia']) && $row['vacia']) {
                    $resultadoPalpacion = 'Vacia';
                } elseif (isset($row['resultado_palpacion'])) {
                    $resultadoPalpacion = $row['resultado_palpacion'];
                    $tiempoGestacionDias = isset($row['tiempo_gestacion_dias']) ? (int) $row['tiempo_gestacion_dias'] : null;
                }
            }

            // Preparar datos
            $data = [
                'id_vaca' => $vaca->id_vaca,
                'tipo_evento' => $tipoEvento,
                'fecha_evento' => $fechaEvento,
                'resultado_palpacion' => $resultadoPalpacion,
                'tiempo_gestacion_dias' => $tiempoGestacionDias,
                'especialista' => $row['especialista'] ?? null,
                'observaciones' => $row['observaciones'] ?? $row['observacion'] ?? null,
            ];

            // Usar el servicio para crear el registro (con validaciones)
            return $this->service->create($data);

        } catch (\Exception $e) {
            Log::error('Error al importar registro reproductivo', [
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
            'codigo_vaca' => 'required',
            'fecha_evento' => 'required',
            'tipo_evento' => 'required',
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

