<?php

namespace App\Policies;

use App\Models\ApoyoReproductivoPasante;
use App\Models\User;

class ApoyoReproductivoPasantePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Pasante') || $user->hasRole('Admin');
    }

    public function view(User $user, ApoyoReproductivoPasante $apoyoReproductivoPasante): bool
    {
        return $apoyoReproductivoPasante->user_id === $user->id || $user->hasRole('Admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Pasante');
    }

    public function update(User $user, ApoyoReproductivoPasante $apoyoReproductivoPasante): bool
    {
        // Admin siempre tiene acceso total
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        if ($apoyoReproductivoPasante->aprobada) {
            return false;
        }
        return $apoyoReproductivoPasante->user_id === $user->id;
    }

    public function delete(User $user, ApoyoReproductivoPasante $apoyoReproductivoPasante): bool
    {
        if ($apoyoReproductivoPasante->aprobada) {
            return false;
        }
        return $apoyoReproductivoPasante->user_id === $user->id || $user->hasRole('Admin');
    }

    public function aprobar(User $user, ApoyoReproductivoPasante $apoyoReproductivoPasante): bool
    {
        return $user->hasRole('Admin');
    }
}
