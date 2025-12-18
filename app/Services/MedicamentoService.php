<?php

namespace App\Services;

use App\Models\Medicamento;
use App\Repositories\MedicamentoRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MedicamentoService
{
    protected MedicamentoRepository $repository;

    public function __construct(MedicamentoRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Crear un nuevo medicamento
     *
     * @param array $data
     * @return Medicamento
     * @throws \Exception
     */
    public function create(array $data): Medicamento
    {
        return DB::transaction(function () use ($data) {
            $medicamento = $this->repository->create($data);

            // Log de actividad
            Log::info('Medicamento creado', [
                'medicamento_id' => $medicamento->id_medicamento,
                'nombre' => $medicamento->nombre,
                'periodo_retiro' => $medicamento->periodo_retiro_dias,
                'user_id' => auth()->id()
            ]);

            return $medicamento;
        });
    }

    /**
     * Actualizar un medicamento
     *
     * @param Medicamento $medicamento
     * @param array $data
     * @return Medicamento
     * @throws \Exception
     */
    public function update(Medicamento $medicamento, array $data): Medicamento
    {
        return DB::transaction(function () use ($medicamento, $data) {
            $this->repository->update($medicamento, $data);
            $medicamento->refresh();

            // Log de actividad
            Log::info('Medicamento actualizado', [
                'medicamento_id' => $medicamento->id_medicamento,
                'nombre' => $medicamento->nombre,
                'user_id' => auth()->id()
            ]);

            return $medicamento;
        });
    }

    /**
     * Eliminar un medicamento
     *
     * @param Medicamento $medicamento
     * @return bool
     * @throws \Exception
     */
    public function delete(Medicamento $medicamento): bool
    {
        return DB::transaction(function () use ($medicamento) {
            // Verificar que no tenga usos asociados
            if ($medicamento->usosMedicamentos()->count() > 0) {
                throw new \Exception('No se puede eliminar el medicamento porque tiene usos registrados.');
            }

            $medicamentoId = $medicamento->id_medicamento;
            $nombre = $medicamento->nombre;

            $deleted = $this->repository->delete($medicamento);

            if ($deleted) {
                // Log de actividad
                Log::info('Medicamento eliminado', [
                    'medicamento_id' => $medicamentoId,
                    'nombre' => $nombre,
                    'user_id' => auth()->id()
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Obtener lista paginada de medicamentos con filtros
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
     * Obtener un medicamento por ID
     *
     * @param string $id
     * @return Medicamento|null
     */
    public function findById(string $id): ?Medicamento
    {
        return $this->repository->findById($id);
    }

    /**
     * Obtener medicamentos activos
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActivos()
    {
        return $this->repository->getActivos();
    }

    /**
     * Obtener medicamentos con retiro de ordeño
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getConRetiroOrdeño()
    {
        return $this->repository->getConRetiroOrdeño();
    }

    /**
     * Obtener medicamentos filtrados (sin paginación)
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
            'stock_por_medicamento' => $this->repository->getDatosGraficaStockPorMedicamento(10),
            'proximos_vencer' => $this->repository->getDatosGraficaProximosVencer(30),
        ];
    }
}

