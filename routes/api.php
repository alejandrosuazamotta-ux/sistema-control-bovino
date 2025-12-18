<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas adicionales para reportes y estadísticas
Route::prefix('reportes')->group(function () {
    Route::get('/produccion-diaria', function () {
        return response()->json([
            'success' => true,
            'message' => 'Reporte de producción diaria'
        ]);
    });
    
    Route::get('/estado-reproductivo', function () {
        return response()->json([
            'success' => true,
            'message' => 'Reporte de estado reproductivo'
        ]);
    });
    
    Route::get('/salud-general', function () {
        return response()->json([
            'success' => true,
            'message' => 'Reporte de salud general'
        ]);
    });
});

// Rutas API para gráficas (requieren autenticación)
Route::middleware('auth:sanctum')->prefix('graficas')->group(function () {
    Route::get('/alimentacion', [\App\Http\Controllers\Api\GraficasController::class, 'alimentacion']);
    Route::get('/medicamentos', [\App\Http\Controllers\Api\GraficasController::class, 'medicamentos']);
    Route::get('/uso-medicamentos', [\App\Http\Controllers\Api\GraficasController::class, 'usoMedicamentos']);
    Route::get('/retiros', [\App\Http\Controllers\Api\GraficasController::class, 'retiros']);
    Route::get('/potreros', [\App\Http\Controllers\Api\GraficasController::class, 'potreros']);
    Route::get('/asignacion-potreros', [\App\Http\Controllers\Api\GraficasController::class, 'asignacionPotreros']);
    Route::get('/produccion-lechera', [\App\Http\Controllers\Api\GraficasController::class, 'produccionLechera']);
    Route::get('/registros-reproductivos', [\App\Http\Controllers\Api\GraficasController::class, 'registrosReproductivos']);
    Route::get('/crias', [\App\Http\Controllers\Api\GraficasController::class, 'crias']);
    Route::get('/mortalidad', [\App\Http\Controllers\Api\GraficasController::class, 'mortalidad']);
    Route::get('/salud', [\App\Http\Controllers\Api\GraficasController::class, 'salud']);
    Route::get('/inventario-bodega', [\App\Http\Controllers\Api\GraficasController::class, 'inventarioBodega']);
});
