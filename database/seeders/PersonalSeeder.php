<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PersonalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('personal')->insert([
            ['user_id'=>4,'nombre'=>'Edwin','rol'=>'Pasante','fecha_contratacion'=>'2012-12-12']
]);
    }
}
