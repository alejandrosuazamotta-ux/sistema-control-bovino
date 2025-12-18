<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegistrosReproductivosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('registros_reproductivos')->insert([
            [
                'id_vaca'=>1,
                'tipo_evento'=>'Celo',
                'fecha_evento'=>now()
            ]
        ]);
    }
}
