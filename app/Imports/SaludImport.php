<?php

namespace App\Imports;

use App\Models\Salud;
use App\Models\Vaca;
use App\Models\Personal;
use App\Services\SaludService;
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

class SaludImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, SkipsOnFailure
{
    use Importable, SkipsFailures;

    protected SaludService $service;
    protected array $errors = [];

    public function __construct(SaludService $service)
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
            // Formato esperado: codigo_vaca, tipo_registro, fecha, tipo_prueba, resultado, etc.
            
            // Buscar vaca por código
            $vaca = Vaca::where('codigo', $row['codigo_vaca'] ?? $row['codigo'] ?? null)->first();
            
            if (!$vaca) {
                $this->errors[] = "Vaca no encontrada: " . ($row['codigo_vaca'] ?? $row['codigo'] ?? 'N/A');
                return null;
            }

            // Buscar personal por nombre o usar el primero disponible
            $personal = null;
            if (isset($row['personal']) || isset($row['encargado'])) {
                $nombrePersonal = $row['personal'] ?? $row['encargado'] ?? null;
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

            // Tipo de registro
            $tipoRegistro = $row['tipo_registro'] ?? 'Otro';
            $tiposValidos = ['Vacunación', 'Tratamiento', 'Prueba mastitis', 'Prueba Brucelosis', 'Prueba Tuberculosis', 'Otro'];
            if (!in_array($tipoRegistro, $tiposValidos)) {
                $tipoRegistro = 'Otro';
            }

            // Si es prueba sanitaria, procesar campos adicionales
            $esPruebaSanitaria = in_array($tipoRegistro, ['Prueba mastitis', 'Prueba Brucelosis', 'Prueba Tuberculosis']);
            
            $data = [
                'id_vaca' => $vaca->id_vaca,
                'tipo_registro' => $tipoRegistro,
                'fecha' => $fecha,
                'descripcion' => $row['descripcion'] ?? $row['observaciones'] ?? null,
                'id_personal' => $personal?->id_personal,
            ];

            if ($esPruebaSanitaria) {
                // Determinar tipo de prueba
                $tipoPrueba = $row['tipo_prueba'] ?? null;
                if (!$tipoPrueba) {
                    // Inferir del tipo_registro
                    $tipoPrueba = match($tipoRegistro) {
                        'Prueba mastitis' => 'Mastitis',
                        'Prueba Brucelosis' => 'Brucelosis',
                        'Prueba Tuberculosis' => 'Tuberculosis',
                        default => 'Otra'
                    };
                }

                $data['tipo_prueba'] = $tipoPrueba;
                $data['resultado'] = $row['resultado'] ?? 'Pendiente';
                $data['fecha_resultado'] = isset($row['fecha_resultado']) ? $this->parseFecha($row['fecha_resultado']) : null;
                $data['responsable_prueba'] = $row['responsable_prueba'] ?? $row['responsable'] ?? null;
                $data['observaciones'] = $row['observaciones'] ?? $row['descripcion'] ?? null;

                // Campos específicos por tipo
                if ($tipoPrueba === 'Mastitis') {
                    $data['severidad'] = $row['severidad'] ?? null;
                    $data['tratamiento_sugerido'] = $row['tratamiento_sugerido'] ?? $row['tratamiento'] ?? null;
                }

                if (in_array($tipoPrueba, ['Brucelosis', 'Tuberculosis'])) {
                    $data['acta'] = $row['acta'] ?? $row['numero_acta'] ?? null;
                }

                // Restricciones se aplicarán automáticamente en el Service
            }

            // Crear registro usando el service para aplicar lógica de negocio
            return $this->service->create($data);
        } catch (\Exception $e) {
            Log::error('Error al importar registro de salud: ' . $e->getMessage(), ['row' => $row]);
            $this->errors[] = "Error en fila: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Parsear fecha desde Excel
     */
    protected function parseFecha($fecha): ?string
    {
        if (!$fecha) {
            return null;
        }

        try {
            // Si es un número de Excel (días desde 1900)
            if (is_numeric($fecha)) {
                $fechaCarbon = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fecha));
                return $fechaCarbon->format('Y-m-d');
            }

            // Si es string, intentar parsear
            $fechaCarbon = Carbon::parse($fecha);
            return $fechaCarbon->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning('Error al parsear fecha: ' . $fecha, ['error' => $e->getMessage()]);
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
            'tipo_registro' => 'required',
            'fecha' => 'required',
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
     * Obtener errores
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}

