<?php

namespace App\Policies;

use App\Models\Salud;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SaludPolicy
{
    /**
     * Determine whether the user can view any models.
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
     * Determine whether the user can view the model.
     * 
     * SEGURIDAD: Admin siempre tiene acceso total.
     * Pasante tiene acceso de lectura.
     */
    public function view(User $user, Salud $salud): bool
    {
        // Admin siempre tiene acceso
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        return $user->hasRole('Supervisor') || $user->hasRole('Pasante');
    }

    /**
     * Determine whether the user can create models.
     * 
     * SEGURIDAD: Admin siempre tiene acceso total.
     */
    public function create(User $user): bool
    {
        // Admin siempre tiene acceso
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        return $user->hasRole('Supervisor');
    }

    /**
     * Determine whether the user can update the model.
     * 
     * SEGURIDAD: Admin siempre tiene acceso total.
     */
    public function update(User $user, Salud $salud): bool
    {
        // Admin siempre tiene acceso
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        return $user->hasRole('Supervisor');
    }

    /**
     * Determine whether the user can delete the model.
     * 
     * SEGURIDAD: Solo Admin puede eliminar.
     */
    public function delete(User $user, Salud $salud): bool
    {
        // Solo admin puede eliminar registros de salud
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can restore the model.
     * 
     * SEGURIDAD: Solo Admin puede restaurar.
     */
    public function restore(User $user, Salud $salud): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     * 
     * SEGURIDAD: Solo Admin puede eliminar permanentemente.
     */
    public function forceDelete(User $user, Salud $salud): bool
    {
        return $user->hasRole('Admin');
    }
}
