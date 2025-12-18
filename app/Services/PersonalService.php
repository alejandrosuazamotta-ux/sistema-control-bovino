<?php

namespace App\Services;

use App\Models\Personal;
use App\Models\User;
use App\Repositories\PersonalRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PersonalService
{
    protected PersonalRepository $repository;

    public function __construct(PersonalRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Crear nuevo personal con usuario
     *
     * @param array $data
     * @return Personal
     * @throws \Exception
     */
    public function create(array $data): Personal
    {
        return DB::transaction(function () use ($data) {
            // Verificar que el email no exista
            if ($this->repository->emailExists($data['email'])) {
                throw new \Exception('El email ya está registrado.');
            }

            // Crear usuario
            $user = $this->repository->createUser([
                'name' => $data['nombre'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            // Crear personal asociado
            $personal = $this->repository->create([
                'nombre' => $data['nombre'],
                'rol' => $data['rol'],
                'fecha_contratacion' => $data['fecha_contratacion'] ?? null,
                'telefono' => $data['telefono'] ?? null,
                'direccion' => $data['direccion'] ?? null,
                'user_id' => $user->id,
            ]);

            // Log de actividad
            Log::info('Personal creado', [
                'personal_id' => $personal->id_personal,
                'user_id' => $user->id,
                'created_by' => auth()->id()
            ]);

            return $personal;
        });
    }

    /**
     * Actualizar personal y usuario
     *
     * @param Personal $personal
     * @param array $data
     * @return Personal
     * @throws \Exception
     */
    public function update(Personal $personal, array $data): Personal
    {
        return DB::transaction(function () use ($personal, $data) {
            $user = $personal->user;

            if (!$user) {
                throw new \Exception('El personal no tiene usuario asociado.');
            }

            // Verificar que el email no esté en uso por otro usuario
            if (isset($data['email']) && $this->repository->emailExists($data['email'], $user->id)) {
                throw new \Exception('El email ya está registrado por otro usuario.');
            }

            // Actualizar usuario
            $userData = [
                'name' => $data['nombre'],
                'email' => $data['email'],
            ];

            if (isset($data['password']) && !empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            $this->repository->updateUser($user, $userData);

            // Actualizar personal
            $this->repository->update($personal, [
                'nombre' => $data['nombre'],
                'rol' => $data['rol'],
                'fecha_contratacion' => $data['fecha_contratacion'] ?? null,
                'telefono' => $data['telefono'] ?? null,
                'direccion' => $data['direccion'] ?? null,
            ]);

            $personal->refresh();

            // Log de actividad
            Log::info('Personal actualizado', [
                'personal_id' => $personal->id_personal,
                'user_id' => $user->id,
                'updated_by' => auth()->id()
            ]);

            return $personal;
        });
    }

    /**
     * Eliminar personal y usuario
     *
     * @param Personal $personal
     * @return bool
     * @throws \Exception
     */
    public function delete(Personal $personal): bool
    {
        return DB::transaction(function () use ($personal) {
            $personalId = $personal->id_personal;
            $userId = $personal->user?->id;

            // Eliminar usuario si existe
            if ($personal->user) {
                $this->repository->deleteUser($personal->user);
            }

            // Eliminar personal
            $deleted = $this->repository->delete($personal);

            if ($deleted) {
                // Log de actividad
                Log::info('Personal eliminado', [
                    'personal_id' => $personalId,
                    'user_id' => $userId,
                    'deleted_by' => auth()->id()
                ]);
            }

            return $deleted;
        });
    }

    /**
     * Obtener lista paginada de personal
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 10)
    {
        return $this->repository->paginateWithUser($perPage);
    }

    /**
     * Obtener personal con usuario por ID
     *
     * @param string $id
     * @return Personal|null
     */
    public function findWithUser(string $id): ?Personal
    {
        return $this->repository->findWithUser($id);
    }

    /**
     * Obtener personal por ID
     *
     * @param string $id
     * @return Personal|null
     */
    public function findById(string $id): ?Personal
    {
        return $this->repository->findById($id);
    }
}

