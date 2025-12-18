<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VacaStoreRequest;
use App\Http\Requests\VacaUpdateRequest;
use App\Models\Potrero;
use App\Models\Vaca;
use App\Services\VacaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VacaController extends Controller
{
    protected VacaService $vacaService;

    public function __construct(VacaService $vacaService)
    {
        $this->vacaService = $vacaService;
    }

    /**
     * Display a listing of the resource.
     * 
     * Autorización: Admin, Supervisor y Pasante pueden ver el listado
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Vaca::class);

        $filters = [
            'search' => $request->get('search'),
            'estado_salud' => $request->get('estado_salud'),
            'estado_reproductivo' => $request->get('estado_reproductivo'),
        ];

        $vacas = $this->vacaService->getPaginated($filters, 10);
        
        return view('admin.vacas.index', compact('vacas'));
    }

    /**
     * Show the form for creating a new resource.
     * 
     * Autorización: Solo Admin y Supervisor pueden crear vacas
     */
    public function create()
    {
        Gate::authorize('create', Vaca::class);

        $potreros = Potrero::all();
        return view('admin.vacas.create', compact('potreros'));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * Autorización: Solo Admin y Supervisor pueden crear vacas
     */
    public function store(VacaStoreRequest $request)
    {
        Gate::authorize('create', Vaca::class);

        try {
            $vaca = $this->vacaService->create($request->validated());
            
            return redirect()->route('admin.vacas.index')
                ->with('success', 'Vaca registrada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al registrar la vaca: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     * 
     * Autorización: Admin, Supervisor y Pasante pueden ver detalles
     */
    public function show(string $id)
    {
        $vaca = $this->vacaService->findWithRelations($id);
        
        if (!$vaca) {
            return redirect()->route('admin.vacas.index')
                ->with('error', 'Vaca no encontrada.');
        }

        Gate::authorize('view', $vaca);

        return view('admin.vacas.show', compact('vaca'));
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * Autorización: Solo Admin y Supervisor pueden editar vacas
     */
    public function edit(string $id)
    {
        $vaca = $this->vacaService->findById($id);
        
        if (!$vaca) {
            return redirect()->route('admin.vacas.index')
                ->with('error', 'Vaca no encontrada.');
        }

        Gate::authorize('update', $vaca);

        $potreros = Potrero::all();
        return view('admin.vacas.edit', compact('vaca', 'potreros'));
    }

    /**
     * Update the specified resource in storage.
     * 
     * Autorización: Solo Admin y Supervisor pueden actualizar vacas
     */
    public function update(VacaUpdateRequest $request, string $id)
    {
        $vaca = $this->vacaService->findById($id);
        
        if (!$vaca) {
            return redirect()->route('admin.vacas.index')
                ->with('error', 'Vaca no encontrada.');
        }

        Gate::authorize('update', $vaca);

        try {
            $vaca = $this->vacaService->update($vaca, $request->validated());
            
            return redirect()->route('admin.vacas.show', $vaca->id_vaca)
                ->with('success', 'Vaca actualizada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar la vaca: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     * 
     * Autorización: Solo Admin puede eliminar vacas
     */
    public function destroy(string $id)
    {
        $vaca = $this->vacaService->findById($id);
        
        if (!$vaca) {
            return redirect()->route('admin.vacas.index')
                ->with('error', 'Vaca no encontrada.');
        }

        Gate::authorize('delete', $vaca);

        try {
            $this->vacaService->delete($vaca);
            
            return redirect()->route('admin.vacas.index')
                ->with('success', 'Vaca eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar la vaca: ' . $e->getMessage());
        }
    }
}
