<?php

namespace App\Http\Controllers;

use App\Models\Vaca;
use App\Models\Potrero;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VacaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $vacas = Vaca::with('potrero')->get();
        return response()->json([
            'success' => true,
            'data' => $vacas
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): JsonResponse
    {
        $potreros = Potrero::all();
        return response()->json([
            'success' => true,
            'message' => 'Formulario de creación de vaca',
            'potreros' => $potreros
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'codigo' => 'required|string|max:20|unique:vacas,codigo',
            'fecha_nacimiento' => 'nullable|date|before_or_equal:today',
            'raza' => 'nullable|string|max:50',
            'estado_salud' => 'required|in:Sana,En tratamiento,En observación',
            'estado_reproductivo' => 'required|in:Celo,Preñada,Lactancia,Descanso',
            'id_potrero' => 'nullable|exists:potreros,id_potrero'
        ]);

        // Verificar capacidad del potrero si se asigna
        if ($request->filled('id_potrero')) {
            $potrero = Potrero::findOrFail($request->id_potrero);
            $ocupacion = $potrero->vacas->count();
            
            if ($ocupacion >= $potrero->capacidad) {
                return response()->json([
                    'success' => false,
                    'message' => 'El potrero seleccionado está lleno.',
                    'errors' => ['id_potrero' => ['El potrero seleccionado está lleno.']]
                ], 422);
            }
        }

        $vaca = Vaca::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Vaca creada exitosamente',
            'data' => $vaca
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $vaca = Vaca::with(['potrero', 'crias', 'registrosReproductivos', 'produccionLechera', 'salud', 'alimentacion'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $vaca
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        $vaca = Vaca::findOrFail($id);
        $potreros = Potrero::all();
        
        return response()->json([
            'success' => true,
            'data' => $vaca,
            'potreros' => $potreros
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'codigo' => 'required|string|max:20|unique:vacas,codigo,' . $id . ',id_vaca',
            'fecha_nacimiento' => 'nullable|date|before_or_equal:today',
            'raza' => 'nullable|string|max:50',
            'estado_salud' => 'required|in:Sana,En tratamiento,En observación',
            'estado_reproductivo' => 'required|in:Celo,Preñada,Lactancia,Descanso',
            'id_potrero' => 'nullable|exists:potreros,id_potrero'
        ]);

        $vaca = Vaca::findOrFail($id);

        // Verificar capacidad del potrero si se cambia
        if ($request->filled('id_potrero') && $request->id_potrero != $vaca->id_potrero) {
            $potrero = Potrero::findOrFail($request->id_potrero);
            $ocupacion = $potrero->vacas->count();
            
            if ($ocupacion >= $potrero->capacidad) {
                return response()->json([
                    'success' => false,
                    'message' => 'El potrero seleccionado está lleno.',
                    'errors' => ['id_potrero' => ['El potrero seleccionado está lleno.']]
                ], 422);
            }
        }

        $vaca->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Vaca actualizada exitosamente',
            'data' => $vaca
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $vaca = Vaca::findOrFail($id);
        $vaca->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vaca eliminada exitosamente'
        ]);
    }
}
