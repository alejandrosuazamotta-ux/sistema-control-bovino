<?php

namespace App\Http\Controllers\Pasante;

use App\Http\Controllers\Controller;
use App\Services\MedicamentoService;
use App\Models\Medicamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MedicamentoController extends Controller
{
    protected MedicamentoService $medicamentoService;

    public function __construct(MedicamentoService $medicamentoService)
    {
        $this->medicamentoService = $medicamentoService;
    }

    /**
     * Display a listing of the resource (solo lectura para pasante).
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Medicamento::class);

        $filters = [
            'search' => $request->get('search'),
            'tipo' => $request->get('tipo'),
            'activo' => $request->get('activo', true),
        ];

        $medicamentos = $this->medicamentoService->getPaginated($filters, 15);

        // Datos para gráficas
        $datosGraficas = $this->medicamentoService->getDatosGraficas();

        return view('pasante.medicamentos.index', compact('medicamentos', 'datosGraficas'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $medicamento = $this->medicamentoService->findById($id);
        
        if (!$medicamento) {
            return redirect()->route('pasante.medicamentos.index')
                ->with('error', 'Medicamento no encontrado.');
        }

        Gate::authorize('view', $medicamento);
        return view('pasante.medicamentos.show', compact('medicamento'));
    }
}

