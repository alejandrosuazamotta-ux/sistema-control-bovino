<?php

namespace App\Services;

use App\Models\PruebaSanitaria;
use App\Models\Vaca;
use App\Repositories\PruebaSanitariaRepository;
use App\Events\PruebaSanitariaResultadoPositivo;
use App\Events\PruebaSanitariaNegativa;
use App\Constants\ExceptionMessages;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PruebaSanitariaService
{
    protected PruebaSanitariaRepository $repository;

    public function __construct(PruebaSanitariaRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Crear una nueva prueba sanitaria
     *
     * @param array $data
     * @return PruebaSanitaria
     * @throws \Exception
     */
    public function create(array $data): PruebaSanitaria
    {
        return DB::transaction(function () use ($data) {
            // Validación defensiva: vaca existe
            $vaca = Vaca::find($data['id_vaca']);
            if (!$vaca) {
                throw new \Exception(ExceptionMessages::PRUEBA_SANITARIA_VACA_NO_EXISTE);
            }

            // Si es Pasante, asignar user_id
            if (auth()->user()->hasRole('pasante')) {
                $data['user_id'] = auth()->id();
            }

            // Procesar archivo de evidencia si existe
            if (isset($data['evidencia_archivo']) && $data['evidencia_archivo']) {
                $archivo = $data['evidencia_archivo'];
                $extension = $archivo->getClientOriginalExtension();
                $tipo = in_array($extension, ['jpg', 'jpeg', 'png']) ? 'imagen' : 'pdf';
                
                $nombreArchivo = 'pruebas_sanitarias/' . uniqid() . '_' . time() . '.' . $extension;
                $ruta = $archivo->storeAs('public', $nombreArchivo);
                
                $data['evidencia_archivo'] = $nombreArchivo;
                $data['evidencia_tipo'] = $tipo;
            }

            // Procesar lógica según tipo de prueba y resultado
            $this->procesarPruebaSanitaria($data);

            // Crear el registro
            $prueba = $this->repository->create($data);
            $prueba->load(['vaca', 'personal', 'usuario']);

            // Disparar eventos según resultado
            if ($prueba->resultado === 'Positivo') {
                event(new PruebaSanitariaResultadoPositivo($prueba));
            } elseif ($prueba->resultado === 'Negativo') {
                event(new PruebaSanitariaNegativa($prueba));
            }

            // Log de actividad (sin datos sensibles innecesarios)
            Log::info('Prueba sanitaria creada', [
                'prueba_id' => $prueba->id_prueba,
                'vaca_id' => $prueba->id_vaca,
                'tipo_prueba' => $prueba->tipo_prueba,
                'resultado' => $prueba->resultado,
                'user_id' => auth()->id() // Necesario para auditoría
            ]);

            return $prueba;
        });
    }

    /**
     * Actualizar una prueba sanitaria
     *
     * @param PruebaSanitaria $prueba
     * @param array $data
     * @return PruebaSanitaria
     * @throws \Exception
     */
    public function update(PruebaSanitaria $prueba, array $data): PruebaSanitaria
    {
        return DB::transaction(function () use ($prueba, $data) {
            // Validación defensiva: prueba no cerrada
            if ($prueba->cerrada) {
                throw new \Exception(ExceptionMessages::PRUEBA_SANITARIA_CERRADA);
            }

            // Validación defensiva: vaca existe
            $vaca = Vaca::find($data['id_vaca'] ?? $prueba->id_vaca);
            if (!$vaca) {
                throw new \Exception(ExceptionMessages::PRUEBA_SANITARIA_VACA_NO_EXISTE);
            }

            // Guardar resultado anterior para comparar
            $resultadoAnterior = $prueba->resultado;
            $tipoPruebaAnterior = $prueba->tipo_prueba;

            // Procesar nuevo archivo de evidencia si existe
            if (isset($data['evidencia_archivo']) && $data['evidencia_archivo']) {
                // Eliminar archivo anterior si existe
                if ($prueba->evidencia_archivo) {
                    Storage::delete('public/' . $prueba->evidencia_archivo);
                }

                $archivo = $data['evidencia_archivo'];
                $extension = $archivo->getClientOriginalExtension();
                $tipo = in_array($extension, ['jpg', 'jpeg', 'png']) ? 'imagen' : 'pdf';
                
                $nombreArchivo = 'pruebas_sanitarias/' . uniqid() . '_' . time() . '.' . $extension;
                $ruta = $archivo->storeAs('public', $nombreArchivo);
                
                $data['evidencia_archivo'] = $nombreArchivo;
                $data['evidencia_tipo'] = $tipo;
            }

            // Procesar lógica según tipo de prueba y resultado
            $this->procesarPruebaSanitaria($data, $prueba);

            // Actualizar el registro
            $this->repository->update($prueba, $data);
            $prueba->refresh();
            $prueba->load(['vaca', 'personal', 'usuario']);

            // Disparar eventos si el resultado cambió
            if ($resultadoAnterior !== $prueba->resultado) {
                if ($prueba->resultado === 'Positivo') {
                    event(new PruebaSanitariaResultadoPositivo($prueba));
                } elseif ($prueba->resultado === 'Negativo') {
                    event(new PruebaSanitariaNegativa($prueba));
                }
            }

            // Log de actividad (sin datos sensibles innecesarios)
            Log::info('Prueba sanitaria actualizada', [
                'prueba_id' => $prueba->id_prueba,
                'vaca_id' => $prueba->id_vaca,
                'tipo_prueba' => $prueba->tipo_prueba,
                'resultado' => $prueba->resultado,
                'user_id' => auth()->id() // Necesario para auditoría
            ]);

            return $prueba;
        });
    }

    /**
     * Eliminar una prueba sanitaria
     *
     * @param PruebaSanitaria $prueba
     * @return bool
     * @throws \Exception
     */
    public function delete(PruebaSanitaria $prueba): bool
    {
        return DB::transaction(function () use ($prueba) {
            // Eliminar archivo de evidencia si existe
            if ($prueba->evidencia_archivo) {
                Storage::delete('public/' . $prueba->evidencia_archivo);
            }

            // Si tenía resultado positivo, quitar restricciones
            if ($prueba->resultado === 'Positivo') {
                $this->quitarRestricciones($prueba);
            }

            $resultado = $this->repository->delete($prueba);

            // Log de actividad (sin datos sensibles innecesarios)
            Log::info('Prueba sanitaria eliminada', [
                'prueba_id' => $prueba->id_prueba,
                'vaca_id' => $prueba->id_vaca,
                'user_id' => auth()->id() // Necesario para auditoría
            ]);

            return $resultado;
        });
    }

    /**
     * Cerrar una prueba sanitaria
     *
     * @param PruebaSanitaria $prueba
     * @return PruebaSanitaria
     */
    public function cerrar(PruebaSanitaria $prueba): PruebaSanitaria
    {
        $prueba->cerrar();
        $prueba->load(['vaca', 'personal', 'usuario']);

        // Log de actividad (sin datos sensibles innecesarios)
        Log::info('Prueba sanitaria cerrada', [
            'prueba_id' => $prueba->id_prueba,
            'vaca_id' => $prueba->id_vaca,
            'user_id' => auth()->id() // Necesario para auditoría
        ]);

        return $prueba;
    }

    /**
     * Procesar lógica de prueba sanitaria según tipo y resultado
     *
     * @param array $data
     * @param PruebaSanitaria|null $pruebaExistente
     * @return void
     */
    protected function procesarPruebaSanitaria(array &$data, ?PruebaSanitaria $pruebaExistente = null): void
    {
        $tipoPrueba = $data['tipo_prueba'] ?? $pruebaExistente?->tipo_prueba;
        $resultado = $data['resultado'] ?? $pruebaExistente?->resultado;

        // Si no hay fecha_resultado y el resultado no es Pendiente, usar fecha actual
        if (!isset($data['fecha_resultado']) && $resultado !== 'Pendiente') {
            $data['fecha_resultado'] = now()->format('Y-m-d');
        }

        // Lógica para Mastitis
        if ($tipoPrueba === 'Mastitis') {
            if ($resultado === 'Positivo') {
                $data['restriccion_ordeño'] = true;
                // Severidad es obligatoria para Mastitis positiva
                if (!isset($data['severidad']) || empty($data['severidad'])) {
                    $data['severidad'] = 'Moderada'; // Valor por defecto
                }
            } elseif ($resultado === 'Negativo') {
                $data['restriccion_ordeño'] = false;
                // Si había una prueba anterior positiva, quitar restricciones
                if ($pruebaExistente && $pruebaExistente->restriccion_ordeño) {
                    $this->quitarRestriccionOrdeño($pruebaExistente);
                }
            }
        }

        // Lógica para Brucelosis y Tuberculosis
        if (in_array($tipoPrueba, ['Brucelosis', 'Tuberculosis'])) {
            if ($resultado === 'Positivo') {
                $data['inhabilitada'] = true;
            } elseif ($resultado === 'Negativo') {
                $data['inhabilitada'] = false;
                // Si había una prueba anterior positiva, habilitar vaca
                if ($pruebaExistente && $pruebaExistente->inhabilitada) {
                    $this->habilitarVaca($pruebaExistente);
                }
            }
        }
    }

    /**
     * Quitar restricción de ordeño
     *
     * @param PruebaSanitaria $prueba
     * @return void
     */
    protected function quitarRestriccionOrdeño(PruebaSanitaria $prueba): void
    {
        $produccionService = app(ProduccionLecheraService::class);
        $produccionService->quitarExclusionPorSanidad(
            $prueba->id_vaca,
            $prueba->fecha_prueba->format('Y-m-d')
        );
    }

    /**
     * Habilitar vaca
     *
     * @param PruebaSanitaria $prueba
     * @return void
     */
    protected function habilitarVaca(PruebaSanitaria $prueba): void
    {
        $vaca = $prueba->vaca;
        if ($vaca && $vaca->estado_salud === 'Inhabilitada') {
            $vaca->estado_salud = 'Sana';
            $vaca->save();
        }
    }

    /**
     * Quitar todas las restricciones de una prueba
     *
     * @param PruebaSanitaria $prueba
     * @return void
     */
    protected function quitarRestricciones(PruebaSanitaria $prueba): void
    {
        if ($prueba->restriccion_ordeño) {
            $this->quitarRestriccionOrdeño($prueba);
        }

        if ($prueba->inhabilitada) {
            $this->habilitarVaca($prueba);
        }
    }

    /**
     * Obtener lista paginada con filtros
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(array $filters = [], int $perPage = 15)
    {
        // Si es Pasante, solo mostrar las que creó
        if (auth()->user()->hasRole('pasante')) {
            $filters['user_id'] = auth()->id();
        }

        return $this->repository->paginateWithFilters($filters, $perPage);
    }

    /**
     * Obtener prueba por ID con relaciones
     *
     * @param string $id
     * @return PruebaSanitaria|null
     */
    public function findWithRelations(string $id): ?PruebaSanitaria
    {
        return $this->repository->findWithRelations($id);
    }

    /**
     * Obtener prueba por ID
     *
     * @param string $id
     * @return PruebaSanitaria|null
     */
    public function findById(string $id): ?PruebaSanitaria
    {
        return $this->repository->findById($id);
    }

    /**
     * Obtener estadísticas
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
        return $this->repository->getDatosGraficas();
    }
}

