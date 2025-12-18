<?php

namespace App\Services;

use App\Models\Cria;
use App\Repositories\CriaRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CriaService
{
    protected CriaRepository $repository;

    public function __construct(CriaRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Crear una nueva cría
     *
     * @param array $data
     * @return Cria
     * @throws \Exception
     */
    public function create(array $data): Cria
    {
        return DB::transaction(function () use ($data) {
            // Validar que la vaca madre existe
            $vacaMadre = \App\Models\Vaca::find($data['id_vaca_madre']);
            if (!$vacaMadre) {
                throw new \Exception('La vaca madre seleccionada no existe.');
            }

            // Validar que la fecha de nacimiento no sea futura
            $fechaNacimiento = \Carbon\Carbon::parse($data['fecha_nacimiento']);
            if ($fechaNacimiento->isFuture()) {
                throw new \Exception('La fecha de nacimiento no puede ser futura.');
            }

            // Validar que fecha_tatuado no sea anterior a fecha_nacimiento
            if (isset($data['fecha_tatuado']) && $data['fecha_tatuado'] !== null) {
                $fechaTatuado = \Carbon\Carbon::parse($data['fecha_tatuado']);
                if ($fechaTatuado->lt($fechaNacimiento)) {
                    throw new \Exception('La fecha de tatuado no puede ser anterior a la fecha de nacimiento.');
                }
            }

            // Validar que fecha_destete no sea anterior a fecha_nacimiento
            if (isset($data['fecha_destete']) && $data['fecha_destete'] !== null) {
                $fechaDestete = \Carbon\Carbon::parse($data['fecha_destete']);
                if ($fechaDestete->lt($fechaNacimiento)) {
                    throw new \Exception('La fecha de destete no puede ser anterior a la fecha de nacimiento.');
                }
            }

            // Si estado_destete es "Destetada" pero no hay fecha_destete, usar fecha actual
            if (isset($data['estado_destete']) && $data['estado_destete'] === 'Destetada' && !isset($data['fecha_destete'])) {
                $data['fecha_destete'] = now()->format('Y-m-d');
            }

            // Si hay fecha_destete, asegurar que estado_destete sea "Destetada"
            if (isset($data['fecha_destete']) && $data['fecha_destete'] !== null) {
                $data['estado_destete'] = 'Destetada';
            }

            $cria = $this->repository->create($data);
            $cria->load('vacaMadre');

            // Log de actividad
            Log::info('Cría creada', [
                'cria_id' => $cria->id_cria,
                'vaca_madre_id' => $cria->id_vaca_madre,
                'sexo' => $cria->sexo,
                'fecha_nacimiento' => $cria->fecha_nacimiento->format('Y-m-d'),
                'user_id' => auth()->id()
            ]);

            return $cria;
        });
    }

    /**
     * Actualizar una cría
     *
     * @param Cria $cria
     * @param array $data
     * @return Cria
     * @throws \Exception
     */
    public function update(Cria $cria, array $data): Cria
    {
        return DB::transaction(function () use ($cria, $data) {
            // Si estado_destete cambia a "Destetada" pero no hay fecha_destete, usar fecha actual
            if (isset($data['estado_destete']) && $data['estado_destete'] === 'Destetada' && !isset($data['fecha_destete'])) {
                if ($cria->fecha_destete === null) {
                    $data['fecha_destete'] = now()->format('Y-m-d');
                }
            }

            // Si hay fecha_destete, asegurar que estado_destete sea "Destetada"
            if (isset($data['fecha_destete']) && $data['fecha_destete'] !== null) {
                $data['estado_destete'] = 'Destetada';
            } elseif (isset($data['estado_destete']) && $data['estado_destete'] === 'No destetada') {
                // Si cambia a "No destetada", limpiar fecha_destete
                $data['fecha_destete'] = null;
            }

            $this->repository->update($cria, $data);
            $cria->refresh();
            $cria->load('vacaMadre');

            // Log de actividad
            Log::info('Cría actualizada', [
                'cria_id' => $cria->id_cria,
                'user_id' => auth()->id()
            ]);

            return $cria;
        });
    }

    /**
     * Eliminar una cría
     *
     * @param Cria $cria
     * @return bool
     * @throws \Exception
     */
    public function delete(Cria $cria): bool
    {
        return DB::transaction(function () use ($cria) {
            $criaId = $cria->id_cria;
            $vacaMadreId = $cria->id_vaca_madre;

            $deleted = $this->repository->delete($cria);

            if ($deleted) {
                // Log de actividad
                Log::info('Cría eliminada', [
                    'cria_id' => $criaId,
                    'vaca_madre_id' => $vacaMadreId,
                    'user_id' => auth()->id()
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Obtener lista paginada de crías con filtros
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
     * Obtener una cría con todas sus relaciones
     *
     * @param string $id
     * @return Cria|null
     */
    public function findWithRelations(string $id): ?Cria
    {
        return $this->repository->findWithRelations($id);
    }

    /**
     * Obtener cría por ID
     *
     * @param string $id
     * @return Cria|null
     */
    public function findById(string $id): ?Cria
    {
        return $this->repository->findById($id);
    }

    /**
     * Obtener crías de una vaca madre
     *
     * @param int $vacaId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPorVacaMadre(int $vacaId)
    {
        return $this->repository->getPorVacaMadre($vacaId);
    }

    /**
     * Obtener crías próximas al destete
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProximasAlDestete()
    {
        return $this->repository->getProximasAlDestete();
    }

    /**
     * Obtener estadísticas de crías
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
            'nacimientos_por_mes' => $this->repository->getDatosGraficaNacimientosPorMes(12),
            'por_sexo' => $this->repository->getDatosGraficaPorSexo(),
            'por_concepcion' => $this->repository->getDatosGraficaPorConcepcion(),
        ];
    }

    /**
     * Obtener crías por rango de edad
     *
     * @param int $edadMinDias
     * @param int $edadMaxDias
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPorRangoEdad(int $edadMinDias, int $edadMaxDias)
    {
        return $this->repository->getPorRangoEdad($edadMinDias, $edadMaxDias);
    }
}

