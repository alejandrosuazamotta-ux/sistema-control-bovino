<?php

namespace App\Services;

use App\Models\AsignacionPotrero;
use App\Models\Potrero;
use App\Models\Vaca;
use App\Repositories\AsignacionPotreroRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AsignacionPotreroService
{
    protected AsignacionPotreroRepository $repository;

    public function __construct(AsignacionPotreroRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Validar capacidad del potrero
     *
     * @param Potrero $potrero
     * @throws ValidationException
     */
    protected function validarCapacidadPotrero(Potrero $potrero): void
    {
        $ocupacion = $potrero->vacas()->whereNull('id_potrero')->orWhere('id_potrero', $potrero->id_potrero)->count();
        
        if ($ocupacion >= $potrero->capacidad) {
            throw ValidationException::withMessages([
                'id_potrero' => 'El potrero seleccionado está lleno.'
            ]);
        }
    }

    /**
     * Validar aforo del potrero
     *
     * @param Potrero $potrero
     * @param Vaca $vaca
     * @throws ValidationException
     */
    protected function validarAforoPotrero(Potrero $potrero, Vaca $vaca): void
    {
        if (!$potrero->area_hectareas || $potrero->area_hectareas <= 0) {
            return;
        }

        $uggVaca = $vaca->peso_kg ? ($vaca->peso_kg / 450) : 0;
        $totalUGG = $potrero->calcularAforoActual() * $potrero->area_hectareas + $uggVaca;
        $aforoNuevo = $totalUGG / $potrero->area_hectareas;
        
        if ($potrero->aforo_maximo && $aforoNuevo > $potrero->aforo_maximo) {
            throw ValidationException::withMessages([
                'id_potrero' => "El aforo máximo sería excedido. Aforo actual: " . number_format($potrero->calcularAforoActual(), 2) . " UGG/ha, máximo: " . number_format($potrero->aforo_maximo, 2) . " UGG/ha"
            ]);
        }
    }

    /**
     * Validar que la vaca no esté asignada activamente
     *
     * @param int $vacaId
     * @param int $potreroId
     * @throws ValidationException
     */
    protected function validarAsignacionActiva(int $vacaId, int $potreroId): void
    {
        $existeAsignacionActiva = AsignacionPotrero::where('id_vaca', $vacaId)
            ->where('id_potrero', $potreroId)
            ->whereNull('fecha_salida')
            ->exists();

        if ($existeAsignacionActiva) {
            throw ValidationException::withMessages([
                'id_vaca' => 'Esta vaca ya está asignada activamente a este potrero. Debe finalizar la asignación anterior primero.'
            ]);
        }
    }

    /**
     * Crear una nueva asignación
     *
     * @param array $data
     * @return AsignacionPotrero
     * @throws \Exception
     */
    public function create(array $data): AsignacionPotrero
    {
        return DB::transaction(function () use ($data) {
            $potrero = Potrero::findOrFail($data['id_potrero']);
            $vaca = Vaca::findOrFail($data['id_vaca']);

            // Validaciones de negocio
            $this->validarCapacidadPotrero($potrero);
            $this->validarAforoPotrero($potrero, $vaca);
            $this->validarAsignacionActiva($data['id_vaca'], $data['id_potrero']);

            // Actualizar el potrero de la vaca solo si no hay fecha de salida
            if (!isset($data['fecha_salida']) || !$data['fecha_salida']) {
                $vaca->id_potrero = $data['id_potrero'];
                $vaca->save();
            }

            // Crear el registro de asignación
            $asignacion = $this->repository->create($data);
            $asignacion->load(['vaca', 'potrero']);

            // Actualizar aforo del potrero
            if ($potrero->area_hectareas && $potrero->area_hectareas > 0) {
                $potrero->aforo_actual = $potrero->calcularAforoActual();
                $potrero->save();
            }

            Log::info('Asignación de potrero creada', [
                'asignacion_id' => $asignacion->id_asignacion,
                'vaca_id' => $asignacion->id_vaca,
                'potrero_id' => $asignacion->id_potrero,
                'user_id' => auth()->id()
            ]);

            return $asignacion;
        });
    }

    /**
     * Actualizar una asignación
     *
     * @param AsignacionPotrero $asignacion
     * @param array $data
     * @return AsignacionPotrero
     * @throws \Exception
     */
    public function update(AsignacionPotrero $asignacion, array $data): AsignacionPotrero
    {
        return DB::transaction(function () use ($asignacion, $data) {
            // Verificar capacidad del potrero si se cambia
            if (isset($data['id_potrero']) && $data['id_potrero'] != $asignacion->id_potrero) {
                $potrero = Potrero::findOrFail($data['id_potrero']);
                $ocupacion = $potrero->vacas->count();
                
                if ($ocupacion >= $potrero->capacidad) {
                    throw ValidationException::withMessages([
                        'id_potrero' => 'El potrero seleccionado está lleno.'
                    ]);
                }
            }

            // Actualizar el potrero de la vaca
            if (isset($data['id_vaca']) && isset($data['id_potrero'])) {
                $vaca = Vaca::findOrFail($data['id_vaca']);
                $vaca->id_potrero = $data['id_potrero'];
                $vaca->save();
            }

            $this->repository->update($asignacion, $data);
            $asignacion->refresh();
            $asignacion->load(['vaca', 'potrero']);

            Log::info('Asignación de potrero actualizada', [
                'asignacion_id' => $asignacion->id_asignacion,
                'user_id' => auth()->id()
            ]);

            return $asignacion;
        });
    }

    /**
     * Eliminar una asignación
     *
     * @param AsignacionPotrero $asignacion
     * @return bool
     * @throws \Exception
     */
    public function delete(AsignacionPotrero $asignacion): bool
    {
        return DB::transaction(function () use ($asignacion) {
            $asignacionId = $asignacion->id_asignacion;
            $vacaId = $asignacion->id_vaca;
            $potreroId = $asignacion->id_potrero;

            $deleted = $this->repository->delete($asignacion);

            if ($deleted) {
                Log::info('Asignación de potrero eliminada', [
                    'asignacion_id' => $asignacionId,
                    'vaca_id' => $vacaId,
                    'potrero_id' => $potreroId,
                    'user_id' => auth()->id()
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Obtener lista paginada de asignaciones con filtros
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
     * Obtener una asignación por ID
     *
     * @param string $id
     * @return AsignacionPotrero|null
     */
    public function findById(string $id): ?AsignacionPotrero
    {
        return $this->repository->findById($id);
    }

    /**
     * Calcular carga UGG promedio por potrero
     *
     * @param int $potreroId
     * @return float
     */
    public function calcularCargaUGG(int $potreroId): float
    {
        $asignaciones = AsignacionPotrero::where('id_potrero', $potreroId)
            ->whereNotNull('carga_ugg')
            ->get();

        if ($asignaciones->isEmpty()) {
            return 0;
        }

        return $asignaciones->avg('carga_ugg') ?? 0;
    }

    /**
     * Calcular días de descanso promedio por potrero
     *
     * @param int $potreroId
     * @return float
     */
    public function calcularDiasDescanso(int $potreroId): float
    {
        $asignaciones = AsignacionPotrero::where('id_potrero', $potreroId)
            ->whereNotNull('dias_descanso')
            ->where('dias_descanso', '>', 0)
            ->get();

        if ($asignaciones->isEmpty()) {
            return 0;
        }

        return $asignaciones->avg('dias_descanso') ?? 0;
    }

    /**
     * Obtener historial de rotación de un potrero
     *
     * @param int $potreroId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerHistorialRotacion(int $potreroId, int $limit = 20)
    {
        return AsignacionPotrero::where('id_potrero', $potreroId)
            ->with(['vaca', 'potrero'])
            ->orderBy('fecha_asignacion', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener estadísticas de rotación por potrero
     *
     * @param int $potreroId
     * @return array
     */
    public function obtenerEstadisticasRotacion(int $potreroId): array
    {
        $asignaciones = AsignacionPotrero::where('id_potrero', $potreroId)->get();

        return [
            'total_asignaciones' => $asignaciones->count(),
            'asignaciones_activas' => $asignaciones->whereNull('fecha_salida')->count(),
            'asignaciones_finalizadas' => $asignaciones->whereNotNull('fecha_salida')->count(),
            'dias_estancia_promedio' => $asignaciones->whereNotNull('dias_estancia')->avg('dias_estancia') ?? 0,
            'dias_descanso_promedio' => $this->calcularDiasDescanso($potreroId),
            'carga_ugg_promedio' => $this->calcularCargaUGG($potreroId),
            'aforo_kg_promedio' => $asignaciones->whereNotNull('aforo_kg')->avg('aforo_kg') ?? 0,
        ];
    }

    /**
     * Obtener datos para gráficas
     *
     * @return array
     */
    public function getDatosGraficas(): array
    {
        return [
            'asignaciones_por_potrero' => $this->repository->getDatosGraficaAsignacionesPorPotrero(10),
            'rotaciones_por_mes' => $this->repository->getDatosGraficaRotacionesPorMes(12),
            'potreros_mas_usados' => $this->repository->getDatosGraficaPotrerosMasUsados(10),
            'carga_ugg_por_potrero' => $this->repository->getDatosGraficaCargaUGG(),
            'dias_descanso_por_potrero' => $this->repository->getDatosGraficaDiasDescanso(),
            'uso_por_potrero' => $this->repository->getDatosGraficaUsoPorPotrero(),
        ];
    }
}

