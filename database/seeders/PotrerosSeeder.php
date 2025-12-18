<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PotrerosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('potreros')->insert([
            ['nombre'=>'Potrero 1','ubicacion'=>'Zona Norte','capacidad'=>12],
            ['nombre'=>'Potrero 2','ubicacion'=>'Zona Sur','capacidad'=>15],
]);
    }
}
