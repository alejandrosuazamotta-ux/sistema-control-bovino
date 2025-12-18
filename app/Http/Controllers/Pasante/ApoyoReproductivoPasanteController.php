<?php

namespace App\Http\Controllers\Pasante;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApoyoReproductivoPasanteStoreRequest;
use App\Http\Requests\ApoyoReproductivoPasanteUpdateRequest;
use App\Services\ApoyoReproductivoPasanteService;
use App\Models\Vaca;
use Illuminate\Http\Request;

class ApoyoReproductivoPasanteController extends Controller
{
    protected ApoyoReproductivoPasanteService $service;

    public function __construct(ApoyoReproductivoPasanteService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = [
            'user_id' => auth()->id(),
            'tipo_actividad' => $request->get('tipo_actividad'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
        ];

        $apoyos = $this->service->getPaginated($filters, 15);
        $estadisticas = $this->service->getEstadisticas(auth()->id());

        return view('pasante.apoyo-reproductivo.index', compact('apoyos', 'estadisticas'));
    }

    public function create()
    {
        $vacas = Vaca::orderBy('codigo')->get();
        return view('pasante.apoyo-reproductivo.create', compact('vacas'));
    }

    public function store(ApoyoReproductivoPasanteStoreRequest $request)
    {
        try {
            $this->service->create($request->validated());
            
            return redirect()->route('pasante.apoyo-reproductivo.index')
                ->with('success', 'Apoyo reproductivo registrado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al registrar el apoyo: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(string $id)
    {
        $apoyo = $this->service->findById($id);
        
        if (!$apoyo || ($apoyo->user_id !== auth()->id() && !auth()->user()->hasRole('Admin'))) {
            abort(403);
        }

        return view('pasante.apoyo-reproductivo.show', compact('apoyo'));
    }

    public function edit(string $id)
    {
        $apoyo = $this->service->findById($id);
        
        if (!$apoyo || $apoyo->user_id !== auth()->id()) {
            abort(403);
        }

        $vacas = Vaca::orderBy('codigo')->get();
        return view('pasante.apoyo-reproductivo.edit', compact('apoyo', 'vacas'));
    }

    public function update(ApoyoReproductivoPasanteUpdateRequest $request, string $id)
    {
        try {
            $apoyo = $this->service->findById($id);
            
            if (!$apoyo || $apoyo->user_id !== auth()->id()) {
                abort(403);
            }

            $this->service->update($apoyo, $request->validated());
            
            return redirect()->route('pasante.apoyo-reproductivo.index')
                ->with('success', 'Apoyo reproductivo actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el apoyo: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $apoyo = $this->service->findById($id);
            
            if (!$apoyo || ($apoyo->user_id !== auth()->id() && !auth()->user()->hasRole('Admin'))) {
                abort(403);
            }

            $this->service->delete($apoyo);
            
            return redirect()->route('pasante.apoyo-reproductivo.index')
                ->with('success', 'Apoyo reproductivo eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar el apoyo: ' . $e->getMessage());
        }
    }
}
