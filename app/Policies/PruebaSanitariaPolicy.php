<?php

namespace App\Policies;

use App\Models\PruebaSanitaria;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PruebaSanitariaPolicy
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
     * Pasante solo puede ver las que creó.
     */
    public function view(User $user, PruebaSanitaria $pruebaSanitaria): bool
    {
        // Admin siempre tiene acceso
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        // Supervisor puede ver todas
        if ($user->hasRole('Supervisor')) {
            return true;
        }

        // Pasante solo puede ver las que creó
        if ($user->hasRole('Pasante')) {
            return $pruebaSanitaria->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     * 
     * SEGURIDAD: Admin siempre tiene acceso total.
     * Pasante puede crear pruebas sanitarias.
     */
    public function create(User $user): bool
    {
        // Admin siempre tiene acceso
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        return $user->hasRole('Supervisor') || $user->hasRole('Pasante');
    }

    /**
     * Determine whether the user can update the model.
     * 
     * SEGURIDAD: Admin siempre tiene acceso total (incluso si está cerrada).
     * Supervisor puede editar solo si está abierta.
     * Pasante solo puede editar las que creó y que estén abiertas.
     */
    public function update(User $user, PruebaSanitaria $pruebaSanitaria): bool
    {
        // Admin siempre tiene acceso (puede editar incluso si está cerrada)
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Si la prueba está cerrada, Supervisor y Pasante no pueden editarla
        if ($pruebaSanitaria->cerrada) {
            return false;
        }

        // Supervisor puede editar todas las abiertas
        if ($user->hasRole('Supervisor')) {
            return true;
        }

        // Pasante solo puede editar las que creó y que estén abiertas
        if ($user->hasRole('Pasante')) {
            return $pruebaSanitaria->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PruebaSanitaria $pruebaSanitaria): bool
    {
        // Solo Admin puede eliminar
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PruebaSanitaria $pruebaSanitaria): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PruebaSanitaria $pruebaSanitaria): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can close the test.
     */
    public function cerrar(User $user, PruebaSanitaria $pruebaSanitaria): bool
    {
        // Solo Admin y Supervisor pueden cerrar pruebas
        return $user->hasRole('Admin') || $user->hasRole('Supervisor');
    }
}
