<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VacasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vacas')->insert([
            ['codigo'=>'V001','raza'=>'Holstein','estado_reproductivo'=>'Lactancia'],
            ['codigo'=>'V002','raza'=>'Jersey','estado_reproductivo'=>'Preñada'],
]);
    }
}
