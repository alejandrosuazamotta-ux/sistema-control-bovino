<?php

namespace App\Repositories;

use App\Models\Personal;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PersonalRepository
{
    /**
     * Obtener todo el personal con relaciones
     */
    public function allWithUser(): Collection
    {
        return Personal::with('user')->get();
    }

    /**
     * Obtener personal paginado con relaciones
     */
    public function paginateWithUser(int $perPage = 10): LengthAwarePaginator
    {
        return Personal::with('user')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Obtener personal por ID con relaciones
     */
    public function findWithUser(string $id): ?Personal
    {
        return Personal::with('user')->find($id);
    }

    /**
     * Obtener personal por ID
     */
    public function findById(string $id): ?Personal
    {
        return Personal::find($id);
    }

    /**
     * Crear usuario
     */
    public function createUser(array $data): User
    {
        return User::create($data);
    }

    /**
     * Crear personal
     */
    public function create(array $data): Personal
    {
        return Personal::create($data);
    }

    /**
     * Actualizar usuario
     */
    public function updateUser(User $user, array $data): bool
    {
        return $user->update($data);
    }

    /**
     * Actualizar personal
     */
    public function update(Personal $personal, array $data): bool
    {
        return $personal->update($data);
    }

    /**
     * Eliminar usuario
     */
    public function deleteUser(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Eliminar personal
     */
    public function delete(Personal $personal): bool
    {
        return $personal->delete();
    }

    /**
     * Verificar si email existe
     */
    public function emailExists(string $email, ?int $excludeUserId = null): bool
    {
        $query = User::where('email', $email);
        
        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }
        
        return $query->exists();
    }
}

