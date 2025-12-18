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
        Schema::create('medicamentos', function (Blueprint $table) {
            $table->id('id_medicamento');
            $table->string('nombre', 100)->unique();
            $table->string('tipo', 50)->nullable()->comment('Antibiótico, Antiparasitario, Vacuna, etc.');
            $table->string('principio_activo', 100)->nullable();
            $table->string('via_administracion', 50)->nullable()->comment('Intramuscular, Subcutánea, Oral, etc.');
            $table->string('dosis', 100)->nullable()->comment('Dosis recomendada');
            $table->integer('periodo_retiro_dias')->default(0)->comment('Días de retiro de ordeño/producción');
            $table->boolean('activo')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index('activo');
            $table->index('tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicamentos');
    }
};
