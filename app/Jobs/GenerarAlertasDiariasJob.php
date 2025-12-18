<?php

namespace App\Jobs;

use App\Services\AlertaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerarAlertasDiariasJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(AlertaService $alertaService): void
    {
        try {
            Log::info('Iniciando generación de alertas diarias');
            
            $resultados = $alertaService->generarTodasLasAlertas();
            
            Log::info('Alertas diarias generadas exitosamente', [
                'resultados' => $resultados,
                'total' => array_sum($resultados)
            ]);
        } catch (\Exception $e) {
            Log::error('Error al generar alertas diarias', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
}
