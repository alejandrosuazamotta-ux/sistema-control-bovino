<?php

namespace App\Http\Controllers;

use App\Models\RegistroReproductivo;
use App\Models\Vaca;
use App\Models\Personal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RegistroReproductivoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $registros = RegistroReproductivo::with(['vaca', 'personal'])->get();
        return response()->json([
            'success' => true,
            'data' => $registros
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
            'message' => 'Formulario de creación de registro reproductivo',
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
            'tipo_evento' => 'required|in:Inseminación,Parto,Celo,Días abiertos',
            'fecha_evento' => 'required|date|before_or_equal:today',
            'observaciones' => 'nullable|string',
            'id_personal' => 'nullable|exists:personal,id_personal'
        ]);

        $registro = RegistroReproductivo::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Registro reproductivo creado exitosamente',
            'data' => $registro
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $registro = RegistroReproductivo::with(['vaca', 'personal'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $registro
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        $registro = RegistroReproductivo::findOrFail($id);
        $vacas = Vaca::all();
        $personal = Personal::all();
        
        return response()->json([
            'success' => true,
            'data' => $registro,
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
            'tipo_evento' => 'required|in:Inseminación,Parto,Celo,Días abiertos',
            'fecha_evento' => 'required|date|before_or_equal:today',
            'observaciones' => 'nullable|string',
            'id_personal' => 'nullable|exists:personal,id_personal'
        ]);

        $registro = RegistroReproductivo::findOrFail($id);
        $registro->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Registro reproductivo actualizado exitosamente',
            'data' => $registro
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $registro = RegistroReproductivo::findOrFail($id);
        $registro->delete();

        return response()->json([
            'success' => true,
            'message' => 'Registro reproductivo eliminado exitosamente'
        ]);
    }
}
