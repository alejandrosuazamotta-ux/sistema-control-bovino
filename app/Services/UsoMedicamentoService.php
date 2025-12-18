<?php

namespace App\Services;

use App\Models\UsoMedicamento;
use App\Models\Retiro;
use App\Repositories\UsoMedicamentoRepository;
use App\Repositories\RetiroRepository;
use App\Services\InventarioBodegaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UsoMedicamentoService
{
    protected UsoMedicamentoRepository $repository;
    protected RetiroRepository $retiroRepository;
    protected ?InventarioBodegaService $inventarioService;

    public function __construct(
        UsoMedicamentoRepository $repository,
        RetiroRepository $retiroRepository,
        ?InventarioBodegaService $inventarioService = null
    ) {
        $this->repository = $repository;
        $this->retiroRepository = $retiroRepository;
        $this->inventarioService = $inventarioService;
    }

    /**
     * Crear un nuevo uso de medicamento y generar retiro automático si aplica
     *
     * @param array $data
     * @return UsoMedicamento
     * @throws \Exception
     */
    public function create(array $data): UsoMedicamento
    {
        return DB::transaction(function () use ($data) {
            // Validar que el medicamento existe y está activo
            $medicamento = \App\Models\Medicamento::find($data['id_medicamento']);
            if (!$medicamento) {
                throw new \Exception('El medicamento seleccionado no existe.');
            }
            if (!$medicamento->activo) {
                throw new \Exception('El medicamento seleccionado no está activo.');
            }

            // Validar que la vaca existe
            $vaca = \App\Models\Vaca::find($data['id_vaca']);
            if (!$vaca) {
                throw new \Exception('La vaca seleccionada no existe.');
            }

            // Validar que la fecha de aplicación no sea futura
            $fechaAplicacion = \Carbon\Carbon::parse($data['fecha_aplicacion']);
            if ($fechaAplicacion->isFuture()) {
                throw new \Exception('La fecha de aplicación no puede ser futura.');
            }

            // Asignar user_id si es pasante
            if (auth()->check() && auth()->user()->hasRole('pasante')) {
                $data['user_id'] = auth()->id();
            }

            // Procesar archivo de evidencia si existe
            if (isset($data['evidencia_archivo']) && $data['evidencia_archivo']) {
                $archivo = $data['evidencia_archivo'];
                $extension = $archivo->getClientOriginalExtension();
                
                $nombreArchivo = 'uso_medicamentos/' . uniqid() . '_' . time() . '.' . $extension;
                $ruta = $archivo->storeAs('public', $nombreArchivo);
                
                $data['evidencia_path'] = $nombreArchivo;
                unset($data['evidencia_archivo']); // Remover del array para no guardarlo en la BD
            }

            // Crear el uso de medicamento
            $uso = $this->repository->create($data);
            $uso->load(['medicamento', 'vaca', 'personal', 'usuario']);

            // Si el medicamento tiene período de retiro, crear retiro automático
            if ($uso->medicamento && $uso->medicamento->periodo_retiro_dias > 0) {
                $this->crearRetiroAutomatico($uso);
            }

            // Registrar salida en inventario si existe el servicio y hay cantidad
            if ($this->inventarioService && isset($data['dosis_aplicada']) && $data['dosis_aplicada'] > 0) {
                try {
                    $this->inventarioService->registrarSalidaPorUsoMedicamento(
                        $uso->id_medicamento,
                        $data['dosis_aplicada'],
                        $uso->id_uso,
                        $uso->id_vaca
                    );
                } catch (\Exception $e) {
                    // Log del error pero no fallar la creación del uso
                    Log::warning('Error al registrar salida en inventario', [
                        'uso_id' => $uso->id_uso,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Log de actividad
            Log::info('Uso de medicamento creado', [
                'uso_id' => $uso->id_uso,
                'medicamento_id' => $uso->id_medicamento,
                'vaca_id' => $uso->id_vaca,
                'fecha_aplicacion' => $uso->fecha_aplicacion->format('Y-m-d'),
                'user_id' => auth()->id()
            ]);

            return $uso;
        });
    }

    /**
     * Actualizar un uso de medicamento
     *
     * @param UsoMedicamento $uso
     * @param array $data
     * @return UsoMedicamento
     * @throws \Exception
     */
    public function update(UsoMedicamento $uso, array $data): UsoMedicamento
    {
        return DB::transaction(function () use ($uso, $data) {
            // Procesar archivo de evidencia si existe
            if (isset($data['evidencia_archivo']) && $data['evidencia_archivo']) {
                // Eliminar archivo anterior si existe
                if ($uso->evidencia_path && Storage::exists('public/' . $uso->evidencia_path)) {
                    Storage::delete('public/' . $uso->evidencia_path);
                }

                $archivo = $data['evidencia_archivo'];
                $extension = $archivo->getClientOriginalExtension();
                
                $nombreArchivo = 'uso_medicamentos/' . uniqid() . '_' . time() . '.' . $extension;
                $ruta = $archivo->storeAs('public', $nombreArchivo);
                
                $data['evidencia_path'] = $nombreArchivo;
                unset($data['evidencia_archivo']); // Remover del array para no guardarlo en la BD
            }

            $this->repository->update($uso, $data);
            $uso->refresh();
            $uso->load(['medicamento', 'vaca', 'personal', 'retiro']);

            // Si cambió el medicamento o la fecha, actualizar retiro si existe
            if (isset($data['id_medicamento']) || isset($data['fecha_aplicacion'])) {
                if ($uso->retiro) {
                    $this->actualizarRetiroDesdeUso($uso);
                } elseif ($uso->medicamento && $uso->medicamento->periodo_retiro_dias > 0) {
                    // Si no tenía retiro pero ahora el medicamento requiere, crearlo
                    $this->crearRetiroAutomatico($uso);
                }
            }

            // Log de actividad
            Log::info('Uso de medicamento actualizado', [
                'uso_id' => $uso->id_uso,
                'user_id' => auth()->id()
            ]);

            return $uso;
        });
    }

    /**
     * Eliminar un uso de medicamento
     *
     * @param UsoMedicamento $uso
     * @return bool
     * @throws \Exception
     */
    public function delete(UsoMedicamento $uso): bool
    {
        return DB::transaction(function () use ($uso) {
            $usoId = $uso->id_uso;
            $vacaId = $uso->id_vaca;

            // Eliminar retiro asociado si existe
            if ($uso->retiro) {
                $this->retiroRepository->delete($uso->retiro);
            }

            $deleted = $this->repository->delete($uso);

            if ($deleted) {
                // Log de actividad
                Log::info('Uso de medicamento eliminado', [
                    'uso_id' => $usoId,
                    'vaca_id' => $vacaId,
                    'user_id' => auth()->id()
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Crear retiro automático basado en el uso de medicamento
     *
     * @param UsoMedicamento $uso
     * @return Retiro
     * @throws \Exception
     */
    protected function crearRetiroAutomatico(UsoMedicamento $uso): Retiro
    {
        if (!$uso->medicamento || $uso->medicamento->periodo_retiro_dias <= 0) {
            throw new \Exception('El medicamento no requiere período de retiro.');
        }

        // Verificar que no exista ya un retiro para este uso
        if ($uso->tieneRetiro()) {
            throw new \Exception('Este uso de medicamento ya tiene un retiro asociado.');
        }

        // Calcular fecha fin
        $fechaFin = $uso->calcularFechaFinRetiro();
        
        if (!$fechaFin) {
            throw new \Exception('No se pudo calcular la fecha de fin del retiro.');
        }

        // Crear retiro
        $retiro = $this->retiroRepository->create([
            'id_vaca' => $uso->id_vaca,
            'id_uso_medicamento' => $uso->id_uso,
            'fecha_inicio' => $uso->fecha_aplicacion,
            'fecha_fin' => $fechaFin,
            'tipo_retiro' => 'Ordeño', // Por defecto retiro de ordeño
            'activo' => true,
            'observaciones' => "Retiro automático generado por aplicación de medicamento: {$uso->medicamento->nombre}"
        ]);

        // Log de actividad
        Log::info('Retiro automático creado', [
            'retiro_id' => $retiro->id_retiro,
            'uso_id' => $uso->id_uso,
            'vaca_id' => $uso->id_vaca,
            'fecha_inicio' => $retiro->fecha_inicio->format('Y-m-d'),
            'fecha_fin' => $retiro->fecha_fin->format('Y-m-d'),
            'user_id' => auth()->id()
        ]);

        return $retiro;
    }

    /**
     * Actualizar retiro existente basado en cambios en el uso
     *
     * @param UsoMedicamento $uso
     * @return Retiro
     * @throws \Exception
     */
    protected function actualizarRetiroDesdeUso(UsoMedicamento $uso): Retiro
    {
        if (!$uso->retiro) {
            throw new \Exception('No existe retiro para actualizar.');
        }

        $fechaFin = $uso->calcularFechaFinRetiro();
        
        if (!$fechaFin) {
            // Si el medicamento ya no requiere retiro, desactivar el retiro
            $this->retiroRepository->update($uso->retiro, ['activo' => false]);
            return $uso->retiro;
        }

        // Actualizar fechas del retiro
        $this->retiroRepository->update($uso->retiro, [
            'fecha_inicio' => $uso->fecha_aplicacion,
            'fecha_fin' => $fechaFin,
        ]);

        $uso->retiro->refresh();

        return $uso->retiro;
    }

    /**
     * Obtener lista paginada de usos con filtros
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
     * Obtener un uso con todas sus relaciones
     *
     * @param string $id
     * @return UsoMedicamento|null
     */
    public function findWithRelations(string $id): ?UsoMedicamento
    {
        return $this->repository->findWithRelations($id);
    }

    /**
     * Obtener uso por ID
     *
     * @param string $id
     * @return UsoMedicamento|null
     */
    public function findById(string $id): ?UsoMedicamento
    {
        return $this->repository->findById($id);
    }

    /**
     * Obtener usos filtrados (sin paginación)
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
            'uso_por_medicamento' => $this->repository->getDatosGraficaUsoPorMedicamento(10),
            'aplicaciones_por_mes' => $this->repository->getDatosGraficaAplicacionesPorMes(12),
            'top_vacas' => $this->repository->getDatosGraficaTopVacas(10),
        ];
    }
}

