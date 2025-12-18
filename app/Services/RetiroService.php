<?php

namespace App\Services;

use App\Models\Retiro;
use App\Models\ProduccionLechera;
use App\Repositories\RetiroRepository;
use App\Constants\ExceptionMessages;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RetiroService
{
    protected RetiroRepository $repository;

    public function __construct(RetiroRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Crear un nuevo retiro
     *
     * @param array $data
     * @return Retiro
     * @throws \Exception
     */
    public function create(array $data): Retiro
    {
        return DB::transaction(function () use ($data) {
            // Validación defensiva: fechas inconsistentes
            if (isset($data['fecha_inicio']) && isset($data['fecha_fin'])) {
                $fechaInicio = Carbon::parse($data['fecha_inicio']);
                $fechaFin = Carbon::parse($data['fecha_fin']);
                
                if ($fechaInicio->gt($fechaFin)) {
                    throw new \Exception(ExceptionMessages::RETIRO_FECHAS_INCONSISTENTES);
                }
            }

            // Validar que no haya retiro activo solapado
            $this->validarNoRetiroSolapado($data['id_vaca'], $data['fecha_inicio'], $data['fecha_fin'], $data['tipo_retiro']);

            $retiro = $this->repository->create($data);
            $retiro->load(['vaca', 'usoMedicamento.medicamento']);

            // HARDENING: Marcar automáticamente producciones ya existentes dentro del rango del retiro
            $this->marcarProduccionesExcluidasPorRetiro($retiro);

            // Log de actividad (sin datos sensibles innecesarios)
            Log::info('Retiro creado', [
                'retiro_id' => $retiro->id_retiro,
                'vaca_id' => $retiro->id_vaca,
                'tipo_retiro' => $retiro->tipo_retiro,
                'fecha_inicio' => $retiro->fecha_inicio->format('Y-m-d'),
                'fecha_fin' => $retiro->fecha_fin->format('Y-m-d'),
                'user_id' => auth()->id() // Necesario para auditoría
            ]);

            return $retiro;
        });
    }

    /**
     * Actualizar un retiro
     *
     * @param Retiro $retiro
     * @param array $data
     * @return Retiro
     * @throws \Exception
     */
    public function update(Retiro $retiro, array $data): Retiro
    {
        return DB::transaction(function () use ($retiro, $data) {
            // Validación defensiva: fechas inconsistentes
            if (isset($data['fecha_inicio']) && isset($data['fecha_fin'])) {
                $fechaInicio = Carbon::parse($data['fecha_inicio']);
                $fechaFin = Carbon::parse($data['fecha_fin']);
                
                if ($fechaInicio->gt($fechaFin)) {
                    throw new \Exception(ExceptionMessages::RETIRO_FECHAS_INCONSISTENTES);
                }
            }

            // Si cambian las fechas o tipo, validar solapamiento
            if (isset($data['fecha_inicio']) || isset($data['fecha_fin']) || isset($data['tipo_retiro'])) {
                $fechaInicio = $data['fecha_inicio'] ?? $retiro->fecha_inicio->format('Y-m-d');
                $fechaFin = $data['fecha_fin'] ?? $retiro->fecha_fin->format('Y-m-d');
                $tipoRetiro = $data['tipo_retiro'] ?? $retiro->tipo_retiro;
                
                $this->validarNoRetiroSolapado(
                    $retiro->id_vaca,
                    $fechaInicio,
                    $fechaFin,
                    $tipoRetiro,
                    $retiro->id_retiro
                );
            }

            $this->repository->update($retiro, $data);
            $retiro->refresh();
            $retiro->load(['vaca', 'usoMedicamento.medicamento']);

            // HARDENING: Si cambian las fechas, actualizar el marcado de producciones
            if (isset($data['fecha_inicio']) || isset($data['fecha_fin'])) {
                $this->marcarProduccionesExcluidasPorRetiro($retiro);
            }

            // Log de actividad (sin datos sensibles innecesarios)
            Log::info('Retiro actualizado', [
                'retiro_id' => $retiro->id_retiro,
                'vaca_id' => $retiro->id_vaca,
                'user_id' => auth()->id() // Necesario para auditoría
            ]);

            return $retiro;
        });
    }

    /**
     * Eliminar un retiro
     *
     * @param Retiro $retiro
     * @return bool
     * @throws \Exception
     */
    public function delete(Retiro $retiro): bool
    {
        return DB::transaction(function () use ($retiro) {
            $retiroId = $retiro->id_retiro;
            $vacaId = $retiro->id_vaca;

            $deleted = $this->repository->delete($retiro);

            if ($deleted) {
                // Log de actividad (sin datos sensibles innecesarios)
                Log::info('Retiro eliminado', [
                    'retiro_id' => $retiroId,
                    'vaca_id' => $vacaId,
                    'user_id' => auth()->id() // Necesario para auditoría
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Validar que no haya retiros solapados
     *
     * @param int $vacaId
     * @param string $fechaInicio
     * @param string $fechaFin
     * @param string $tipoRetiro
     * @param int|null $excluirRetiroId
     * @throws \Exception
     */
    protected function validarNoRetiroSolapado(
        int $vacaId,
        string $fechaInicio,
        string $fechaFin,
        string $tipoRetiro,
        ?int $excluirRetiroId = null
    ): void {
        $query = Retiro::where('id_vaca', $vacaId)
            ->where('tipo_retiro', $tipoRetiro)
            ->where('activo', true)
            ->where(function($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fecha_inicio', [$fechaInicio, $fechaFin])
                  ->orWhereBetween('fecha_fin', [$fechaInicio, $fechaFin])
                  ->orWhere(function($q2) use ($fechaInicio, $fechaFin) {
                      $q2->where('fecha_inicio', '<=', $fechaInicio)
                         ->where('fecha_fin', '>=', $fechaFin);
                  });
            });

        if ($excluirRetiroId) {
            $query->where('id_retiro', '!=', $excluirRetiroId);
        }

        if ($query->exists()) {
            throw new \Exception(sprintf(ExceptionMessages::RETIRO_SOLAPADO, $tipoRetiro));
        }
    }

    /**
     * Obtener lista paginada de retiros con filtros
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(array $filters = [], int $perPage = 15)
    {
        return $this->repository->paginateWithFilters($filters, $perPage);
    }

    /**
     * Obtener un retiro con todas sus relaciones
     *
     * @param string $id
     * @return Retiro|null
     */
    public function findWithRelations(string $id): ?Retiro
    {
        return $this->repository->findWithRelations($id);
    }

    /**
     * Obtener retiro por ID
     *
     * @param string $id
     * @return Retiro|null
     */
    public function findById(string $id): ?Retiro
    {
        return $this->repository->findById($id);
    }

    /**
     * Verificar si una vaca tiene retiro activo de ordeño
     *
     * @param int $vacaId
     * @param Carbon|null $fecha
     * @return bool
     */
    public function tieneRetiroOrdeñoActivo(int $vacaId, ?Carbon $fecha = null): bool
    {
        return $this->repository->tieneRetiroOrdeñoActivo($vacaId, $fecha);
    }

    /**
     * Verificar si una vaca tiene retiro activo de producción
     *
     * @param int $vacaId
     * @param Carbon|null $fecha
     * @return bool
     */
    public function tieneRetiroProduccionActivo(int $vacaId, ?Carbon $fecha = null): bool
    {
        return $this->repository->tieneRetiroProduccionActivo($vacaId, $fecha);
    }

    /**
     * Obtener retiros activos actualmente
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRetirosActivos()
    {
        return $this->repository->getRetirosActivos();
    }

    /**
     * Obtener retiros próximos a vencer
     *
     * @param int $dias
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRetirosProximosAVencer(int $dias = 7)
    {
        return $this->repository->getRetirosProximosAVencer($dias);
    }

    /**
     * Obtener retiros filtrados (sin paginación)
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFiltered(array $filters = [])
    {
        return $this->repository->getFiltered($filters);
    }

    /**
     * Obtener datos para gráficas
     *
     * @return array
     */
    public function getDatosGraficas(): array
    {
        return [
            'por_tipo' => $this->repository->getDatosGraficaPorTipo(),
            'retiros_por_mes' => $this->repository->getDatosGraficaRetirosPorMes(12),
            'activos_vs_inactivos' => $this->repository->getDatosGraficaActivosVsInactivos(),
        ];
    }
}

