<?php

namespace App\Http\Controllers\Pasante;

use App\Http\Controllers\Controller;
use App\Http\Requests\MortalidadStoreRequest;
use App\Models\Vaca;
use App\Models\Cria;
use App\Models\Mortalidad;
use App\Services\MortalidadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class MortalidadController extends Controller
{
    protected MortalidadService $mortalidadService;

    public function __construct(MortalidadService $mortalidadService)
    {
        $this->mortalidadService = $mortalidadService;
    }

    /**
     * Display a listing of the resource (solo lectura para pasante).
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Mortalidad::class);

        $filters = [
            'search' => $request->get('search'),
            'animal_type' => $request->get('animal_type'),
            'clasificacion' => $request->get('clasificacion'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
        ];

        $mortalidades = $this->mortalidadService->getPaginated($filters, 15);
        $estadisticas = $this->mortalidadService->getEstadisticas();
        $datosGraficas = $this->mortalidadService->getDatosGraficas();
        
        return view('pasante.mortalidad.index', compact('mortalidades', 'estadisticas', 'datosGraficas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Mortalidad::class);

        // Obtener vacas que no tienen registro de mortalidad (optimizado)
        $vacas = Vaca::select('id_vaca', 'codigo')
            ->whereDoesntHave('mortalidad')
            ->get();
        
        // Obtener crías que no tienen registro de mortalidad (optimizado)
        $crias = Cria::select('id_cria', 'nombre_cria', 'sinigan')
            ->whereDoesntHave('mortalidad')
            ->get();
        
        return view('pasante.mortalidad.create', compact('vacas', 'crias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MortalidadStoreRequest $request)
    {
        Gate::authorize('create', Mortalidad::class);

        try {
            $mortalidad = $this->mortalidadService->create($request->validated());
            
            return redirect()
                ->route('pasante.mortalidad.index')
                ->with('success', 'Registro de mortalidad creado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al crear registro de mortalidad (Pasante): ' . $e->getMessage(), [
                'data' => $request->except(['acta_archivo']),
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el registro: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mortalidad = $this->mortalidadService->findWithRelations($id);
        
        if (!$mortalidad) {
            return redirect()
                ->route('pasante.mortalidad.index')
                ->with('error', 'Registro de mortalidad no encontrado.');
        }
        
        Gate::authorize('view', $mortalidad);
        
        return view('pasante.mortalidad.show', compact('mortalidad'));
    }
}

