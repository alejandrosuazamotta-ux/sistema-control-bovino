<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asignacion_potreros', function (Blueprint $table) {
            $table->id('id_asignacion');
            $table->foreignId('id_potrero')->constrained('potreros', 'id_potrero');
            $table->foreignId('id_vaca')->constrained('vacas', 'id_vaca');
            $table->date('fecha_asignacion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion_potreros');
    }
};
