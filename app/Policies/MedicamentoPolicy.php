<?php

namespace App\Policies;

use App\Models\Medicamento;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MedicamentoPolicy
{
    /**
     * Determine whether the user can view any models.
     * 
     * SEGURIDAD: Admin siempre tiene acceso total.
     * Pasante NO debe tener acceso a medicamentos según requerimiento.
     */
    public function viewAny(User $user): bool
    {
        // Admin siempre tiene acceso
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        return $user->hasRole('Supervisor');
    }

    /**
     * Determine whether the user can view the model.
     * 
     * SEGURIDAD: Admin siempre tiene acceso total.
     * Pasante NO debe tener acceso a medicamentos según requerimiento.
     */
    public function view(User $user, Medicamento $medicamento): bool
    {
        // Admin siempre tiene acceso
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        return $user->hasRole('Supervisor');
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
    public function update(User $user, Medicamento $medicamento): bool
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
    public function delete(User $user, Medicamento $medicamento): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Medicamento $medicamento): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Medicamento $medicamento): bool
    {
        return $user->hasRole('Admin');
    }
}
