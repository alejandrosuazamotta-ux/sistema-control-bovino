<?php

namespace App\Policies;

use App\Models\ApoyoOrdeñoPasante;
use App\Models\User;

class ApoyoOrdeñoPasantePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Pasante') || $user->hasRole('Admin');
    }

    public function view(User $user, ApoyoOrdeñoPasante $apoyoOrdeñoPasante): bool
    {
        return $apoyoOrdeñoPasante->user_id === $user->id || $user->hasRole('Admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Pasante');
    }

    public function update(User $user, ApoyoOrdeñoPasante $apoyoOrdeñoPasante): bool
    {
        // Admin siempre tiene acceso total
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        if ($apoyoOrdeñoPasante->aprobada) {
            return false;
        }
        return $apoyoOrdeñoPasante->user_id === $user->id;
    }

    public function delete(User $user, ApoyoOrdeñoPasante $apoyoOrdeñoPasante): bool
    {
        if ($apoyoOrdeñoPasante->aprobada) {
            return false;
        }
        return $apoyoOrdeñoPasante->user_id === $user->id || $user->hasRole('Admin');
    }

    public function aprobar(User $user, ApoyoOrdeñoPasante $apoyoOrdeñoPasante): bool
    {
        return $user->hasRole('Admin');
    }
}

