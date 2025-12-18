<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modificar el enum para agregar 'Muerta'
        DB::statement("ALTER TABLE vacas MODIFY COLUMN estado_salud ENUM('Sana','En tratamiento','En observación','Muerta') DEFAULT 'Sana'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Antes de eliminar 'Muerta', cambiar las vacas con ese estado
        DB::table('vacas')
            ->where('estado_salud', 'Muerta')
            ->update(['estado_salud' => 'En observación']);
        
        // Restaurar el enum original
        DB::statement("ALTER TABLE vacas MODIFY COLUMN estado_salud ENUM('Sana','En tratamiento','En observación') DEFAULT 'Sana'");
    }
};
