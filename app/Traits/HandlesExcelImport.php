<?php

namespace App\Traits;

use App\Jobs\ProcessExcelImportJob;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait HandlesExcelImport
{
    /**
     * Determinar si debe usar procesamiento asíncrono
     */
    protected function debeUsarProcesamientoAsync(string $rutaArchivo, ?bool $forzarAsync = null, ?object $importInstance = null): bool
    {
        if ($forzarAsync === true) return true;
        if ($forzarAsync === false) return false;

        $tamañoMB = filesize($rutaArchivo) / (1024 * 1024);
        if ($tamañoMB > 1) return true;

        if ($importInstance) {
            try {
                $rows = Excel::toArray($importInstance, $rutaArchivo);
                if (count($rows[0] ?? []) > 1000) return true;
            } catch (\Exception $e) {
                Log::warning('No se pudo determinar número de filas', ['error' => $e->getMessage()]);
            }
        }

        return false;
    }

    /**
     * Procesar importación con soporte para async/sync
     */
    protected function procesarImportacion(
        string $importClass,
        string $rutaTemporal,
        string $rutaArchivo,
        string $moduleName,
        ?bool $procesarAsync = null,
        ?object $importInstance = null
    ) {
        $usarAsync = $this->debeUsarProcesamientoAsync($rutaArchivo, $procesarAsync, $importInstance);

        if ($usarAsync) {
            ProcessExcelImportJob::dispatch(
                $importClass,
                $rutaTemporal,
                auth()->id(),
                $moduleName
            );

            return redirect()->back()
                ->with('info', "La importación de {$moduleName} se está procesando en segundo plano. Recibirá una notificación cuando se complete.");
        }

        return null; // Continuar con procesamiento síncrono
    }
}

