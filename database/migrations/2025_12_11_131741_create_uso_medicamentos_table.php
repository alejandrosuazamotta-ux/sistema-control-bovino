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
        Schema::create('uso_medicamentos', function (Blueprint $table) {
            $table->id('id_uso');
            $table->foreignId('id_medicamento')->constrained('medicamentos', 'id_medicamento')->onDelete('restrict');
            $table->foreignId('id_vaca')->constrained('vacas', 'id_vaca')->onDelete('cascade');
            $table->date('fecha_aplicacion');
            $table->decimal('dosis_aplicada', 8, 2)->nullable()->comment('Cantidad aplicada');
            $table->string('unidad_dosis', 20)->nullable()->comment('ml, mg, unidades, etc.');
            $table->foreignId('id_personal')->nullable()->constrained('personal', 'id_personal')->onDelete('set null');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index('id_vaca');
            $table->index('fecha_aplicacion');
            $table->index('id_medicamento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uso_medicamentos');
    }
};
