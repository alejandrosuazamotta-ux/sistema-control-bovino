<?php

namespace App\Policies;

use App\Models\InventarioBodega;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InventarioBodegaPolicy
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
    public function view(User $user, InventarioBodega $inventarioBodega): bool
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
    public function update(User $user, InventarioBodega $inventarioBodega): bool
    {
        // Admin siempre tiene acceso
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        return $user->hasRole('Supervisor');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, InventarioBodega $inventarioBodega): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, InventarioBodega $inventarioBodega): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, InventarioBodega $inventarioBodega): bool
    {
        return $user->hasRole('Admin');
    }
}

