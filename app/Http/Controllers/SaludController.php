<?php

namespace App\Http\Controllers;

use App\Models\Salud;
use App\Models\Vaca;
use App\Models\Personal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SaludController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $salud = Salud::with(['vaca', 'personal'])->get();
        return response()->json([
            'success' => true,
            'data' => $salud
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
            'message' => 'Formulario de creación de registro de salud',
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
            'tipo_registro' => 'required|in:Vacunación,Tratamiento,Prueba mastitis,Otro',
            'fecha' => 'required|date|before_or_equal:today',
            'descripcion' => 'nullable|string',
            'resultado_prueba' => 'nullable|string|max:50',
            'id_personal' => 'nullable|exists:personal,id_personal'
        ]);

        $salud = Salud::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Registro de salud creado exitosamente',
            'data' => $salud
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $salud = Salud::with(['vaca', 'personal'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $salud
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        $salud = Salud::findOrFail($id);
        $vacas = Vaca::all();
        $personal = Personal::all();
        
        return response()->json([
            'success' => true,
            'data' => $salud,
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
            'tipo_registro' => 'required|in:Vacunación,Tratamiento,Prueba mastitis,Otro',
            'fecha' => 'required|date|before_or_equal:today',
            'descripcion' => 'nullable|string',
            'resultado_prueba' => 'nullable|string|max:50',
            'id_personal' => 'nullable|exists:personal,id_personal'
        ]);

        $salud = Salud::findOrFail($id);
        $salud->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Registro de salud actualizado exitosamente',
            'data' => $salud
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $salud = Salud::findOrFail($id);
        $salud->delete();

        return response()->json([
            'success' => true,
            'message' => 'Registro de salud eliminado exitosamente'
        ]);
    }
}
