<?php

namespace App\Console\Commands;

use App\Services\AlertaService;
use Illuminate\Console\Command;

class GenerarAlertasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alertas:generar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generar todas las alertas del sistema';

    /**
     * Execute the console command.
     */
    public function handle(AlertaService $alertaService)
    {
        $this->info('Generando alertas del sistema...');
        
        $resultados = $alertaService->generarTodasLasAlertas();
        
        $this->info('Alertas generadas exitosamente:');
        $this->table(
            ['Tipo de Alerta', 'Cantidad'],
            [
                ['Preparto (21 días)', $resultados['preparto_21']],
                ['Preparto (7 días)', $resultados['preparto_7']],
                ['Celo', $resultados['celo']],
                ['Destete', $resultados['destete']],
                ['Retiros Activos', $resultados['retiros_activos']],
            ]
        );
        
        $total = array_sum($resultados);
        $this->info("Total de alertas generadas: {$total}");
        
        return Command::SUCCESS;
    }
}
