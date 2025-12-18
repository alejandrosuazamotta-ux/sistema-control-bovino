<?php

namespace App\Http\Controllers\Pasante;

use App\Http\Controllers\Controller;
use App\Http\Requests\TareaPasanteStoreRequest;
use App\Http\Requests\TareaPasanteUpdateRequest;
use App\Services\TareaPasanteService;
use App\Models\User;
use Illuminate\Http\Request;

class TareaPasanteController extends Controller
{
    protected TareaPasanteService $service;

    public function __construct(TareaPasanteService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = [
            'user_id' => auth()->id(),
            'prioridad' => $request->get('prioridad'),
            'estado' => $request->get('estado'),
        ];

        $tareas = $this->service->getPaginated($filters, 15);
        $estadisticas = $this->service->getEstadisticas(auth()->id());
        $vencidas = $this->service->getPaginated(['user_id' => auth()->id(), 'vencidas' => true], 5);

        return view('pasante.tareas.index', compact('tareas', 'estadisticas', 'vencidas'));
    }

    public function create()
    {
        $pasantes = User::role('Pasante')->get();
        return view('pasante.tareas.create', compact('pasantes'));
    }

    public function store(TareaPasanteStoreRequest $request)
    {
        try {
            $this->service->create($request->validated());
            
            return redirect()->route('pasante.tareas.index')
                ->with('success', 'Tarea asignada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al asignar la tarea: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(string $id)
    {
        $tarea = $this->service->findById($id);
        
        if (!$tarea || ($tarea->user_id !== auth()->id() && !auth()->user()->hasRole('Admin'))) {
            abort(403);
        }

        return view('pasante.tareas.show', compact('tarea'));
    }

    public function edit(string $id)
    {
        $tarea = $this->service->findById($id);
        
        if (!$tarea || $tarea->user_id !== auth()->id()) {
            abort(403);
        }

        return view('pasante.tareas.edit', compact('tarea'));
    }

    public function update(TareaPasanteUpdateRequest $request, string $id)
    {
        try {
            $tarea = $this->service->findById($id);
            
            if (!$tarea || $tarea->user_id !== auth()->id()) {
                abort(403);
            }

            $this->service->update($tarea, $request->validated());
            
            return redirect()->route('pasante.tareas.index')
                ->with('success', 'Tarea actualizada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar la tarea: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $tarea = $this->service->findById($id);
            
            if (!$tarea || ($tarea->user_id !== auth()->id() && !auth()->user()->hasRole('Admin'))) {
                abort(403);
            }

            $this->service->delete($tarea);
            
            return redirect()->route('pasante.tareas.index')
                ->with('success', 'Tarea eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar la tarea: ' . $e->getMessage());
        }
    }
}
