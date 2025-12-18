<?php

namespace App\Policies;

use App\Models\ActividadPasante;
use App\Models\User;

class ActividadPasantePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Pasante') || $user->hasRole('Admin');
    }

    public function view(User $user, ActividadPasante $actividadPasante): bool
    {
        // El pasante solo puede ver sus propias actividades
        // El admin puede ver todas
        return $actividadPasante->user_id === $user->id || $user->hasRole('Admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Pasante');
    }

    public function update(User $user, ActividadPasante $actividadPasante): bool
    {
        // Admin siempre tiene acceso total
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        // Solo el pasante propietario puede actualizar, y solo si no está aprobada
        if ($actividadPasante->aprobada) {
            return false;
        }
        return $actividadPasante->user_id === $user->id;
    }

    public function delete(User $user, ActividadPasante $actividadPasante): bool
    {
        // Admin siempre tiene acceso total
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        // Solo el pasante propietario puede eliminar, y solo si no está aprobada
        if ($actividadPasante->aprobada) {
            return false;
        }
        return $actividadPasante->user_id === $user->id;
    }

    public function aprobar(User $user, ActividadPasante $actividadPasante): bool
    {
        // Solo admin puede aprobar
        return $user->hasRole('Admin');
    }
}
