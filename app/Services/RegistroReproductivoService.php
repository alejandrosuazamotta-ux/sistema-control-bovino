<?php

namespace App\Services;

use App\Models\RegistroReproductivo;
use App\Models\Vaca;
use App\Repositories\RegistroReproductivoRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RegistroReproductivoService
{
    protected RegistroReproductivoRepository $repository;

    public function __construct(RegistroReproductivoRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Crear un nuevo registro reproductivo
     *
     * @param array $data
     * @return RegistroReproductivo
     * @throws \Exception
     */
    public function create(array $data): RegistroReproductivo
    {
        return DB::transaction(function () use ($data) {
            // Validar que la vaca existe
            $vaca = Vaca::find($data['id_vaca']);
            if (!$vaca) {
                throw new \Exception('La vaca seleccionada no existe.');
            }

            // Validar estados incompatibles
            if ($data['tipo_evento'] === 'Celo' && $vaca->estado_reproductivo === 'Preñada') {
                throw new \Exception('No se puede registrar un evento de Celo para una vaca que está Preñada.');
            }

            // Validar que la fecha del evento no sea futura
            $fechaEvento = Carbon::parse($data['fecha_evento']);
            if ($fechaEvento->isFuture()) {
                throw new \Exception('La fecha del evento no puede ser futura.');
            }

            // Validar lógica de negocio según tipo de evento
            if ($data['tipo_evento'] === 'Palpación' && isset($data['resultado_palpacion'])) {
                if ($data['resultado_palpacion'] === 'Preñada' && !isset($data['tiempo_gestacion_dias'])) {
                    throw new \Exception('Debe especificar el tiempo de gestación en días para una palpación con resultado Preñada.');
                }
            }

            // Calcular días abiertos si aplica
            if (in_array($data['tipo_evento'], ['Inseminación', 'Palpación'])) {
                $ultimoParto = $this->repository->getUltimoParto($data['id_vaca']);
                if ($ultimoParto) {
                    $data['dias_abiertos'] = $ultimoParto->fecha_evento->diffInDays($fechaEvento);
                }
            }

            // Si es palpación preñada, calcular fecha probable parto
            if ($data['tipo_evento'] === 'Palpación' 
                && isset($data['resultado_palpacion']) 
                && $data['resultado_palpacion'] === 'Preñada'
                && isset($data['tiempo_gestacion_dias'])) {
                
                $fechaEvento = Carbon::parse($data['fecha_evento']);
                $diasRestantes = 283 - $data['tiempo_gestacion_dias'];
                $data['fecha_probable_parto'] = $fechaEvento->copy()->addDays($diasRestantes)->format('Y-m-d');
            }

            $registro = $this->repository->create($data);
            $registro->load(['vaca', 'personal']);

            // Actualizar estado reproductivo de la vaca si es necesario
            $this->actualizarEstadoVaca($registro);

            // Log de actividad
            Log::info('Registro reproductivo creado', [
                'registro_id' => $registro->id_registro,
                'vaca_id' => $registro->id_vaca,
                'tipo_evento' => $registro->tipo_evento,
                'fecha_evento' => $registro->fecha_evento->format('Y-m-d'),
                'user_id' => auth()->id()
            ]);

            return $registro;
        });
    }

    /**
     * Actualizar un registro reproductivo
     *
     * @param RegistroReproductivo $registro
     * @param array $data
     * @return RegistroReproductivo
     * @throws \Exception
     */
    public function update(RegistroReproductivo $registro, array $data): RegistroReproductivo
    {
        return DB::transaction(function () use ($registro, $data) {
            // Recalcular días abiertos si cambió el tipo de evento o la fecha
            if (isset($data['tipo_evento']) || isset($data['fecha_evento'])) {
                $tipoEvento = $data['tipo_evento'] ?? $registro->tipo_evento;
                $fechaEvento = isset($data['fecha_evento']) 
                    ? Carbon::parse($data['fecha_evento']) 
                    : $registro->fecha_evento;

                if (in_array($tipoEvento, ['Inseminación', 'Palpación'])) {
                    $ultimoParto = $this->repository->getUltimoParto($registro->id_vaca);
                    if ($ultimoParto) {
                        $data['dias_abiertos'] = $ultimoParto->fecha_evento->diffInDays($fechaEvento);
                    }
                }
            }

            // Recalcular fecha probable parto si cambió algo relevante
            if (isset($data['tipo_evento']) || isset($data['resultado_palpacion']) 
                || isset($data['tiempo_gestacion_dias']) || isset($data['fecha_evento'])) {
                
                $tipoEvento = $data['tipo_evento'] ?? $registro->tipo_evento;
                $resultado = $data['resultado_palpacion'] ?? $registro->resultado_palpacion;
                $tiempoGestacion = $data['tiempo_gestacion_dias'] ?? $registro->tiempo_gestacion_dias;
                $fechaEvento = isset($data['fecha_evento']) 
                    ? Carbon::parse($data['fecha_evento']) 
                    : $registro->fecha_evento;

                if ($tipoEvento === 'Palpación' && $resultado === 'Preñada' && $tiempoGestacion !== null) {
                    $diasRestantes = 283 - $tiempoGestacion;
                    $data['fecha_probable_parto'] = $fechaEvento->copy()->addDays($diasRestantes)->format('Y-m-d');
                } elseif ($tipoEvento !== 'Palpación' || $resultado !== 'Preñada') {
                    $data['fecha_probable_parto'] = null;
                }
            }

            $this->repository->update($registro, $data);
            $registro->refresh();
            $registro->load(['vaca', 'personal']);

            // Actualizar estado reproductivo de la vaca
            $this->actualizarEstadoVaca($registro);

            // Log de actividad
            Log::info('Registro reproductivo actualizado', [
                'registro_id' => $registro->id_registro,
                'user_id' => auth()->id()
            ]);

            return $registro;
        });
    }

    /**
     * Eliminar un registro reproductivo
     *
     * @param RegistroReproductivo $registro
     * @return bool
     * @throws \Exception
     */
    public function delete(RegistroReproductivo $registro): bool
    {
        return DB::transaction(function () use ($registro) {
            $registroId = $registro->id_registro;
            $vacaId = $registro->id_vaca;

            $deleted = $this->repository->delete($registro);

            if ($deleted) {
                // Log de actividad
                Log::info('Registro reproductivo eliminado', [
                    'registro_id' => $registroId,
                    'vaca_id' => $vacaId,
                    'user_id' => auth()->id()
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Obtener lista paginada de registros con filtros
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(array $filters = [], int $perPage = 15)
    {
        return $this->repository->getPaginated($filters, $perPage);
    }

    /**
     * Obtener un registro con todas sus relaciones
     *
     * @param string $id
     * @return RegistroReproductivo|null
     */
    public function findWithRelations(string $id): ?RegistroReproductivo
    {
        return $this->repository->findWithRelations($id);
    }

    /**
     * Obtener registro por ID
     *
     * @param string $id
     * @return RegistroReproductivo|null
     */
    public function findById(string $id): ?RegistroReproductivo
    {
        return $this->repository->findById($id);
    }

    /**
     * Obtener vacas próximas al parto
     *
     * @param int $dias
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVacasProximasAlParto(int $dias = 21)
    {
        return $this->repository->getVacasProximasAlParto($dias);
    }

    /**
     * Obtener vacas que necesitan revisión de celo
     *
     * @param int $diasDesdeUltimoParto
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVacasNecesitanCelo(int $diasDesdeUltimoParto = 21)
    {
        return $this->repository->getVacasNecesitanCelo($diasDesdeUltimoParto);
    }

    /**
     * Obtener estadísticas reproductivas
     *
     * @return array
     */
    public function getEstadisticas(): array
    {
        return $this->repository->getEstadisticas();
    }

    /**
     * Obtener datos para gráficas
     *
     * @return array
     */
    public function getDatosGraficas(): array
    {
        return [
            'por_tipo_evento' => $this->repository->getDatosGraficaPorTipoEvento(),
            'preñadas_por_mes' => $this->repository->getDatosGraficaPreñadasPorMes(12),
            'dias_abiertos' => $this->repository->getDatosGraficaDiasAbiertos(),
        ];
    }

    /**
     * Actualizar estado reproductivo de la vaca según el registro
     *
     * @param RegistroReproductivo $registro
     * @return void
     */
    protected function actualizarEstadoVaca(RegistroReproductivo $registro): void
    {
        $vaca = Vaca::find($registro->id_vaca);
        
        if (!$vaca) {
            return;
        }

        // Si es palpación preñada, actualizar estado a "Preñada"
        if ($registro->tipo_evento === 'Palpación' && $registro->resultado_palpacion === 'Preñada') {
            $vaca->estado_reproductivo = 'Preñada';
            $vaca->save();
        }
        // Si es parto, actualizar estado a "Lactancia"
        elseif ($registro->tipo_evento === 'Parto') {
            $vaca->estado_reproductivo = 'Lactancia';
            $vaca->save();
        }
        // Si es inseminación, actualizar estado a "Preñada" (asumimos que fue exitosa)
        elseif ($registro->tipo_evento === 'Inseminación') {
            // No cambiamos el estado automáticamente, esperamos confirmación por palpación
        }
    }
}

