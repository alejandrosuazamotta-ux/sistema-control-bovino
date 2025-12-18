<?php

namespace App\Http\Controllers\Pasante;

use App\Http\Controllers\Controller;
use App\Http\Requests\PruebaSanitariaStoreRequest;
use App\Http\Requests\PruebaSanitariaUpdateRequest;
use App\Models\Vaca;
use App\Models\Personal;
use App\Models\PruebaSanitaria;
use App\Services\PruebaSanitariaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PruebaSanitariaController extends Controller
{
    protected PruebaSanitariaService $pruebaSanitariaService;

    public function __construct(PruebaSanitariaService $pruebaSanitariaService)
    {
        $this->pruebaSanitariaService = $pruebaSanitariaService;
    }

    /**
     * Display a listing of the resource (solo las que creó el pasante).
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', PruebaSanitaria::class);

        $filters = [
            'search' => $request->get('search'),
            'tipo_prueba' => $request->get('tipo_prueba'),
            'resultado' => $request->get('resultado'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
            'id_vaca' => $request->get('id_vaca'),
            'user_id' => auth()->id(), // Solo las que creó el pasante
        ];

        $pruebas = $this->pruebaSanitariaService->getPaginated($filters, 15);
        $estadisticas = $this->pruebaSanitariaService->getEstadisticas();
        $datosGraficas = $this->pruebaSanitariaService->getDatosGraficas();

        // Datos para los filtros
        $vacas = Vaca::select('id_vaca', 'codigo')->orderBy('codigo')->get();

        return view('pasante.pruebas_sanitarias.index', compact(
            'pruebas',
            'estadisticas',
            'datosGraficas',
            'vacas'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', PruebaSanitaria::class);

        $vacas = Vaca::orderBy('codigo')->get();
        $personal = Personal::orderBy('nombre')->get();

        return view('pasante.pruebas_sanitarias.create', compact('vacas', 'personal'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PruebaSanitariaStoreRequest $request)
    {
        Gate::authorize('create', PruebaSanitaria::class);

        try {
            $prueba = $this->pruebaSanitariaService->create($request->validated());

            return redirect()->route('pasante.pruebas-sanitarias.index')
                ->with('success', 'Prueba sanitaria registrada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al crear prueba sanitaria (Pasante): ' . $e->getMessage(), [
                'data' => $request->except(['evidencia_archivo']),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Error al crear la prueba: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $prueba = $this->pruebaSanitariaService->findWithRelations($id);

        if (!$prueba) {
            return redirect()->route('pasante.pruebas-sanitarias.index')
                ->with('error', 'Prueba sanitaria no encontrada.');
        }

        Gate::authorize('view', $prueba);

        return view('pasante.pruebas_sanitarias.show', compact('prueba'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $prueba = $this->pruebaSanitariaService->findById($id);

        if (!$prueba) {
            return redirect()->route('pasante.pruebas-sanitarias.index')
                ->with('error', 'Prueba sanitaria no encontrada.');
        }

        Gate::authorize('update', $prueba);

        $vacas = Vaca::orderBy('codigo')->get();

        return view('pasante.pruebas_sanitarias.edit', compact('prueba', 'vacas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PruebaSanitariaUpdateRequest $request, string $id)
    {
        $prueba = $this->pruebaSanitariaService->findById($id);

        if (!$prueba) {
            return redirect()->route('pasante.pruebas-sanitarias.index')
                ->with('error', 'Prueba sanitaria no encontrada.');
        }

        Gate::authorize('update', $prueba);

        try {
            $prueba = $this->pruebaSanitariaService->update($prueba, $request->validated());

            return redirect()->route('pasante.pruebas-sanitarias.show', $prueba->id_prueba)
                ->with('success', 'Prueba sanitaria actualizada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar prueba sanitaria (Pasante): ' . $e->getMessage(), [
                'prueba_id' => $id,
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Error al actualizar la prueba: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Descargar evidencia de prueba sanitaria
     */
    public function descargarEvidencia(string $id)
    {
        $prueba = $this->pruebaSanitariaService->findById($id);

        if (!$prueba || !$prueba->evidencia_archivo) {
            return redirect()->back()
                ->with('error', 'No se encontró evidencia para esta prueba.');
        }

        Gate::authorize('view', $prueba);

        $rutaArchivo = 'public/' . $prueba->evidencia_archivo;

        if (!Storage::exists($rutaArchivo)) {
            return redirect()->back()
                ->with('error', 'El archivo de evidencia no existe.');
        }

        return Storage::download($rutaArchivo);
    }
}

