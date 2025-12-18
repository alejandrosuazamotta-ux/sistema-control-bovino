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
        Schema::create('salud', function (Blueprint $table) {
            $table->id('id_salud');
            $table->foreignId('id_vaca')->constrained('vacas','id_vaca');
            $table->enum('tipo_registro',['Vacunación','Tratamiento','Prueba mastitis','Otro']);
            $table->date('fecha');
            $table->text('descripcion')->nullable();
            $table->string('resultado_prueba',50)->nullable();
            $table->foreignId('id_personal')->nullable()->constrained('personal','id_personal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salud');
    }
};
