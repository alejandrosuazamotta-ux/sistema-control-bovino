<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear roles si no existen
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $pasanteRole = Role::firstOrCreate(['name' => 'Pasante']);
        $supervisorRole = Role::firstOrCreate(['name' => 'Supervisor']);

        // admin
        $admin = User::firstOrCreate(
            ['email' => 'juan@gmail.com'],
            [
                'name' => 'Juan',
                'password' => bcrypt('admin123'),
            ]
        );
        $admin->assignRole($adminRole);

        // pasante
        $pasante = User::firstOrCreate(
            ['email' => 'carlos@gmail.com'],
            [
                'name' => 'Carlos',
                'password' => bcrypt('pasante123'),
            ]
        );
        $pasante->assignRole($pasanteRole);

        // supervisor (opcional)
        $supervisor = User::firstOrCreate(
            ['email' => 'maria@gmail.com'],
            [
                'name' => 'Maria',
                'password' => bcrypt('supervisor123'),
            ]
        );
        $supervisor->assignRole($supervisorRole);
    }
}
