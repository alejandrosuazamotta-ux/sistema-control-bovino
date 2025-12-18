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
        Schema::create('vacas', function (Blueprint $table) {
            $table->id('id_vaca');
            $table->string('codigo',20)->unique();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('raza',50)->nullable();
            $table->enum('estado_salud',['Sana','En tratamiento','En observación'])->default('Sana');
            $table->enum('estado_reproductivo',['Celo','Preñada','Lactancia','Descanso'])->default('Descanso');
            $table->foreignId('id_potrero')->nullable()->constrained('potreros','id_potrero');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacas');
    }
};
