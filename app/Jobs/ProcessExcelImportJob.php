<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ReflectionClass;

/**
 * Job para procesar importación de Excel de forma asíncrona
 * 
 * Este job permite procesar archivos Excel grandes sin bloquear la aplicación.
 * Se puede usar con cualquier clase Import que implemente ToModel.
 */
class ProcessExcelImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * El número de veces que se puede intentar el job.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * El número de segundos que el job puede ejecutarse antes de timeout.
     *
     * @var int
     */
    public $timeout = 600; // 10 minutos

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $importClass,
        public string $filePath,
        public ?int $userId = null,
        public ?string $moduleName = null
    ) {
        // Configurar queue específica si es necesario
        $this->onQueue('imports');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Iniciando importación asíncrona de Excel', [
                'import_class' => $this->importClass,
                'file_path' => $this->filePath,
                'module' => $this->moduleName,
                'user_id' => $this->userId
            ]);

            // Verificar que el archivo existe
            if (!Storage::exists($this->filePath)) {
                throw new \Exception("El archivo no existe: {$this->filePath}");
            }

            // Verificar que la clase Import existe
            if (!class_exists($this->importClass)) {
                throw new \Exception("La clase Import no existe: {$this->importClass}");
            }

            // Instanciar la clase Import correctamente según su constructor
            $importInstance = $this->createImportInstance($this->importClass);

            // Procesar la importación
            Excel::import($importInstance, $this->filePath);

            Log::info('Importación asíncrona completada exitosamente', [
                'import_class' => $this->importClass,
                'file_path' => $this->filePath,
                'module' => $this->moduleName,
                'user_id' => $this->userId
            ]);

            // Notificar al usuario del éxito
            $this->notificarExitoImportacion();

            // Eliminar el archivo temporal después de procesarlo
            Storage::delete($this->filePath);

        } catch (\Exception $e) {
            Log::error('Error en importación asíncrona de Excel', [
                'import_class' => $this->importClass,
                'file_path' => $this->filePath,
                'module' => $this->moduleName,
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-lanzar la excepción para que el job falle y se reintente
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job de importación Excel falló definitivamente', [
            'import_class' => $this->importClass,
            'file_path' => $this->filePath,
            'module' => $this->moduleName,
            'user_id' => $this->userId,
            'error' => $exception->getMessage()
        ]);

        // Notificar al usuario sobre el fallo
        $this->notificarFalloImportacion($exception);
    }

    /**
     * Crear instancia de Import class según su constructor
     * 
     * @param string $importClass
     * @return object
     * @throws \Exception
     */
    protected function createImportInstance(string $importClass): object
    {
        $reflection = new ReflectionClass($importClass);
        $constructor = $reflection->getConstructor();

        // Si no tiene constructor, instanciar sin parámetros
        if (!$constructor) {
            return new $importClass();
        }

        $parameters = $constructor->getParameters();

        // Si no tiene parámetros, instanciar sin parámetros
        if (empty($parameters)) {
            return new $importClass();
        }

        // Si tiene un parámetro, intentar resolverlo
        $firstParam = $parameters[0];
        $paramType = $firstParam->getType();

        if ($paramType && !$paramType->isBuiltin()) {
            $typeName = $paramType->getName();
            
            // Si es un Service, resolverlo desde el contenedor
            if (str_contains($typeName, 'Service')) {
                $service = app($typeName);
                return new $importClass($service);
            }
        }

        // Si no se puede determinar, intentar instanciar sin parámetros (puede fallar)
        // Esto es un fallback - idealmente todas las Import classes deberían tener constructores consistentes
        try {
            return new $importClass();
        } catch (\Throwable $e) {
            throw new \Exception("No se pudo instanciar {$importClass}. Constructor requiere parámetros que no se pueden resolver automáticamente.");
        }
    }

    /**
     * Notificar al usuario cuando la importación completa exitosamente
     */
    protected function notificarExitoImportacion(): void
    {
        if (!$this->userId) {
            return;
        }

        try {
            \App\Models\Notificacion::create([
                'tipo' => 'importacion_excel',
                'nivel' => 'success',
                'titulo' => "Importación completada - {$this->moduleName}",
                'mensaje' => "La importación de {$this->moduleName} se ha completado exitosamente. Revise los registros importados.",
                'entidad_tipo' => null,
                'entidad_id' => null,
                'fecha_referencia' => now(),
                'leida' => false,
            ]);
        } catch (\Exception $e) {
            Log::warning('No se pudo crear notificación de importación exitosa', [
                'error' => $e->getMessage(),
                'user_id' => $this->userId
            ]);
        }
    }

    /**
     * Notificar al usuario cuando la importación falla
     */
    protected function notificarFalloImportacion(\Throwable $exception): void
    {
        if (!$this->userId) {
            return;
        }

        try {
            \App\Models\Notificacion::create([
                'tipo' => 'importacion_excel',
                'nivel' => 'urgente',
                'titulo' => "Error en importación - {$this->moduleName}",
                'mensaje' => "La importación de {$this->moduleName} ha fallado: {$exception->getMessage()}. Revise los logs para más detalles.",
                'entidad_tipo' => null,
                'entidad_id' => null,
                'fecha_referencia' => now(),
                'leida' => false,
            ]);
        } catch (\Exception $e) {
            Log::warning('No se pudo crear notificación de fallo de importación', [
                'error' => $e->getMessage(),
                'user_id' => $this->userId
            ]);
        }
    }
}
