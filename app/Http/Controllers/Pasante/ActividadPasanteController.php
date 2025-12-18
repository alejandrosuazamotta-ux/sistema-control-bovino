<?php

namespace App\Http\Controllers\Pasante;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActividadPasanteStoreRequest;
use App\Http\Requests\ActividadPasanteUpdateRequest;
use App\Services\ActividadPasanteService;
use Illuminate\Http\Request;

class ActividadPasanteController extends Controller
{
    protected ActividadPasanteService $service;

    public function __construct(ActividadPasanteService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = [
            'user_id' => auth()->id(),
            'tipo_actividad' => $request->get('tipo_actividad'),
            'estado' => $request->get('estado'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
        ];

        $actividades = $this->service->getPaginated($filters, 15);
        $estadisticas = $this->service->getEstadisticas(auth()->id());

        return view('pasante.actividades.index', compact('actividades', 'estadisticas'));
    }

    public function create()
    {
        return view('pasante.actividades.create');
    }

    public function store(ActividadPasanteStoreRequest $request)
    {
        try {
            $this->service->create($request->validated());
            
            return redirect()->route('pasante.actividades.index')
                ->with('success', 'Actividad registrada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al registrar la actividad: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(string $id)
    {
        $actividad = $this->service->findById($id);
        
        if (!$actividad || $actividad->user_id !== auth()->id()) {
            abort(403);
        }

        return view('pasante.actividades.show', compact('actividad'));
    }

    public function edit(string $id)
    {
        $actividad = $this->service->findById($id);
        
        if (!$actividad || $actividad->user_id !== auth()->id()) {
            abort(403);
        }

        return view('pasante.actividades.edit', compact('actividad'));
    }

    public function update(ActividadPasanteUpdateRequest $request, string $id)
    {
        try {
            $actividad = $this->service->findById($id);
            
            if (!$actividad || $actividad->user_id !== auth()->id()) {
                abort(403);
            }

            $this->service->update($actividad, $request->validated());
            
            return redirect()->route('pasante.actividades.index')
                ->with('success', 'Actividad actualizada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar la actividad: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $actividad = $this->service->findById($id);
            
            if (!$actividad || $actividad->user_id !== auth()->id()) {
                abort(403);
            }

            $this->service->delete($actividad);
            
            return redirect()->route('pasante.actividades.index')
                ->with('success', 'Actividad eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar la actividad: ' . $e->getMessage());
        }
    }
}
