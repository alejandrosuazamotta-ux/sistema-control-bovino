<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AlimentacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('alimentacion')->insert([
            [
                'id_vaca'=>1,
                'fecha'=>now(),
                'tipo_alimento'=>'Pasto',
                'cantidad'=>12.5
            ]
        ]);
    }
}
