<?php

namespace App\Services;

use App\Models\ProduccionLechera;
use App\Models\Vaca;
use App\Repositories\ProduccionLecheraRepository;
use App\Services\RetiroService;
use App\Services\SaludService;
use App\Constants\ExceptionMessages;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProduccionLecheraService
{
    protected ProduccionLecheraRepository $repository;
    protected RetiroService $retiroService;
    protected SaludService $saludService;

    public function __construct(
        ProduccionLecheraRepository $repository,
        RetiroService $retiroService,
        SaludService $saludService
    ) {
        $this->repository = $repository;
        $this->retiroService = $retiroService;
        $this->saludService = $saludService;
    }

    /**
     * Crear una nueva producción lechera con validaciones
     *
     * @param array $data
     * @return ProduccionLechera
     * @throws \Exception
     */
    public function create(array $data): ProduccionLechera
    {
        return DB::transaction(function () use ($data) {
            // Validar que la vaca no esté en retiro en la fecha de producción
            $this->validarVacaNoEnRetiro($data['id_vaca'], $data['fecha']);

            // Validar que la vaca existe
            $vaca = Vaca::find($data['id_vaca']);
            if (!$vaca) {
                throw new \Exception(ExceptionMessages::PRODUCCION_VACA_NO_EXISTE);
            }

            // Validar que la vaca esté en estado de lactancia
            if ($vaca->estado_reproductivo !== 'Lactancia') {
                throw new \Exception(sprintf(ExceptionMessages::PRODUCCION_VACA_NO_LACTANCIA, $vaca->estado_reproductivo));
            }

            // Validar que la vaca no tenga restricción de ordeño activa
            if ($this->saludService->tieneRestriccionOrdeñoActiva($data['id_vaca'], $data['fecha'])) {
                throw new \Exception(ExceptionMessages::PRODUCCION_RESTRICCION_ORDENO);
            }

            // Validar que la vaca no esté inhabilitada
            if ($this->saludService->estaVacaInhabilitada($data['id_vaca'])) {
                throw new \Exception(ExceptionMessages::PRODUCCION_VACA_INHABILITADA);
            }

            // Validar que no exista duplicado (misma vaca, fecha y turno)
            if ($this->repository->existeDuplicado($data['id_vaca'], $data['fecha'], $data['turno'])) {
                throw new \Exception(ExceptionMessages::PRODUCCION_DUPLICADA);
            }

            // Marcar como excluida por retiro si aplica (por si acaso)
            // Esto es redundante pero asegura consistencia
            $data['excluida_por_retiro'] = false;
            $data['excluida_por_sanidad'] = false;
            
            // Crear el registro
            $produccion = $this->repository->create($data);

            // Log de actividad (sin datos sensibles innecesarios)
            Log::info('Producción lechera creada', [
                'produccion_id' => $produccion->id_produccion,
                'vaca_id' => $produccion->id_vaca,
                'fecha' => $produccion->fecha->format('Y-m-d'),
                'turno' => $produccion->turno,
                'cantidad_litros' => $produccion->cantidad_leche,
                'user_id' => auth()->id() // Necesario para auditoría
            ]);

            return $produccion;
        });
    }

    /**
     * Actualizar una producción lechera con validaciones
     *
     * @param ProduccionLechera $produccion
     * @param array $data
     * @return ProduccionLechera
     * @throws \Exception
     */
    public function update(ProduccionLechera $produccion, array $data): ProduccionLechera
    {
        return DB::transaction(function () use ($produccion, $data) {
            // Validar que la vaca no esté en retiro
            $vacaId = $data['id_vaca'] ?? $produccion->id_vaca;
            $fecha = $data['fecha'] ?? $produccion->fecha->format('Y-m-d');
            
            $this->validarVacaNoEnRetiro($vacaId, $fecha);

            // Validar que la vaca no tenga restricción de ordeño activa
            if ($this->saludService->tieneRestriccionOrdeñoActiva($vacaId, $fecha)) {
                throw new \Exception(ExceptionMessages::PRODUCCION_ACTUALIZAR_RESTRICCION_ORDENO);
            }

            // Validar que la vaca no esté inhabilitada
            if ($this->saludService->estaVacaInhabilitada($vacaId)) {
                throw new \Exception(ExceptionMessages::PRODUCCION_ACTUALIZAR_VACA_INHABILITADA);
            }

            // Validar que no exista duplicado (misma vaca, fecha y turno)
            $vacaId = $data['id_vaca'] ?? $produccion->id_vaca;
            $fecha = $data['fecha'] ?? $produccion->fecha->format('Y-m-d');
            $turno = $data['turno'] ?? $produccion->turno;

            if ($this->repository->existeDuplicado($vacaId, $fecha, $turno, $produccion->id_produccion)) {
                throw new \Exception(ExceptionMessages::PRODUCCION_ACTUALIZAR_DUPLICADA);
            }

            // Actualizar el registro
            $this->repository->update($produccion, $data);
            $produccion->refresh();

            // Log de actividad (sin datos sensibles innecesarios)
            Log::info('Producción lechera actualizada', [
                'produccion_id' => $produccion->id_produccion,
                'vaca_id' => $produccion->id_vaca,
                'fecha' => $produccion->fecha->format('Y-m-d'),
                'turno' => $produccion->turno,
                'user_id' => auth()->id() // Necesario para auditoría
            ]);

            return $produccion;
        });
    }

    /**
     * Eliminar una producción lechera
     *
     * @param ProduccionLechera $produccion
     * @return bool
     * @throws \Exception
     */
    public function delete(ProduccionLechera $produccion): bool
    {
        return DB::transaction(function () use ($produccion) {
            $produccionId = $produccion->id_produccion;
            $vacaId = $produccion->id_vaca;

            $deleted = $this->repository->delete($produccion);

            if ($deleted) {
            // Log de actividad (sin datos sensibles innecesarios)
            Log::info('Producción lechera eliminada', [
                'produccion_id' => $produccionId,
                'vaca_id' => $vacaId,
                'user_id' => auth()->id() // Necesario para auditoría
            ]);
            }

            return $deleted;
        });
    }

    /**
     * Obtener lista paginada de producciones con filtros
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
     * Obtener una producción con todas sus relaciones
     *
     * @param string $id
     * @return ProduccionLechera|null
     */
    public function findWithRelations(string $id): ?ProduccionLechera
    {
        return $this->repository->findWithRelations($id);
    }

    /**
     * Obtener producción por ID
     *
     * @param string $id
     * @return ProduccionLechera|null
     */
    public function findById(string $id): ?ProduccionLechera
    {
        return $this->repository->findById($id);
    }

    /**
     * Validar que la vaca no esté en retiro
     *
     * @param int $vacaId
     * @param string|null $fecha Fecha de la producción (para validar retiro en esa fecha)
     * @throws \Exception
     */
    protected function validarVacaNoEnRetiro(int $vacaId, ?string $fecha = null): void
    {
        $vaca = Vaca::find($vacaId);
        
        if (!$vaca) {
            throw new \Exception(ExceptionMessages::PRODUCCION_VACA_NO_EXISTE);
        }

        // Validar retiro de ordeño
        $fechaValidacion = $fecha ? \Carbon\Carbon::parse($fecha) : now();
        
        if ($this->retiroService->tieneRetiroOrdeñoActivo($vacaId, $fechaValidacion)) {
            throw new \Exception(ExceptionMessages::PRODUCCION_VACA_EN_RETIRO_ORDENO);
        }

        // Validar retiro de producción
        if ($this->retiroService->tieneRetiroProduccionActivo($vacaId, $fechaValidacion)) {
            throw new \Exception(ExceptionMessages::PRODUCCION_VACA_EN_RETIRO_PRODUCCION);
        }

        // Si la vaca está en tratamiento, loguear advertencia (sin datos sensibles)
        if ($vaca->estado_salud === 'En tratamiento') {
            Log::warning('Producción registrada para vaca en tratamiento', [
                'vaca_id' => $vacaId,
                'estado_salud' => $vaca->estado_salud,
                'user_id' => auth()->id() // Necesario para auditoría
            ]);
        }
    }

    /**
     * Obtener estadísticas de producción
     *
     * @return array
     */
    public function getEstadisticas(): array
    {
        return [
            'total_produccion_mes' => $this->repository->getTotalProduccionMesActual(),
            'promedio_diario' => $this->repository->getPromedioDiario(30),
            'vacas_activas' => Vaca::where('estado_reproductivo', 'Lactancia')->count(),
        ];
    }

    /**
     * Obtener producción por potrero
     *
     * @param int $potreroId
     * @param string|null $fechaInicio
     * @param string|null $fechaFin
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getProduccionPorPotrero(int $potreroId, ?string $fechaInicio = null, ?string $fechaFin = null)
    {
        return $this->repository->getProduccionPorPotrero($potreroId, $fechaInicio, $fechaFin);
    }

    /**
     * Obtener curva de lactancia de una vaca
     *
     * @param int $vacaId
     * @param string|null $fechaInicio
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCurvaLactancia(int $vacaId, ?string $fechaInicio = null)
    {
        return $this->repository->getCurvaLactancia($vacaId, $fechaInicio);
    }

    /**
     * Obtener pico de producción de una vaca
     *
     * @param int $vacaId
     * @return ProduccionLechera|null
     */
    public function getPicoProduccion(int $vacaId): ?ProduccionLechera
    {
        return $this->repository->getPicoProduccion($vacaId);
    }

    /**
     * Obtener promedio de producción de una vaca
     *
     * @param int $vacaId
     * @param int|null $mes
     * @param int|null $anio
     * @return float
     */
    public function getPromedioVaca(int $vacaId, ?int $mes = null, ?int $anio = null): float
    {
        return $this->repository->getPromedioVaca($vacaId, $mes, $anio);
    }

    /**
     * Marcar producciones como excluidas por sanidad
     * 
     * @param int $vacaId
     * @param string $fechaDesde Fecha desde la cual marcar producciones
     * @param string $motivo Motivo de la exclusión
     * @return int Número de producciones marcadas
     */
    public function marcarExcluidasPorSanidad(int $vacaId, string $fechaDesde, string $motivo = 'Prueba sanitaria positiva'): int
    {
        return DB::transaction(function () use ($vacaId, $fechaDesde, $motivo) {
            $producciones = ProduccionLechera::where('id_vaca', $vacaId)
                ->where('fecha', '>=', $fechaDesde)
                ->where('excluida_por_sanidad', false)
                ->get();

            $count = 0;
            foreach ($producciones as $produccion) {
                $produccion->excluida_por_sanidad = true;
                $produccion->observaciones = ($produccion->observaciones ? $produccion->observaciones . ' | ' : '') . 
                    "Excluida por sanidad: {$motivo}";
                $produccion->save();
                $count++;
            }

            if ($count > 0) {
                Log::info('Producciones marcadas como excluidas por sanidad', [
                    'vaca_id' => $vacaId,
                    'fecha_desde' => $fechaDesde,
                    'motivo' => $motivo,
                    'cantidad' => $count,
                    'user_id' => auth()->id()
                ]);
            }

            return $count;
        });
    }

    /**
     * Quitar exclusión por sanidad de producciones
     * 
     * @param int $vacaId
     * @param string|null $fechaDesde Fecha desde la cual quitar exclusión (null = todas)
     * @return int Número de producciones actualizadas
     */
    public function quitarExclusionPorSanidad(int $vacaId, ?string $fechaDesde = null): int
    {
        return DB::transaction(function () use ($vacaId, $fechaDesde) {
            $query = ProduccionLechera::where('id_vaca', $vacaId)
                ->where('excluida_por_sanidad', true);

            if ($fechaDesde) {
                $query->where('fecha', '>=', $fechaDesde);
            }

            $producciones = $query->get();
            $count = 0;

            foreach ($producciones as $produccion) {
                $produccion->excluida_por_sanidad = false;
                // Limpiar observaciones relacionadas con sanidad
                $produccion->observaciones = preg_replace('/\s*\|\s*Excluida por sanidad:.*$/i', '', $produccion->observaciones ?? '');
                $produccion->save();
                $count++;
            }

            if ($count > 0) {
                Log::info('Exclusión por sanidad removida de producciones', [
                    'vaca_id' => $vacaId,
                    'fecha_desde' => $fechaDesde,
                    'cantidad' => $count,
                    'user_id' => auth()->id()
                ]);
            }

            return $count;
        });
    }
}

