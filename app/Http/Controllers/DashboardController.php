<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Verificar si tiene rol de Admin usando Spatie
        if ($user->hasRole('Admin')) {
            return redirect()->route('admin.dashboard');
        }
        
        // Verificar si tiene rol de Pasante usando Spatie
        if ($user->hasRole('Pasante')) {
            return redirect()->route('pasante.dashboard');
        }

        // Si no tiene ningún rol específico, mostrar dashboard genérico
        return view('dashboard');
    }
}