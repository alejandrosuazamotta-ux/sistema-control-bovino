<?php

namespace App\Http\Controllers;

use App\Models\Alimentacion;
use App\Models\Vaca;
use App\Models\Personal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AlimentacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $alimentacion = Alimentacion::with(['vaca', 'personal'])->get();
        return response()->json([
            'success' => true,
            'data' => $alimentacion
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): JsonResponse
    {
        $vacas = Vaca::all();
        $personal = Personal::all();
        return response()->json([
            'success' => true,
            'message' => 'Formulario de creación de registro de alimentación',
            'vacas' => $vacas,
            'personal' => $personal
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'id_vaca' => 'required|exists:vacas,id_vaca',
            'fecha' => 'required|date|before_or_equal:today',
            'tipo_alimento' => 'required|in:Ensilaje,Pasto,Concentrado,Subproducto,Otro',
            'cantidad' => 'nullable|numeric|min:0|max:999.99',
            'observaciones' => 'nullable|string',
            'id_personal' => 'nullable|exists:personal,id_personal'
        ]);

        $alimentacion = Alimentacion::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Registro de alimentación creado exitosamente',
            'data' => $alimentacion
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $alimentacion = Alimentacion::with(['vaca', 'personal'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $alimentacion
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        $alimentacion = Alimentacion::findOrFail($id);
        $vacas = Vaca::all();
        $personal = Personal::all();
        
        return response()->json([
            'success' => true,
            'data' => $alimentacion,
            'vacas' => $vacas,
            'personal' => $personal
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'id_vaca' => 'required|exists:vacas,id_vaca',
            'fecha' => 'required|date|before_or_equal:today',
            'tipo_alimento' => 'required|in:Ensilaje,Pasto,Concentrado,Subproducto,Otro',
            'cantidad' => 'nullable|numeric|min:0|max:999.99',
            'observaciones' => 'nullable|string',
            'id_personal' => 'nullable|exists:personal,id_personal'
        ]);

        $alimentacion = Alimentacion::findOrFail($id);
        $alimentacion->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Registro de alimentación actualizado exitosamente',
            'data' => $alimentacion
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $alimentacion = Alimentacion::findOrFail($id);
        $alimentacion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Registro de alimentación eliminado exitosamente'
        ]);
    }
}
