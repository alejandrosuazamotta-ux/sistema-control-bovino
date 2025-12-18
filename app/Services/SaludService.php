<?php

namespace App\Services;

use App\Models\Salud;
use App\Models\Vaca;
use App\Models\Notificacion;
use App\Repositories\SaludRepository;
use App\Services\AlertaService;
use App\Constants\ExceptionMessages;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SaludService
{
    protected SaludRepository $repository;
    protected AlertaService $alertaService;

    public function __construct(
        SaludRepository $repository,
        AlertaService $alertaService
    ) {
        $this->repository = $repository;
        $this->alertaService = $alertaService;
    }

    /**
     * Crear un nuevo registro de salud
     *
     * @param array $data
     * @return Salud
     * @throws \Exception
     */
    public function create(array $data): Salud
    {
        return DB::transaction(function () use ($data) {
            // Validación defensiva: vaca existe
            $vaca = Vaca::find($data['id_vaca']);
            if (!$vaca) {
                throw new \Exception(ExceptionMessages::SALUD_VACA_NO_EXISTE);
            }

            // Si es una prueba sanitaria, procesar lógica específica
            if (in_array($data['tipo_registro'], ['Prueba mastitis', 'Prueba Brucelosis', 'Prueba Tuberculosis'])) {
                $this->procesarPruebaSanitaria($data);
            }

            // Crear el registro
            $salud = $this->repository->create($data);
            $salud->load(['vaca', 'personal']);

            // Si es prueba sanitaria positiva, generar alertas después de crear el registro
            if (in_array($data['tipo_registro'], ['Prueba mastitis', 'Prueba Brucelosis', 'Prueba Tuberculosis'])) {
                if (isset($data['resultado']) && $data['resultado'] === 'Positivo') {
                    if ($data['tipo_prueba'] === 'Mastitis') {
                        $this->generarAlertaMastitis($vaca, $data, $salud);
                    }
                }
            }

            // Log de actividad
            Log::info('Registro de salud creado', [
                'salud_id' => $salud->id_salud,
                'vaca_id' => $salud->id_vaca,
                'tipo_registro' => $salud->tipo_registro,
                'tipo_prueba' => $salud->tipo_prueba ?? 'N/A',
                'resultado' => $salud->resultado ?? 'N/A',
                'user_id' => auth()->id()
            ]);

            return $salud;
        });
    }

    /**
     * Actualizar un registro de salud
     *
     * @param Salud $salud
     * @param array $data
     * @return Salud
     * @throws \Exception
     */
    public function update(Salud $salud, array $data): Salud
    {
        return DB::transaction(function () use ($salud, $data) {
            // Validación defensiva: vaca existe
            $vaca = Vaca::find($data['id_vaca'] ?? $salud->id_vaca);
            if (!$vaca) {
                throw new \Exception(ExceptionMessages::SALUD_VACA_NO_EXISTE);
            }

            // Si es una prueba sanitaria, procesar lógica específica
            if (in_array($data['tipo_registro'] ?? $salud->tipo_registro, ['Prueba mastitis', 'Prueba Brucelosis', 'Prueba Tuberculosis'])) {
                $this->procesarPruebaSanitaria($data, $salud);
            }

            // Actualizar el registro
            $this->repository->update($salud, $data);
            $salud->refresh();
            $salud->load(['vaca', 'personal']);

            // Si es prueba sanitaria positiva, generar alertas después de actualizar
            if (in_array($data['tipo_registro'] ?? $salud->tipo_registro, ['Prueba mastitis', 'Prueba Brucelosis', 'Prueba Tuberculosis'])) {
                if (isset($data['resultado']) && $data['resultado'] === 'Positivo') {
                    $tipoPrueba = $data['tipo_prueba'] ?? $salud->tipo_prueba;
                    if ($tipoPrueba === 'Mastitis') {
                        $this->generarAlertaMastitis($vaca, $data, $salud);
                    }
                }
            }

            // Log de actividad
            Log::info('Registro de salud actualizado', [
                'salud_id' => $salud->id_salud,
                'vaca_id' => $salud->id_vaca,
                'user_id' => auth()->id()
            ]);

            return $salud;
        });
    }

    /**
     * Eliminar un registro de salud
     *
     * @param Salud $salud
     * @return bool
     * @throws \Exception
     */
    public function delete(Salud $salud): bool
    {
        return DB::transaction(function () use ($salud) {
            $saludId = $salud->id_salud;
            $vacaId = $salud->id_vaca;

            $deleted = $this->repository->delete($salud);

            if ($deleted) {
                // Si tenía restricción de ordeño, verificar si hay otras restricciones
                if ($salud->restriccion_ordeño) {
                    $this->verificarRestriccionesOrdeño($vacaId);
                }

                // Log de actividad
                Log::info('Registro de salud eliminado', [
                    'salud_id' => $saludId,
                    'vaca_id' => $vacaId,
                    'user_id' => auth()->id()
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Procesar lógica de prueba sanitaria
     *
     * @param array $data
     * @param Salud|null $saludExistente
     * @return void
     */
    protected function procesarPruebaSanitaria(array &$data, ?Salud $saludExistente = null): void
    {
        $vaca = Vaca::find($data['id_vaca']);
        if (!$vaca) {
            return;
        }

        // Determinar tipo de prueba desde tipo_registro si no está explícito
        if (!isset($data['tipo_prueba']) && isset($data['tipo_registro'])) {
            $data['tipo_prueba'] = match($data['tipo_registro']) {
                'Prueba mastitis' => 'Mastitis',
                'Prueba Brucelosis' => 'Brucelosis',
                'Prueba Tuberculosis' => 'Tuberculosis',
                default => 'Otra'
            };
        }

        // Si el resultado es Positivo, aplicar restricciones
        if (isset($data['resultado']) && $data['resultado'] === 'Positivo') {
            switch ($data['tipo_prueba']) {
                case 'Mastitis':
                    // Mastitis positiva → restricción de ordeño
                    // La alerta se generará después de crear el registro
                    $data['restriccion_ordeño'] = true;
                    // Marcar producciones futuras como excluidas por sanidad
                    $this->marcarProduccionesExcluidasPorMastitis($vaca->id_vaca, $data['fecha'] ?? now()->format('Y-m-d'));
                    break;

                case 'Brucelosis':
                case 'Tuberculosis':
                    // Brucelosis/Tuberculosis positiva → inhabilitar vaca + alerta
                    $data['inhabilitada'] = true;
                    $this->inhabilitarVaca($vaca);
                    $this->generarAlertaEnfermedadGrave($vaca, $data['tipo_prueba']);
                    break;
            }
        } else {
            // Si el resultado cambia a Negativo, quitar restricciones
            if ($saludExistente && $saludExistente->restriccion_ordeño && ($data['resultado'] ?? null) === 'Negativo') {
                $data['restriccion_ordeño'] = false;
                // Quitar exclusión de producciones por sanidad
                $produccionService = app(\App\Services\ProduccionLecheraService::class);
                $produccionService->quitarExclusionPorSanidad($vaca->id_vaca, $saludExistente->fecha->format('Y-m-d'));
            }
            if ($saludExistente && $saludExistente->inhabilitada && ($data['resultado'] ?? null) === 'Negativo') {
                $data['inhabilitada'] = false;
                $this->habilitarVaca($vaca);
            }
        }

        // Si no hay fecha_resultado y el resultado no es Pendiente, usar fecha actual
        if (!isset($data['fecha_resultado']) && isset($data['resultado']) && $data['resultado'] !== 'Pendiente') {
            $data['fecha_resultado'] = now()->format('Y-m-d');
        }
    }

    /**
     * Generar alerta de mastitis positiva
     *
     * @param Vaca $vaca
     * @param array $data
     * @return void
     */
    protected function generarAlertaMastitis(Vaca $vaca, array $data, ?Salud $salud = null): void
    {
        $severidad = $data['severidad'] ?? 'No especificada';
        $nivel = match($severidad) {
            'Severa' => 'urgente',
            'Moderada' => 'advertencia',
            default => 'informacion'
        };

        // Usar el ID del registro si ya existe, o null si se está creando
        $saludId = $salud?->id_salud ?? $data['id_salud'] ?? null;

        // Verificar si ya existe una alerta similar hoy
        $existeAlerta = Notificacion::where('tipo', 'mastitis')
            ->where('entidad_tipo', Salud::class)
            ->where('entidad_id', $saludId)
            ->where('leida', false)
            ->whereDate('created_at', today())
            ->exists();

        if (!$existeAlerta) {
            Notificacion::create([
                'tipo' => 'mastitis',
                'nivel' => $nivel,
                'titulo' => "Mastitis detectada - Vaca {$vaca->codigo}",
                'mensaje' => "La vaca {$vaca->codigo} tiene mastitis positiva (Severidad: {$severidad}). Se ha aplicado restricción de ordeño.",
                'entidad_tipo' => Salud::class,
                'entidad_id' => $saludId,
                'fecha_referencia' => $data['fecha'] ?? now(),
                'leida' => false,
            ]);
        }
    }

    /**
     * Generar alerta de enfermedad grave (Brucelosis/Tuberculosis)
     *
     * @param Vaca $vaca
     * @param string $tipoPrueba
     * @return void
     */
    protected function generarAlertaEnfermedadGrave(Vaca $vaca, string $tipoPrueba): void
    {
        $existeAlerta = Notificacion::where('tipo', 'enfermedad_grave')
            ->where('entidad_tipo', Vaca::class)
            ->where('entidad_id', $vaca->id_vaca)
            ->where('leida', false)
            ->whereDate('created_at', today())
            ->exists();

        if (!$existeAlerta) {
            Notificacion::create([
                'tipo' => 'enfermedad_grave',
                'nivel' => 'urgente',
                'titulo' => "{$tipoPrueba} positiva - Vaca {$vaca->codigo} INHABILITADA",
                'mensaje' => "La vaca {$vaca->codigo} ha dado positivo en prueba de {$tipoPrueba}. La vaca ha sido inhabilitada automáticamente. Se recomienda sacrificio.",
                'entidad_tipo' => Vaca::class,
                'entidad_id' => $vaca->id_vaca,
                'fecha_referencia' => now(),
                'leida' => false,
            ]);
        }
    }

    /**
     * Inhabilitar vaca (cambiar estado)
     *
     * @param Vaca $vaca
     * @return void
     */
    protected function inhabilitarVaca(Vaca $vaca): void
    {
        $vaca->estado_salud = 'Inhabilitada';
        $vaca->save();

        Log::warning('Vaca inhabilitada por prueba sanitaria positiva', [
            'vaca_id' => $vaca->id_vaca,
            'codigo' => $vaca->codigo,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Habilitar vaca (quitar inhabilitación)
     *
     * @param Vaca $vaca
     * @return void
     */
    protected function habilitarVaca(Vaca $vaca): void
    {
        // Solo cambiar si estaba inhabilitada
        if ($vaca->estado_salud === 'Inhabilitada') {
            $vaca->estado_salud = 'Sana'; // O el estado que corresponda
            $vaca->save();

            Log::info('Vaca habilitada - prueba sanitaria negativa', [
                'vaca_id' => $vaca->id_vaca,
                'codigo' => $vaca->codigo,
                'user_id' => auth()->id()
            ]);
        }
    }

    /**
     * Verificar restricciones de ordeño después de eliminar registro
     *
     * @param int $vacaId
     * @return void
     */
    protected function verificarRestriccionesOrdeño(int $vacaId): void
    {
        // Si no hay más restricciones activas, podría quitarse alguna marca
        // Esta lógica se puede extender según necesidades
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
        return $this->repository->paginateWithFilters($filters, $perPage);
    }

    /**
     * Obtener un registro con relaciones
     *
     * @param string $id
     * @return Salud|null
     */
    public function findWithRelations(string $id): ?Salud
    {
        return $this->repository->findWithRelations($id);
    }

    /**
     * Obtener registro por ID
     *
     * @param string $id
     * @return Salud|null
     */
    public function findById(string $id): ?Salud
    {
        return $this->repository->findById($id);
    }

    /**
     * Verificar si una vaca tiene restricción de ordeño activa
     *
     * @param int $vacaId
     * @param string|null $fecha
     * @return bool
     */
    public function tieneRestriccionOrdeñoActiva(int $vacaId, ?string $fecha = null): bool
    {
        return $this->repository->tieneRestriccionOrdeñoActiva($vacaId, $fecha);
    }

    /**
     * Verificar si una vaca está inhabilitada
     *
     * @param int $vacaId
     * @return bool
     */
    public function estaVacaInhabilitada(int $vacaId): bool
    {
        return $this->repository->estaVacaInhabilitada($vacaId);
    }

    /**
     * Obtener estadísticas de salud
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
            'por_tipo' => $this->repository->getDatosGraficaPorTipo(),
            'por_resultado' => $this->repository->getDatosGraficaResultados(),
            'por_mes' => $this->repository->getDatosGraficaPorMes(12),
        ];
    }

    /**
     * Marcar producciones como excluidas por sanidad (mastitis positiva)
     * 
     * @param int $vacaId
     * @param string $fechaDesde Fecha desde la cual marcar producciones (fecha de la prueba)
     * @return int Número de producciones marcadas
     */
    public function marcarProduccionesExcluidasPorMastitis(int $vacaId, string $fechaDesde): int
    {
        $produccionService = app(\App\Services\ProduccionLecheraService::class);
        return $produccionService->marcarExcluidasPorSanidad($vacaId, $fechaDesde, 'Mastitis positiva detectada');
    }

    /**
     * Obtener el repository (para acceso desde otros servicios)
     *
     * @return SaludRepository
     */
    public function getRepository(): SaludRepository
    {
        return $this->repository;
    }
}

