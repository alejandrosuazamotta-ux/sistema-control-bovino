<?php

namespace App\Http\Controllers\Pasante;

use App\Http\Controllers\Controller;
use App\Http\Requests\RotacionPotrerosPasanteStoreRequest;
use App\Http\Requests\RotacionPotrerosPasanteUpdateRequest;
use App\Services\RotacionPotrerosPasanteService;
use App\Models\Vaca;
use App\Models\Potrero;
use Illuminate\Http\Request;

class RotacionPotrerosPasanteController extends Controller
{
    protected RotacionPotrerosPasanteService $service;

    public function __construct(RotacionPotrerosPasanteService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = [
            'user_id' => auth()->id(),
            'id_potrero_origen' => $request->get('id_potrero_origen'),
            'id_potrero_destino' => $request->get('id_potrero_destino'),
            'fecha_inicio' => $request->get('fecha_inicio'),
            'fecha_fin' => $request->get('fecha_fin'),
        ];

        $rotaciones = $this->service->getPaginated($filters, 15);
        $estadisticas = $this->service->getEstadisticas(auth()->id());

        return view('pasante.rotacion-potreros.index', compact('rotaciones', 'estadisticas'));
    }

    public function create()
    {
        $vacas = Vaca::orderBy('codigo')->get();
        $potreros = Potrero::orderBy('nombre')->get();
        return view('pasante.rotacion-potreros.create', compact('vacas', 'potreros'));
    }

    public function store(RotacionPotrerosPasanteStoreRequest $request)
    {
        try {
            $this->service->create($request->validated());
            
            return redirect()->route('pasante.rotacion-potreros.index')
                ->with('success', 'Rotación de potreros registrada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al registrar la rotación: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(string $id)
    {
        $rotacion = $this->service->findById($id);
        
        if (!$rotacion || ($rotacion->user_id !== auth()->id() && !auth()->user()->hasRole('Admin'))) {
            abort(403);
        }

        return view('pasante.rotacion-potreros.show', compact('rotacion'));
    }

    public function edit(string $id)
    {
        $rotacion = $this->service->findById($id);
        
        if (!$rotacion || $rotacion->user_id !== auth()->id()) {
            abort(403);
        }

        $vacas = Vaca::orderBy('codigo')->get();
        $potreros = Potrero::orderBy('nombre')->get();
        return view('pasante.rotacion-potreros.edit', compact('rotacion', 'vacas', 'potreros'));
    }

    public function update(RotacionPotrerosPasanteUpdateRequest $request, string $id)
    {
        try {
            $rotacion = $this->service->findById($id);
            
            if (!$rotacion || $rotacion->user_id !== auth()->id()) {
                abort(403);
            }

            $this->service->update($rotacion, $request->validated());
            
            return redirect()->route('pasante.rotacion-potreros.index')
                ->with('success', 'Rotación de potreros actualizada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar la rotación: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $rotacion = $this->service->findById($id);
            
            if (!$rotacion || ($rotacion->user_id !== auth()->id() && !auth()->user()->hasRole('Admin'))) {
                abort(403);
            }

            $this->service->delete($rotacion);
            
            return redirect()->route('pasante.rotacion-potreros.index')
                ->with('success', 'Rotación de potreros eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar la rotación: ' . $e->getMessage());
        }
    }
}
