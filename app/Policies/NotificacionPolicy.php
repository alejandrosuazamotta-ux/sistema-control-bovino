<?php

namespace App\Policies;

use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NotificacionPolicy
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
     * Pasante solo puede ver las asignadas a él.
     */
    public function view(User $user, Notificacion $notificacion): bool
    {
        // Admin siempre tiene acceso
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        // Supervisor puede ver todas
        if ($user->hasRole('Supervisor')) {
            return true;
        }
        
        // Pasante solo puede ver las asignadas a él
        if ($user->hasRole('Pasante')) {
            return $notificacion->user_id === $user->id || $notificacion->user_id === null;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     * 
     * SEGURIDAD: Solo Admin puede crear alertas manualmente.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can update the model.
     * 
     * SEGURIDAD: Admin siempre tiene acceso total.
     * Pasante solo puede marcar como vista las asignadas a él.
     */
    public function update(User $user, Notificacion $notificacion): bool
    {
        // Admin siempre tiene acceso
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        // Pasante solo puede marcar como vista las asignadas a él
        if ($user->hasRole('Pasante')) {
            return $notificacion->user_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Notificacion $notificacion): bool
    {
        // Solo admin puede eliminar
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can mark as attended.
     */
    public function marcarAtendida(User $user, Notificacion $notificacion): bool
    {
        // Solo admin puede marcar como atendida
        return $user->hasRole('Admin') || $user->hasRole('Supervisor');
    }

    /**
     * Determine whether the user can mark as viewed.
     */
    public function marcarVista(User $user, Notificacion $notificacion): bool
    {
        // Admin, Supervisor y Pasante pueden marcar como vista
        if ($user->hasRole('Admin') || $user->hasRole('Supervisor')) {
            return true;
        }
        
        // Pasante solo puede marcar como vista las asignadas a él
        if ($user->hasRole('Pasante')) {
            return $notificacion->user_id === $user->id || $notificacion->user_id === null;
        }
        
        return false;
    }
}
