<?php

namespace App\Policies;

use App\Models\RotacionPotrerosPasante;
use App\Models\User;

class RotacionPotrerosPasantePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('Pasante') || $user->hasRole('Admin');
    }

    public function view(User $user, RotacionPotrerosPasante $rotacionPotrerosPasante): bool
    {
        return $rotacionPotrerosPasante->user_id === $user->id || $user->hasRole('Admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Pasante');
    }

    public function update(User $user, RotacionPotrerosPasante $rotacionPotrerosPasante): bool
    {
        // Admin siempre tiene acceso total
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        if ($rotacionPotrerosPasante->aprobada) {
            return false;
        }
        return $rotacionPotrerosPasante->user_id === $user->id;
    }

    public function delete(User $user, RotacionPotrerosPasante $rotacionPotrerosPasante): bool
    {
        if ($rotacionPotrerosPasante->aprobada) {
            return false;
        }
        return $rotacionPotrerosPasante->user_id === $user->id || $user->hasRole('Admin');
    }

    public function aprobar(User $user, RotacionPotrerosPasante $rotacionPotrerosPasante): bool
    {
        return $user->hasRole('Admin');
    }
}
