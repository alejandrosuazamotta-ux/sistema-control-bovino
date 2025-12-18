<?php

namespace App\Policies;

use App\Models\ProduccionLechera;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProduccionLecheraPolicy
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
    public function view(User $user, ProduccionLechera $produccionLechera): bool
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
    public function update(User $user, ProduccionLechera $produccionLechera): bool
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
    public function delete(User $user, ProduccionLechera $produccionLechera): bool
    {
        // Solo admin puede eliminar producciones
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can restore the model.
     * 
     * SEGURIDAD: Solo Admin puede restaurar.
     */
    public function restore(User $user, ProduccionLechera $produccionLechera): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     * 
     * SEGURIDAD: Solo Admin puede eliminar permanentemente.
     */
    public function forceDelete(User $user, ProduccionLechera $produccionLechera): bool
    {
        return $user->hasRole('Admin');
    }
}
