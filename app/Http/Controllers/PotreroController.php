<?php

namespace App\Http\Controllers;

use App\Models\Potrero;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PotreroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $potreros = Potrero::all();
        return response()->json([
            'success' => true,
            'data' => $potreros
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Formulario de creación de potrero'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'ubicacion' => 'nullable|string|max:100',
            'capacidad' => 'required|integer|min:1'
        ]);

        $potrero = Potrero::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Potrero creado exitosamente',
            'data' => $potrero
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $potrero = Potrero::with('vacas')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $potrero
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        $potrero = Potrero::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $potrero
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'ubicacion' => 'nullable|string|max:100',
            'capacidad' => 'required|integer|min:1'
        ]);

        $potrero = Potrero::findOrFail($id);
        $potrero->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Potrero actualizado exitosamente',
            'data' => $potrero
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $potrero = Potrero::findOrFail($id);
        $potrero->delete();

        return response()->json([
            'success' => true,
            'message' => 'Potrero eliminado exitosamente'
        ]);
    }
}
