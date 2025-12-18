<?php

namespace App\Http\Controllers\Pasante;

use App\Http\Controllers\Controller;
use App\Http\Requests\UsoMedicamentoStoreRequest;
use App\Models\Vaca;
use App\Models\Personal;
use App\Models\Medicamento;
use App\Models\UsoMedicamento;
use App\Services\UsoMedicamentoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class UsoMedicamentoController extends Controller
{
    protected UsoMedicamentoService $usoMedicamentoService;

    public function __construct(UsoMedicamentoService $usoMedicamentoService)
    {
        $this->usoMedicamentoService = $usoMedicamentoService;
    }

    /**
     * Display a listing of the resource (solo los que creó el pasante).
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', UsoMedicamento::class);

        $filters = [
            'search' => $request->get('search'),
            'id_vaca' => $request->get('id_vaca'),
            'id_medicamento' => $request->get('id_medicamento'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
            'user_id' => auth()->id(), // Solo los que creó el pasante
        ];

        $usos = $this->usoMedicamentoService->getPaginated($filters, 15);

        // Datos para filtros (optimizado - solo campos necesarios)
        $vacas = Vaca::select('id_vaca', 'codigo')->orderBy('codigo')->get();
        $medicamentos = Medicamento::select('id_medicamento', 'nombre')->activos()->orderBy('nombre')->get();

        return view('pasante.uso_medicamentos.index', compact('usos', 'vacas', 'medicamentos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', UsoMedicamento::class);

        // Optimizado - solo campos necesarios
        $vacas = Vaca::select('id_vaca', 'codigo')->orderBy('codigo')->get();
        $medicamentos = Medicamento::select('id_medicamento', 'nombre')->activos()->orderBy('nombre')->get();
        $personal = Personal::select('id_personal', 'nombre')->orderBy('nombre')->get();
        
        return view('pasante.uso_medicamentos.create', compact('vacas', 'medicamentos', 'personal'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UsoMedicamentoStoreRequest $request)
    {
        Gate::authorize('create', UsoMedicamento::class);

        try {
            $uso = $this->usoMedicamentoService->create($request->validated());
            
            $mensaje = 'Uso de medicamento registrado exitosamente.';
            
            // Si se creó retiro automático, informar
            if ($uso->retiro) {
                $mensaje .= ' Se ha generado un retiro automático hasta el ' . $uso->retiro->fecha_fin->format('d/m/Y') . '.';
            }
            
            return redirect()->route('pasante.uso-medicamentos.index')
                ->with('success', $mensaje);
        } catch (\Exception $e) {
            Log::error('Error al crear uso de medicamento (Pasante): ' . $e->getMessage(), [
                'data' => $request->except(['evidencia_archivo']),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Error al registrar el uso: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $uso = $this->usoMedicamentoService->findWithRelations($id);
        
        if (!$uso) {
            return redirect()->route('pasante.uso-medicamentos.index')
                ->with('error', 'Uso de medicamento no encontrado.');
        }

        Gate::authorize('view', $uso);
        return view('pasante.uso_medicamentos.show', compact('uso'));
    }
}

