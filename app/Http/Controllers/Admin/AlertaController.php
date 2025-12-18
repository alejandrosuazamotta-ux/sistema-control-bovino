<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AlertaService;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class AlertaController extends Controller
{
    protected AlertaService $alertaService;

    public function __construct(AlertaService $alertaService)
    {
        $this->alertaService = $alertaService;
    }

    /**
     * Mostrar lista de alertas
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Notificacion::class);

        $filters = [
            'tipo' => $request->get('tipo'),
            'nivel' => $request->get('nivel'),
            'leida' => $request->get('leida'),
            'estado' => $request->get('estado'),
        ];

        $alertas = $this->alertaService->getAlertasPaginadas($filters, 15);
        $contador = $this->alertaService->getContadorAlertas();
        $datosGraficas = $this->alertaService->getDatosGraficas();

        return view('admin.alertas.index', compact('alertas', 'contador', 'filters', 'datosGraficas'));
    }

    /**
     * Obtener alertas no leídas (API)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function noLeidas()
    {
        $alertas = $this->alertaService->getAlertasNoLeidas(50);
        return response()->json($alertas);
    }

    /**
     * Marcar una alerta como leída
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function marcarLeida(Request $request, string $id)
    {
        try {
            $notificacion = Notificacion::find($id);
            if (!$notificacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alerta no encontrada.'
                ], 404);
            }

            Gate::authorize('update', $notificacion);

            $success = $this->alertaService->marcarComoLeida((int)$id);
            
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Alerta marcada como leída.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo marcar la alerta como leída.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al marcar alerta como leída: ' . $e->getMessage(), [
                'alerta_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al marcar alerta como leída: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar una alerta como atendida
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function marcarAtendida(Request $request, string $id)
    {
        try {
            $notificacion = Notificacion::find($id);
            if (!$notificacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alerta no encontrada.'
                ], 404);
            }

            Gate::authorize('marcarAtendida', $notificacion);

            $success = $this->alertaService->marcarComoAtendida((int)$id);
            
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Alerta marcada como atendida.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo marcar la alerta como atendida.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al marcar alerta como atendida: ' . $e->getMessage(), [
                'alerta_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al marcar alerta como atendida: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar todas las alertas como leídas
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function marcarTodasLeidas(Request $request)
    {
        try {
            $count = $this->alertaService->marcarTodasComoLeidas();
            
            return response()->json([
                'success' => true,
                'message' => "Se marcaron {$count} alerta(s) como leída(s)."
            ]);
        } catch (\Exception $e) {
            Log::error('Error al marcar todas las alertas como leídas: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al marcar todas las alertas como leídas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar alertas manualmente
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generar(Request $request)
    {
        try {
            $resultados = $this->alertaService->generarTodasLasAlertas();
            
            return response()->json([
                'success' => true,
                'message' => 'Alertas generadas exitosamente.',
                'resultados' => $resultados
            ]);
        } catch (\Exception $e) {
            Log::error('Error al generar alertas manualmente: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al generar alertas: ' . $e->getMessage()
            ], 500);
        }
    }
}
