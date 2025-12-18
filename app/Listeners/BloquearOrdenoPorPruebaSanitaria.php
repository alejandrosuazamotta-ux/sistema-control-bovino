<?php

namespace App\Listeners;

use App\Events\PruebaSanitariaResultadoPositivo;
use App\Services\ProduccionLecheraService;
use App\Services\AlertaService;
use Illuminate\Support\Facades\Log;

class BloquearOrdenoPorPruebaSanitaria
{
    protected ProduccionLecheraService $produccionService;
    protected AlertaService $alertaService;

    /**
     * Create the event listener.
     */
    public function __construct(
        ProduccionLecheraService $produccionService,
        AlertaService $alertaService
    ) {
        $this->produccionService = $produccionService;
        $this->alertaService = $alertaService;
    }

    /**
     * Handle the event.
     */
    public function handle(PruebaSanitariaResultadoPositivo $event): void
    {
        $prueba = $event->pruebaSanitaria;
        $vaca = $prueba->vaca;

        if (!$vaca) {
            Log::error('Vaca no encontrada en prueba sanitaria', [
                'prueba_id' => $prueba->id_prueba
            ]);
            return;
        }

        // Solo procesar si es Mastitis positiva (bloquea ordeño)
        if ($prueba->tipo_prueba === 'Mastitis' && $prueba->resultado === 'Positivo' && $prueba->restriccion_ordeño) {
            // Marcar producciones como excluidas por sanidad
            $this->produccionService->marcarExcluidasPorSanidad(
                $vaca->id_vaca,
                $prueba->fecha_prueba->format('Y-m-d'),
                'Mastitis positiva - Prueba sanitaria'
            );

            // Generar alerta
            $this->generarAlertaMastitis($vaca, $prueba);

            Log::info('Ordeño bloqueado por prueba sanitaria positiva (Mastitis)', [
                'prueba_id' => $prueba->id_prueba,
                'vaca_id' => $vaca->id_vaca,
                'codigo_vaca' => $vaca->codigo
            ]);
        }

        // Si es Brucelosis o Tuberculosis positiva, inhabilitar vaca
        if (in_array($prueba->tipo_prueba, ['Brucelosis', 'Tuberculosis']) && $prueba->resultado === 'Positivo' && $prueba->inhabilitada) {
            // Marcar producciones como excluidas
            $this->produccionService->marcarExcluidasPorSanidad(
                $vaca->id_vaca,
                $prueba->fecha_prueba->format('Y-m-d'),
                $prueba->tipo_prueba . ' positiva - Prueba sanitaria'
            );

            // Cambiar estado de la vaca
            $vaca->estado_salud = 'Inhabilitada';
            $vaca->save();

            Log::warning('Vaca inhabilitada por prueba sanitaria positiva', [
                'prueba_id' => $prueba->id_prueba,
                'tipo_prueba' => $prueba->tipo_prueba,
                'vaca_id' => $vaca->id_vaca,
                'codigo_vaca' => $vaca->codigo
            ]);
        }
    }

    /**
     * Generar alerta de mastitis
     */
    protected function generarAlertaMastitis($vaca, $prueba): void
    {
        $severidad = $prueba->severidad ?? 'No especificada';
        $nivel = match($severidad) {
            'Severa' => 'urgente',
            'Moderada' => 'advertencia',
            default => 'informacion'
        };

        // Verificar si ya existe una alerta similar hoy
        $existeAlerta = \App\Models\Notificacion::where('tipo', 'mastitis')
            ->where('entidad_tipo', \App\Models\PruebaSanitaria::class)
            ->where('entidad_id', $prueba->id_prueba)
            ->where('leida', false)
            ->whereDate('created_at', today())
            ->exists();

        if (!$existeAlerta) {
            \App\Models\Notificacion::create([
                'tipo' => 'mastitis',
                'nivel' => $nivel,
                'titulo' => "Mastitis detectada - Vaca {$vaca->codigo}",
                'mensaje' => "La vaca {$vaca->codigo} tiene mastitis positiva (Severidad: {$severidad}). Se ha aplicado restricción de ordeño.",
                'entidad_tipo' => \App\Models\PruebaSanitaria::class,
                'entidad_id' => $prueba->id_prueba,
                'fecha_referencia' => $prueba->fecha_prueba,
                'leida' => false,
            ]);
        }
    }
}
