<?php

namespace App\Services;

use App\Models\Alimentacion;
use App\Repositories\AlimentacionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AlimentacionService
{
    protected AlimentacionRepository $repository;

    public function __construct(AlimentacionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Crear una nueva alimentación
     *
     * @param array $data
     * @return Alimentacion
     * @throws \Exception
     */
    public function create(array $data): Alimentacion
    {
        return DB::transaction(function () use ($data) {
            $alimentacion = $this->repository->create($data);

            // Log de actividad
            Log::info('Alimentación creada', [
                'alimentacion_id' => $alimentacion->id_alimentacion,
                'vaca_id' => $alimentacion->id_vaca,
                'user_id' => auth()->id()
            ]);

            return $alimentacion;
        });
    }

    /**
     * Actualizar una alimentación
     *
     * @param Alimentacion $alimentacion
     * @param array $data
     * @return Alimentacion
     * @throws \Exception
     */
    public function update(Alimentacion $alimentacion, array $data): Alimentacion
    {
        return DB::transaction(function () use ($alimentacion, $data) {
            $this->repository->update($alimentacion, $data);
            $alimentacion->refresh();

            // Log de actividad
            Log::info('Alimentación actualizada', [
                'alimentacion_id' => $alimentacion->id_alimentacion,
                'vaca_id' => $alimentacion->id_vaca,
                'user_id' => auth()->id()
            ]);

            return $alimentacion;
        });
    }

    /**
     * Eliminar una alimentación
     *
     * @param Alimentacion $alimentacion
     * @return bool
     * @throws \Exception
     */
    public function delete(Alimentacion $alimentacion): bool
    {
        return DB::transaction(function () use ($alimentacion) {
            $alimentacionId = $alimentacion->id_alimentacion;
            $vacaId = $alimentacion->id_vaca;

            $deleted = $this->repository->delete($alimentacion);

            if ($deleted) {
                // Log de actividad
                Log::info('Alimentación eliminada', [
                    'alimentacion_id' => $alimentacionId,
                    'vaca_id' => $vacaId,
                    'user_id' => auth()->id()
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Obtener lista paginada de alimentaciones con filtros
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
     * Obtener una alimentación con todas sus relaciones
     *
     * @param string $id
     * @return Alimentacion|null
     */
    public function findWithRelations(string $id): ?Alimentacion
    {
        return $this->repository->findWithRelations($id);
    }

    /**
     * Obtener alimentación por ID
     *
     * @param string $id
     * @return Alimentacion|null
     */
    public function findById(string $id): ?Alimentacion
    {
        return $this->repository->findById($id);
    }

    /**
     * Obtener estadísticas de alimentación
     *
     * @param array $filters
     * @return array
     */
    public function getEstadisticas(array $filters = []): array
    {
        return $this->repository->getEstadisticas($filters);
    }

    /**
     * Obtener datos para gráficas
     *
     * @return array
     */
    public function getDatosGraficas(): array
    {
        return [
            'por_tipo_alimento' => $this->repository->getDatosGraficaPorTipoAlimento(),
            'consumo_por_mes' => $this->repository->getDatosGraficaConsumoPorMes(12),
            'top_vacas' => $this->repository->getDatosGraficaTopVacas(10),
        ];
    }

    /**
     * Obtener registros filtrados para exportación PDF
     * 
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRegistrosParaExportacion(array $filters = [])
    {
        $query = Alimentacion::with(['vaca', 'personal']);
        
        if (isset($filters['id_vaca']) && !empty($filters['id_vaca'])) {
            $query->where('id_vaca', $filters['id_vaca']);
        }
        
        if (isset($filters['tipo_alimento']) && !empty($filters['tipo_alimento'])) {
            $query->where('tipo_alimento', $filters['tipo_alimento']);
        }
        
        if (isset($filters['fecha_inicio']) && !empty($filters['fecha_inicio'])) {
            $query->where('fecha', '>=', $filters['fecha_inicio']);
        }
        
        if (isset($filters['fecha_fin']) && !empty($filters['fecha_fin'])) {
            $query->where('fecha', '<=', $filters['fecha_fin']);
        }
        
        return $query->orderBy('fecha', 'desc')->get();
    }
}

