<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PersonalController;
use App\Http\Controllers\Admin\PotreroController;
use App\Http\Controllers\Admin\VacaController;
use App\Http\Controllers\Admin\CriaController;
use App\Http\Controllers\Admin\RegistroReproductivoController;
use App\Http\Controllers\Admin\ProduccionLecheraController;
use App\Http\Controllers\Admin\SaludController;
use App\Http\Controllers\Admin\AlimentacionController;
use App\Http\Controllers\Admin\AsignacionPotreroController;
use App\Http\Controllers\Admin\ReporteController;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Ruta post-login (decide a qué dashboard enviar según rol)
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Rutas Pasante
|--------------------------------------------------------------------------
*/
Route::prefix('pasante')
    ->name('pasante.')
    ->middleware(['auth', 'role:Pasante'])
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('pasante.dashboard'); // resources/views/pasante/dashboard.blade.php
        })->name('dashboard');
    });

/*
|--------------------------------------------------------------------------
| Rutas Admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:Admin'])
    ->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('personal', PersonalController::class);
        Route::resource('potreros', PotreroController::class);
        Route::resource('vacas', VacaController::class);
        Route::resource('crias', CriaController::class);
        Route::resource('registros-reproductivos', RegistroReproductivoController::class);
        Route::resource('produccion-lechera', ProduccionLecheraController::class);
        Route::resource('salud', SaludController::class);
        Route::resource('alimentacion', AlimentacionController::class);
        Route::resource('asignacion-potreros', AsignacionPotreroController::class);

        // Reportes
        Route::prefix('reportes')->name('reportes.')->group(function () {
            Route::get('/produccion', [ReporteController::class, 'produccion'])->name('produccion');
            Route::get('/reproduccion', [ReporteController::class, 'reproduccion'])->name('reproduccion');
            Route::get('/salud', [ReporteController::class, 'salud'])->name('salud');
        });
    });
