<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('crias')->insert([
            [
                'id_vaca_madre'=>1,
                'fecha_nacimiento'=>'2025-01-05',
                'peso'=>32.5
            ]
        ]);
    }
}
