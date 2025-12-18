<?php

namespace App\Services;

use App\Models\Vaca;
use App\Repositories\VacaRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VacaService
{
    protected VacaRepository $repository;

    public function __construct(VacaRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Crear una nueva vaca con validación de capacidad de potrero
     *
     * @param array $data
     * @return Vaca
     * @throws \Exception
     */
    public function create(array $data): Vaca
    {
        return DB::transaction(function () use ($data) {
            // Validar capacidad del potrero si se asigna
            if (isset($data['id_potrero']) && !empty($data['id_potrero'])) {
                $capacityCheck = $this->repository->checkPotreroCapacity($data['id_potrero']);
                
                if (!$capacityCheck['available']) {
                    throw new \Exception($capacityCheck['message']);
                }
            }

            $vaca = $this->repository->create($data);

            // Log de actividad
            Log::info('Vaca creada', [
                'vaca_id' => $vaca->id_vaca,
                'codigo' => $vaca->codigo,
                'user_id' => auth()->id()
            ]);

            return $vaca;
        });
    }

    /**
     * Actualizar una vaca con validación de capacidad de potrero
     *
     * @param Vaca $vaca
     * @param array $data
     * @return Vaca
     * @throws \Exception
     */
    public function update(Vaca $vaca, array $data): Vaca
    {
        return DB::transaction(function () use ($vaca, $data) {
            // Validar capacidad del potrero si se cambia
            if (isset($data['id_potrero']) && 
                $data['id_potrero'] != $vaca->id_potrero) {
                
                $capacityCheck = $this->repository->checkPotreroCapacity($data['id_potrero']);
                
                if (!$capacityCheck['available']) {
                    throw new \Exception($capacityCheck['message']);
                }
            }

            $this->repository->update($vaca, $data);
            $vaca->refresh();

            // Log de actividad
            Log::info('Vaca actualizada', [
                'vaca_id' => $vaca->id_vaca,
                'codigo' => $vaca->codigo,
                'user_id' => auth()->id()
            ]);

            return $vaca;
        });
    }

    /**
     * Eliminar una vaca
     *
     * @param Vaca $vaca
     * @return bool
     * @throws \Exception
     */
    public function delete(Vaca $vaca): bool
    {
        return DB::transaction(function () use ($vaca) {
            $vacaId = $vaca->id_vaca;
            $codigo = $vaca->codigo;

            $deleted = $this->repository->delete($vaca);

            if ($deleted) {
                // Log de actividad
                Log::info('Vaca eliminada', [
                    'vaca_id' => $vacaId,
                    'codigo' => $codigo,
                    'user_id' => auth()->id()
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Obtener lista paginada de vacas con filtros
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(array $filters = [], int $perPage = 10)
    {
        return $this->repository->paginateWithFilters($filters, $perPage);
    }

    /**
     * Obtener una vaca con todas sus relaciones
     *
     * @param string $id
     * @return Vaca|null
     */
    public function findWithRelations(string $id): ?Vaca
    {
        return $this->repository->findWithRelations($id);
    }

    /**
     * Obtener vaca por ID
     *
     * @param string $id
     * @return Vaca|null
     */
    public function findById(string $id): ?Vaca
    {
        return $this->repository->findById($id);
    }

    /**
     * Obtener últimas vacas registradas
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLatest(int $limit = 5)
    {
        return $this->repository->getLatest($limit);
    }
}

