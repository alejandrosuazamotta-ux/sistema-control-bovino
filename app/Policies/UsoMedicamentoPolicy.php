<?php

namespace App\Policies;

use App\Models\UsoMedicamento;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UsoMedicamentoPolicy
{
    /**
     * Determine whether the user can view any models.
     * 
     * SEGURIDAD: Admin siempre tiene acceso total.
     * Pasante NO debe tener acceso a uso de medicamentos según requerimiento.
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
     * Pasante NO debe tener acceso a uso de medicamentos según requerimiento.
     */
    public function view(User $user, UsoMedicamento $usoMedicamento): bool
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
     * Pasante NO debe tener acceso a uso de medicamentos según requerimiento.
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
    public function update(User $user, UsoMedicamento $usoMedicamento): bool
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
    public function delete(User $user, UsoMedicamento $usoMedicamento): bool
    {
        // Solo Admin puede eliminar
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UsoMedicamento $usoMedicamento): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UsoMedicamento $usoMedicamento): bool
    {
        return $user->hasRole('Admin');
    }
}

