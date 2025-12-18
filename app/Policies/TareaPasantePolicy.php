<?php

namespace App\Policies;

use App\Models\TareaPasante;
use App\Models\User;

class TareaPasantePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Pasante') || $user->hasRole('Admin');
    }

    public function view(User $user, TareaPasante $tareaPasante): bool
    {
        return $tareaPasante->user_id === $user->id || $user->hasRole('Admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Admin'); // Solo admin puede asignar tareas
    }

    public function update(User $user, TareaPasante $tareaPasante): bool
    {
        // Admin siempre tiene acceso total
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        if ($tareaPasante->aprobada) {
            return false;
        }
        return $tareaPasante->user_id === $user->id;
    }

    public function delete(User $user, TareaPasante $tareaPasante): bool
    {
        // Admin siempre tiene acceso total
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        if ($tareaPasante->aprobada) {
            return false;
        }
        return $tareaPasante->user_id === $user->id;
    }

    public function aprobar(User $user, TareaPasante $tareaPasante): bool
    {
        return $user->hasRole('Admin');
    }
}
