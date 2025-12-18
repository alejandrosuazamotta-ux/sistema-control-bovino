<?php

namespace App\Http\Controllers;

use App\Models\ProduccionLechera;
use App\Models\Vaca;
use App\Models\Personal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * API Controller para Producción Lechera
 * 
 * ⚠️ NOTA: Este controller está desactualizado y no incluye los nuevos campos:
 * - turno (AM/PM)
 * - destino (Agroindustria, Lechero, Particular, Consumo)
 * - valor_unidad
 * - valor_total
 * - excluida_por_retiro
 * 
 * Para usar la funcionalidad completa, usar:
 * - Admin\ProduccionLecheraController (web)
 * - O actualizar este controller para incluir los nuevos campos
 * 
 * @deprecated Este controller necesita actualización para incluir los nuevos campos
 */
class ProduccionLecheraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $produccion = ProduccionLechera::with(['vaca', 'personal'])->sinRetiro()->get();
        return response()->json([
            'success' => true,
            'data' => $produccion
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
            'message' => 'Formulario de creación de producción lechera',
            'vacas' => $vacas,
            'personal' => $personal
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * 
     * ⚠️ ACTUALIZAR: Agregar validación de turno, destino, valor_unidad
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'id_vaca' => 'required|exists:vacas,id_vaca',
            'fecha' => 'required|date|before_or_equal:today',
            'turno' => 'required|in:AM,PM',
            'cantidad_leche' => 'required|numeric|min:0.01|max:999.99',
            'destino' => 'required|in:Agroindustria,Lechero,Particular,Consumo',
            'valor_unidad' => 'nullable|numeric|min:0|max:999999.99',
            'id_personal' => 'required|exists:personal,id_personal'
        ]);

        $produccion = ProduccionLechera::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Producción lechera creada exitosamente',
            'data' => $produccion
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $produccion = ProduccionLechera::with(['vaca', 'personal'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $produccion
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        $produccion = ProduccionLechera::findOrFail($id);
        $vacas = Vaca::all();
        $personal = Personal::all();
        
        return response()->json([
            'success' => true,
            'data' => $produccion,
            'vacas' => $vacas,
            'personal' => $personal
        ]);
    }

    /**
     * Update the specified resource in storage.
     * 
     * ⚠️ ACTUALIZAR: Agregar validación de turno, destino, valor_unidad
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'id_vaca' => 'required|exists:vacas,id_vaca',
            'fecha' => 'required|date|before_or_equal:today',
            'turno' => 'required|in:AM,PM',
            'cantidad_leche' => 'required|numeric|min:0.01|max:999.99',
            'destino' => 'required|in:Agroindustria,Lechero,Particular,Consumo',
            'valor_unidad' => 'nullable|numeric|min:0|max:999999.99',
            'id_personal' => 'required|exists:personal,id_personal'
        ]);

        $produccion = ProduccionLechera::findOrFail($id);
        $produccion->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Producción lechera actualizada exitosamente',
            'data' => $produccion
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $produccion = ProduccionLechera::findOrFail($id);
        $produccion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producción lechera eliminada exitosamente'
        ]);
    }
}
