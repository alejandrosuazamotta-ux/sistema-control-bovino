<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReportePolicy
{
    /**
     * Determine whether the user can view any reports.
     * 
     * SEGURIDAD: Admin siempre tiene acceso total.
     * Pasante tiene acceso de lectura.
     */
    public function viewAny(User $user): bool
    {
        // Admin siempre tiene acceso
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        return $user->hasRole('Supervisor') || $user->hasRole('Pasante');
    }

    /**
     * Determine whether the user can view production report.
     * 
     * SEGURIDAD: Admin siempre tiene acceso total.
     * Pasante tiene acceso de lectura.
     */
    public function viewProduccion(User $user): bool
    {
        // Admin siempre tiene acceso
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        return $user->hasRole('Supervisor') || $user->hasRole('Pasante');
    }

    /**
     * Determine whether the user can view reproductivo report.
     * 
     * SEGURIDAD: Admin siempre tiene acceso total.
     * Pasante tiene acceso de lectura.
     */
    public function viewReproductivo(User $user): bool
    {
        // Admin siempre tiene acceso
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        return $user->hasRole('Supervisor') || $user->hasRole('Pasante');
    }

    /**
     * Determine whether the user can view sanitario report.
     * 
     * SEGURIDAD: Admin siempre tiene acceso total.
     * Pasante tiene acceso de lectura.
     */
    public function viewSanitario(User $user): bool
    {
        // Admin siempre tiene acceso
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        return $user->hasRole('Supervisor') || $user->hasRole('Pasante');
    }

    /**
     * Determine whether the user can view mortalidad report.
     * 
     * SEGURIDAD: Admin siempre tiene acceso total.
     * Pasante tiene acceso de lectura.
     */
    public function viewMortalidad(User $user): bool
    {
        // Admin siempre tiene acceso
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        return $user->hasRole('Supervisor') || $user->hasRole('Pasante');
    }

    /**
     * Determine whether the user can view medicamentos report.
     * 
     * SEGURIDAD: Admin siempre tiene acceso total.
     * Pasante tiene acceso de lectura.
     */
    public function viewMedicamentos(User $user): bool
    {
        // Admin siempre tiene acceso
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        return $user->hasRole('Supervisor') || $user->hasRole('Pasante');
    }

    /**
     * Determine whether the user can export reports.
     * 
     * SEGURIDAD: Admin siempre tiene acceso total.
     */
    public function export(User $user): bool
    {
        // Admin siempre tiene acceso
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        return $user->hasRole('Supervisor');
    }
}
