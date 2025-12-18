<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SaludSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('salud')->insert([
            [
                'id_vaca'=>1,
                'tipo_registro'=>'Vacunación',
                'fecha'=>now(),
                'descripcion'=>'Vacuna aftosa'
            ]
        ]);
    }
}
