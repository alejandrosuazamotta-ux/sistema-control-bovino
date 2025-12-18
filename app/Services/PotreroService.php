<?php

namespace App\Services;

use App\Models\Potrero;
use App\Repositories\PotreroRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PotreroService
{
    protected PotreroRepository $repository;

    public function __construct(PotreroRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Crear un nuevo potrero
     *
     * @param array $data
     * @return Potrero
     * @throws \Exception
     */
    public function create(array $data): Potrero
    {
        return DB::transaction(function () use ($data) {
            $potrero = $this->repository->create($data);

            Log::info('Potrero creado', [
                'potrero_id' => $potrero->id_potrero,
                'nombre' => $potrero->nombre,
                'user_id' => auth()->id()
            ]);

            return $potrero;
        });
    }

    /**
     * Actualizar un potrero
     *
     * @param Potrero $potrero
     * @param array $data
     * @return Potrero
     * @throws \Exception
     */
    public function update(Potrero $potrero, array $data): Potrero
    {
        return DB::transaction(function () use ($potrero, $data) {
            $this->repository->update($potrero, $data);
            $potrero->refresh();

            Log::info('Potrero actualizado', [
                'potrero_id' => $potrero->id_potrero,
                'user_id' => auth()->id()
            ]);

            return $potrero;
        });
    }

    /**
     * Eliminar un potrero
     *
     * @param Potrero $potrero
     * @return bool
     * @throws \Exception
     */
    public function delete(Potrero $potrero): bool
    {
        return DB::transaction(function () use ($potrero) {
            if ($potrero->vacas()->count() > 0) {
                throw new \Exception('No se puede eliminar el potrero porque tiene vacas asignadas.');
            }

            $potreroId = $potrero->id_potrero;
            $nombre = $potrero->nombre;

            $deleted = $this->repository->delete($potrero);

            if ($deleted) {
                Log::info('Potrero eliminado', [
                    'potrero_id' => $potreroId,
                    'nombre' => $nombre,
                    'user_id' => auth()->id()
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Obtener lista paginada de potreros con filtros
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
     * Obtener un potrero por ID
     *
     * @param string $id
     * @return Potrero|null
     */
    public function findById(string $id): ?Potrero
    {
        return $this->repository->findById($id);
    }

    /**
     * Obtener datos para gráficas
     *
     * @return array
     */
    public function getDatosGraficas(): array
    {
        return [
            'por_capacidad' => $this->repository->getDatosGraficaPorCapacidad(),
            'ocupacion_por_potrero' => $this->repository->getDatosGraficaOcupacionPorPotrero(10),
            'uso_por_mes' => $this->repository->getDatosGraficaUsoPorMes(12),
        ];
    }
}

