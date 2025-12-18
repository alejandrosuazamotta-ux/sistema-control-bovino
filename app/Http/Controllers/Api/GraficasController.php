<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AlimentacionService;
use App\Services\MedicamentoService;
use App\Services\UsoMedicamentoService;
use App\Services\RetiroService;
use App\Services\PotreroService;
use App\Services\AsignacionPotreroService;
use App\Services\ProduccionLecheraService;
use App\Services\RegistroReproductivoService;
use App\Services\CriaService;
use App\Services\MortalidadService;
use App\Services\SaludService;
use App\Services\InventarioBodegaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GraficasController extends Controller
{
    protected AlimentacionService $alimentacionService;
    protected MedicamentoService $medicamentoService;
    protected UsoMedicamentoService $usoMedicamentoService;
    protected RetiroService $retiroService;
    protected PotreroService $potreroService;
    protected AsignacionPotreroService $asignacionPotreroService;
    protected ProduccionLecheraService $produccionLecheraService;
    protected RegistroReproductivoService $registroReproductivoService;
    protected CriaService $criaService;
    protected MortalidadService $mortalidadService;
    protected SaludService $saludService;
    protected InventarioBodegaService $inventarioBodegaService;

    public function __construct(
        AlimentacionService $alimentacionService,
        MedicamentoService $medicamentoService,
        UsoMedicamentoService $usoMedicamentoService,
        RetiroService $retiroService,
        PotreroService $potreroService,
        AsignacionPotreroService $asignacionPotreroService,
        ProduccionLecheraService $produccionLecheraService,
        RegistroReproductivoService $registroReproductivoService,
        CriaService $criaService,
        MortalidadService $mortalidadService,
        SaludService $saludService,
        InventarioBodegaService $inventarioBodegaService
    ) {
        $this->alimentacionService = $alimentacionService;
        $this->medicamentoService = $medicamentoService;
        $this->usoMedicamentoService = $usoMedicamentoService;
        $this->retiroService = $retiroService;
        $this->potreroService = $potreroService;
        $this->asignacionPotreroService = $asignacionPotreroService;
        $this->produccionLecheraService = $produccionLecheraService;
        $this->registroReproductivoService = $registroReproductivoService;
        $this->criaService = $criaService;
        $this->mortalidadService = $mortalidadService;
        $this->saludService = $saludService;
        $this->inventarioBodegaService = $inventarioBodegaService;
    }

    /**
     * Obtener datos de gráficas para Alimentación
     */
    public function alimentacion(Request $request): JsonResponse
    {
        try {
            $datos = $this->alimentacionService->getDatosGraficas();
            return response()->json([
                'success' => true,
                'data' => $datos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener datos de gráficas para Medicamentos
     */
    public function medicamentos(Request $request): JsonResponse
    {
        try {
            $datos = $this->medicamentoService->getDatosGraficas();
            return response()->json([
                'success' => true,
                'data' => $datos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener datos de gráficas para Uso Medicamentos
     */
    public function usoMedicamentos(Request $request): JsonResponse
    {
        try {
            $datos = $this->usoMedicamentoService->getDatosGraficas();
            return response()->json([
                'success' => true,
                'data' => $datos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener datos de gráficas para Retiros
     */
    public function retiros(Request $request): JsonResponse
    {
        try {
            $datos = $this->retiroService->getDatosGraficas();
            return response()->json([
                'success' => true,
                'data' => $datos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener datos de gráficas para Potreros
     */
    public function potreros(Request $request): JsonResponse
    {
        try {
            $datos = $this->potreroService->getDatosGraficas();
            return response()->json([
                'success' => true,
                'data' => $datos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener datos de gráficas para Asignación Potreros
     */
    public function asignacionPotreros(Request $request): JsonResponse
    {
        try {
            $datos = $this->asignacionPotreroService->getDatosGraficas();
            return response()->json([
                'success' => true,
                'data' => $datos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener datos de gráficas para Producción Lechera
     */
    public function produccionLechera(Request $request): JsonResponse
    {
        try {
            // Nota: ProduccionLecheraService no tiene getDatosGraficas, usar Repository directamente
            $repository = app(\App\Repositories\ProduccionLecheraRepository::class);
            $datos = [
                'produccion_por_turno' => $repository->getProduccionPorTurno(30),
                'produccion_por_destino' => $repository->getProduccionPorDestino(30),
                'produccion_diaria' => $repository->getProduccionDiaria(30),
            ];
            return response()->json([
                'success' => true,
                'data' => $datos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener datos de gráficas para Registros Reproductivos
     */
    public function registrosReproductivos(Request $request): JsonResponse
    {
        try {
            $datos = $this->registroReproductivoService->getDatosGraficas();
            return response()->json([
                'success' => true,
                'data' => $datos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener datos de gráficas para Crías
     */
    public function crias(Request $request): JsonResponse
    {
        try {
            $datos = $this->criaService->getDatosGraficas();
            return response()->json([
                'success' => true,
                'data' => $datos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener datos de gráficas para Mortalidad
     */
    public function mortalidad(Request $request): JsonResponse
    {
        try {
            $datos = $this->mortalidadService->getDatosGraficas();
            return response()->json([
                'success' => true,
                'data' => $datos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener datos de gráficas para Salud
     */
    public function salud(Request $request): JsonResponse
    {
        try {
            $datos = $this->saludService->getDatosGraficas();
            return response()->json([
                'success' => true,
                'data' => $datos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener datos de gráficas para Inventario Bodega
     */
    public function inventarioBodega(Request $request): JsonResponse
    {
        try {
            $datos = $this->inventarioBodegaService->getDatosGraficas();
            return response()->json([
                'success' => true,
                'data' => $datos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ], 500);
        }
    }
}

