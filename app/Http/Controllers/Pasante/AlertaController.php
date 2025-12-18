<?php

namespace App\Http\Controllers\Pasante;

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
     * Mostrar lista de alertas asignadas al pasante
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

        // Filtrar solo alertas asignadas al pasante actual
        $alertas = $this->alertaService->getAlertasPaginadas($filters, 15, auth()->id());
        $contador = $this->alertaService->getContadorAlertas(auth()->id());
        $datosGraficas = $this->alertaService->getDatosGraficas(auth()->id());

        return view('pasante.alertas.index', compact('alertas', 'contador', 'filters', 'datosGraficas'));
    }

    /**
     * Marcar una alerta como vista (para pasante)
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function marcarVista(Request $request, string $id)
    {
        try {
            $notificacion = Notificacion::find($id);
            if (!$notificacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alerta no encontrada.'
                ], 404);
            }

            Gate::authorize('marcarVista', $notificacion);

            $success = $this->alertaService->marcarComoVista((int)$id);
            
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Alerta marcada como vista.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo marcar la alerta como vista.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al marcar alerta como vista: ' . $e->getMessage(), [
                'alerta_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al marcar alerta como vista: ' . $e->getMessage()
            ], 500);
        }
    }
}

