<?php

namespace App\Http\Controllers\Pasante;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApoyoOrdeñoPasanteStoreRequest;
use App\Http\Requests\ApoyoOrdeñoPasanteUpdateRequest;
use App\Services\ApoyoOrdeñoPasanteService;
use App\Models\Vaca;
use Illuminate\Http\Request;

class ApoyoOrdeñoPasanteController extends Controller
{
    protected ApoyoOrdeñoPasanteService $service;

    public function __construct(ApoyoOrdeñoPasanteService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = [
            'user_id' => auth()->id(),
            'turno' => $request->get('turno'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
        ];

        $apoyos = $this->service->getPaginated($filters, 15);
        $estadisticas = $this->service->getEstadisticas(auth()->id());

        return view('pasante.apoyo-ordeno.index', compact('apoyos', 'estadisticas'));
    }

    public function create()
    {
        $vacas = Vaca::orderBy('codigo')->get();
        return view('pasante.apoyo-ordeno.create', compact('vacas'));
    }

    public function store(ApoyoOrdeñoPasanteStoreRequest $request)
    {
        try {
            $this->service->create($request->validated());
            
            return redirect()->route('pasante.apoyo-ordeno.index')
                ->with('success', 'Apoyo de ordeño registrado exitosamente.');
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

        return view('pasante.apoyo-ordeno.show', compact('apoyo'));
    }

    public function edit(string $id)
    {
        $apoyo = $this->service->findById($id);
        
        if (!$apoyo || $apoyo->user_id !== auth()->id()) {
            abort(403);
        }

        $vacas = Vaca::orderBy('codigo')->get();
        return view('pasante.apoyo-ordeno.edit', compact('apoyo', 'vacas'));
    }

    public function update(ApoyoOrdeñoPasanteUpdateRequest $request, string $id)
    {
        try {
            $apoyo = $this->service->findById($id);
            
            if (!$apoyo || $apoyo->user_id !== auth()->id()) {
                abort(403);
            }

            $this->service->update($apoyo, $request->validated());
            
            return redirect()->route('pasante.apoyo-ordeno.index')
                ->with('success', 'Apoyo de ordeño actualizado exitosamente.');
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
            
            return redirect()->route('pasante.apoyo-ordeno.index')
                ->with('success', 'Apoyo de ordeño eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar el apoyo: ' . $e->getMessage());
        }
    }
}

