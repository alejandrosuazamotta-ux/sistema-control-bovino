<?php

namespace App\Http\Controllers;

use App\Models\Cria;
use App\Models\Vaca;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $crias = Cria::with('vacaMadre')->get();
        return response()->json([
            'success' => true,
            'data' => $crias
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): JsonResponse
    {
        $vacas = Vaca::all();
        return response()->json([
            'success' => true,
            'message' => 'Formulario de creación de cría',
            'vacas' => $vacas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'id_vaca_madre' => 'required|exists:vacas,id_vaca',
            'fecha_nacimiento' => 'required|date',
            'peso' => 'nullable|numeric|min:0',
            'estado_destete' => 'required|in:No destetada,Destetada'
        ]);

        $cria = Cria::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Cría creada exitosamente',
            'data' => $cria
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $cria = Cria::with('vacaMadre')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $cria
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        $cria = Cria::findOrFail($id);
        $vacas = Vaca::all();
        
        return response()->json([
            'success' => true,
            'data' => $cria,
            'vacas' => $vacas
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'id_vaca_madre' => 'required|exists:vacas,id_vaca',
            'fecha_nacimiento' => 'required|date',
            'peso' => 'nullable|numeric|min:0',
            'estado_destete' => 'required|in:No destetada,Destetada'
        ]);

        $cria = Cria::findOrFail($id);
        $cria->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Cría actualizada exitosamente',
            'data' => $cria
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $cria = Cria::findOrFail($id);
        $cria->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cría eliminada exitosamente'
        ]);
    }
}
