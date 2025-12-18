<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProduccionLecheraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('produccion_lechera')->insert([
            [
                'id_vaca'=>1,
                'fecha'=>now(),
                'cantidad_leche'=>18.5,
            ]
    ]);

    }
}
