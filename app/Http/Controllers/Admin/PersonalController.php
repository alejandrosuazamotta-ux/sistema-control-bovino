<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PersonalStoreRequest;
use App\Http\Requests\PersonalUpdateRequest;
use App\Models\Personal;
use App\Services\PersonalService;
use Illuminate\Http\Request;

class PersonalController extends Controller
{
    protected PersonalService $personalService;

    public function __construct(PersonalService $personalService)
    {
        $this->personalService = $personalService;
    }

    public function index()
    {
        $personal = $this->personalService->getPaginated(10);
        return view('admin.personal.index', compact('personal'));
    }

    public function create()
    {
        return view('admin.personal.create');
    }

    public function store(PersonalStoreRequest $request)
    {
        try {
            $this->personalService->create($request->validated());
            
            return redirect()->route('admin.personal.index')
                ->with('success', 'Personal registrado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al registrar el personal: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(string $id)
    {
        $personal = $this->personalService->findWithUser($id);
        
        if (!$personal) {
            abort(404);
        }
        
        return view('admin.personal.show', compact('personal'));
    }

    public function edit(string $id)
    {
        $personal = $this->personalService->findWithUser($id);
        
        if (!$personal) {
            abort(404);
        }
        
        return view('admin.personal.edit', compact('personal'));
    }

    public function update(PersonalUpdateRequest $request, string $id)
    {
        try {
            $personal = $this->personalService->findWithUser($id);
            
            if (!$personal) {
                abort(404);
            }
            
            $this->personalService->update($personal, $request->validated());
            
            return redirect()->route('admin.personal.index')
                ->with('success', 'Personal actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el personal: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $personal = $this->personalService->findWithUser($id);
            
            if (!$personal) {
                abort(404);
            }
            
            $this->personalService->delete($personal);
            
            return redirect()->route('admin.personal.index')
                ->with('success', 'Personal eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar el personal: ' . $e->getMessage());
        }
    }
}
