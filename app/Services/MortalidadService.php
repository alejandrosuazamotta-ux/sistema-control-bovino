<?php

namespace App\Services;

use App\Models\Mortalidad;
use App\Models\Vaca;
use App\Models\Cria;
use App\Repositories\MortalidadRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MortalidadService
{
    protected MortalidadRepository $repository;

    public function __construct(MortalidadRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Crear un nuevo registro de mortalidad y actualizar estado del animal
     *
     * @param array $data
     * @return Mortalidad
     * @throws \Exception
     */
    public function create(array $data): Mortalidad
    {
        return DB::transaction(function () use ($data) {
            // Validar que el animal existe
            $animal = $this->obtenerAnimal($data['animal_type'], $data['animal_id']);
            if (!$animal) {
                throw new \Exception('El animal seleccionado no existe.');
            }

            // Validar que el animal no tenga ya un registro de mortalidad
            if ($this->tieneMortalidadRegistrada($data['animal_type'], $data['animal_id'])) {
                throw new \Exception('Este animal ya tiene un registro de mortalidad.');
            }

            // Validar que la fecha no sea futura
            $fecha = \Carbon\Carbon::parse($data['fecha']);
            if ($fecha->isFuture()) {
                throw new \Exception('La fecha de muerte no puede ser futura.');
            }

            // Procesar archivo de evidencia si existe
            if (isset($data['acta_archivo']) && $data['acta_archivo']) {
                $archivo = $data['acta_archivo'];
                $extension = $archivo->getClientOriginalExtension();
                
                $nombreArchivo = 'mortalidad/' . uniqid() . '_' . time() . '.' . $extension;
                $ruta = $archivo->storeAs('public', $nombreArchivo);
                
                $data['acta_path'] = $nombreArchivo;
                unset($data['acta_archivo']); // Remover del array para no guardarlo en la BD
            }

            // Crear el registro de mortalidad
            $mortalidad = $this->repository->create($data);
            $mortalidad->load('animal');

            // Si es una vaca, cambiar su estado a "Muerta"
            if ($data['animal_type'] === Vaca::class) {
                $this->marcarVacaComoMuerta($data['animal_id']);
            }

            // Log de actividad
            Log::info('Registro de mortalidad creado', [
                'mortalidad_id' => $mortalidad->id_mortalidad,
                'animal_type' => $data['animal_type'],
                'animal_id' => $data['animal_id'],
                'fecha' => $mortalidad->fecha->format('Y-m-d'),
                'clasificacion' => $mortalidad->clasificacion,
                'user_id' => auth()->id()
            ]);

            return $mortalidad;
        });
    }

    /**
     * Actualizar un registro de mortalidad
     *
     * @param Mortalidad $mortalidad
     * @param array $data
     * @return Mortalidad
     * @throws \Exception
     */
    public function update(Mortalidad $mortalidad, array $data): Mortalidad
    {
        return DB::transaction(function () use ($mortalidad, $data) {
            // Validar que la fecha no sea futura
            if (isset($data['fecha'])) {
                $fecha = \Carbon\Carbon::parse($data['fecha']);
                if ($fecha->isFuture()) {
                    throw new \Exception('La fecha de muerte no puede ser futura.');
                }
            }

            // Procesar archivo de evidencia si existe
            if (isset($data['acta_archivo']) && $data['acta_archivo']) {
                // Eliminar archivo anterior si existe
                if ($mortalidad->acta_path && Storage::exists('public/' . $mortalidad->acta_path)) {
                    Storage::delete('public/' . $mortalidad->acta_path);
                }

                $archivo = $data['acta_archivo'];
                $extension = $archivo->getClientOriginalExtension();
                
                $nombreArchivo = 'mortalidad/' . uniqid() . '_' . time() . '.' . $extension;
                $ruta = $archivo->storeAs('public', $nombreArchivo);
                
                $data['acta_path'] = $nombreArchivo;
                unset($data['acta_archivo']); // Remover del array para no guardarlo en la BD
            }

            $this->repository->update($mortalidad, $data);
            $mortalidad->refresh();
            $mortalidad->load('animal');

            // Log de actividad
            Log::info('Registro de mortalidad actualizado', [
                'mortalidad_id' => $mortalidad->id_mortalidad,
                'user_id' => auth()->id()
            ]);

            return $mortalidad;
        });
    }

    /**
     * Eliminar un registro de mortalidad y restaurar estado del animal
     *
     * @param Mortalidad $mortalidad
     * @return bool
     * @throws \Exception
     */
    public function delete(Mortalidad $mortalidad): bool
    {
        return DB::transaction(function () use ($mortalidad) {
            $mortalidadId = $mortalidad->id_mortalidad;
            $animalType = $mortalidad->animal_type;
            $animalId = $mortalidad->animal_id;
            $actaPath = $mortalidad->acta_path;

            $deleted = $this->repository->delete($mortalidad);

            if ($deleted) {
                // Eliminar archivo de evidencia si existe
                if ($actaPath && Storage::exists('public/' . $actaPath)) {
                    Storage::delete('public/' . $actaPath);
                }

                // Si era una vaca, restaurar su estado (si aplica)
                if ($animalType === Vaca::class) {
                    $vaca = Vaca::find($animalId);
                    if ($vaca && !$vaca->mortalidad()->exists()) {
                        // Solo restaurar si no hay otro registro de mortalidad
                        // Por ahora, no cambiamos el estado automáticamente al eliminar
                        // ya que podría ser un error y el animal realmente está muerto
                    }
                }

                // Log de actividad
                Log::info('Registro de mortalidad eliminado', [
                    'mortalidad_id' => $mortalidadId,
                    'animal_type' => $animalType,
                    'animal_id' => $animalId,
                    'user_id' => auth()->id()
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Obtener lista paginada de mortalidades con filtros
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
     * Obtener una mortalidad con todas sus relaciones
     *
     * @param string $id
     * @return Mortalidad|null
     */
    public function findWithRelations(string $id): ?Mortalidad
    {
        return $this->repository->findWithRelations($id);
    }

    /**
     * Obtener mortalidad por ID
     *
     * @param string $id
     * @return Mortalidad|null
     */
    public function findById(string $id): ?Mortalidad
    {
        return $this->repository->findById($id);
    }

    /**
     * Obtener mortalidades recientes
     *
     * @param int $dias
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecientes(int $dias = 30)
    {
        return $this->repository->getRecientes($dias);
    }

    /**
     * Obtener estadísticas de mortalidad
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
            'mortalidad_por_mes' => $this->repository->getDatosGraficaMortalidadPorMes(12),
            'por_tipo_animal' => $this->repository->getDatosGraficaPorTipoAnimal(),
            'por_clasificacion' => $this->repository->getDatosGraficaPorClasificacion(),
        ];
    }

    /**
     * Obtener mortalidades por rango de fechas
     *
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPorRangoFechas(string $fechaInicio, string $fechaFin)
    {
        return $this->repository->getPorRangoFechas($fechaInicio, $fechaFin);
    }

    /**
     * Obtener el animal según su tipo
     *
     * @param string $animalType
     * @param int $animalId
     * @return Vaca|Cria|null
     */
    protected function obtenerAnimal(string $animalType, int $animalId)
    {
        if ($animalType === Vaca::class) {
            return Vaca::find($animalId);
        } elseif ($animalType === Cria::class) {
            return Cria::find($animalId);
        }
        
        return null;
    }

    /**
     * Verificar si el animal ya tiene un registro de mortalidad
     *
     * @param string $animalType
     * @param int $animalId
     * @return bool
     */
    protected function tieneMortalidadRegistrada(string $animalType, int $animalId): bool
    {
        return Mortalidad::where('animal_type', $animalType)
            ->where('animal_id', $animalId)
            ->exists();
    }

    /**
     * Marcar una vaca como muerta
     *
     * @param int $vacaId
     * @return void
     */
    protected function marcarVacaComoMuerta(int $vacaId): void
    {
        $vaca = Vaca::find($vacaId);
        if ($vaca) {
            // Actualizar estado_salud a "Muerta"
            $vaca->estado_salud = 'Muerta';
            $vaca->save();
            
            Log::info('Vaca marcada como muerta', [
                'vaca_id' => $vacaId,
                'codigo' => $vaca->codigo
            ]);
        }
    }
}

