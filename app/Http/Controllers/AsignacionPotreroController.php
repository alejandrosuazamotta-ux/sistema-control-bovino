<?php

namespace App\Http\Controllers;

use App\Models\AsignacionPotrero;
use App\Models\Potrero;
use App\Models\Vaca;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AsignacionPotreroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $asignaciones = AsignacionPotrero::with(['potrero', 'vaca'])->get();
        return response()->json([
            'success' => true,
            'data' => $asignaciones
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): JsonResponse
    {
        $potreros = Potrero::all();
        $vacas = Vaca::all();
        return response()->json([
            'success' => true,
            'message' => 'Formulario de creación de asignación de potrero',
            'potreros' => $potreros,
            'vacas' => $vacas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'id_potrero' => 'required|exists:potreros,id_potrero',
            'id_vaca' => 'required|exists:vacas,id_vaca',
            'fecha_asignacion' => 'required|date|before_or_equal:today'
        ]);

        // Verificar capacidad del potrero
        $potrero = Potrero::findOrFail($request->id_potrero);
        $ocupacion = $potrero->vacas->count();
        
        if ($ocupacion >= $potrero->capacidad) {
            return response()->json([
                'success' => false,
                'message' => 'El potrero seleccionado está lleno.',
                'errors' => ['id_potrero' => ['El potrero seleccionado está lleno.']]
            ], 422);
        }

        // Verificar que no exista una asignación duplicada
        $existeAsignacion = AsignacionPotrero::where('id_vaca', $request->id_vaca)
            ->where('id_potrero', $request->id_potrero)
            ->where('fecha_asignacion', $request->fecha_asignacion)
            ->exists();

        if ($existeAsignacion) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe una asignación para esta vaca en este potrero en la fecha seleccionada.',
                'errors' => ['fecha_asignacion' => ['Ya existe una asignación para esta vaca en este potrero en la fecha seleccionada.']]
            ], 422);
        }

        // Actualizar el potrero de la vaca
        $vaca = Vaca::findOrFail($request->id_vaca);
        $vaca->id_potrero = $request->id_potrero;
        $vaca->save();

        $asignacion = AsignacionPotrero::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Asignación de potrero creada exitosamente',
            'data' => $asignacion
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $asignacion = AsignacionPotrero::with(['potrero', 'vaca'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $asignacion
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        $asignacion = AsignacionPotrero::findOrFail($id);
        $potreros = Potrero::all();
        $vacas = Vaca::all();
        
        return response()->json([
            'success' => true,
            'data' => $asignacion,
            'potreros' => $potreros,
            'vacas' => $vacas
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'id_potrero' => 'required|exists:potreros,id_potrero',
            'id_vaca' => 'required|exists:vacas,id_vaca',
            'fecha_asignacion' => 'required|date|before_or_equal:today'
        ]);

        $asignacion = AsignacionPotrero::findOrFail($id);

        // Verificar capacidad del potrero si se cambia
        if ($request->id_potrero != $asignacion->id_potrero) {
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

        // Actualizar el potrero de la vaca
        $vaca = Vaca::findOrFail($request->id_vaca);
        $vaca->id_potrero = $request->id_potrero;
        $vaca->save();

        $asignacion->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Asignación de potrero actualizada exitosamente',
            'data' => $asignacion
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $asignacion = AsignacionPotrero::findOrFail($id);
        
        // Remover la asignación del potrero de la vaca
        $vaca = $asignacion->vaca;
        if ($vaca && $vaca->id_potrero == $asignacion->id_potrero) {
            $vaca->id_potrero = null;
            $vaca->save();
        }
        
        $asignacion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Asignación de potrero eliminada exitosamente'
        ]);
    }
}
