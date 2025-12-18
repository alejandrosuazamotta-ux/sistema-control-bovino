<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apoyo_ordeno_pasantes', function (Blueprint $table) {
            $table->id('id_apoyo_ordeno');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('Pasante que realiza el apoyo');
            $table->foreignId('id_vaca')->constrained('vacas', 'id_vaca')->onDelete('cascade');
            $table->date('fecha_ordeno')->comment('Fecha del ordeño');
            $table->enum('turno', ['AM', 'PM'])->default('AM');
            $table->decimal('cantidad_leche', 10, 2)->nullable()->comment('Cantidad de leche obtenida (litros)');
            $table->text('observaciones')->nullable()->comment('Observaciones del proceso');
            $table->enum('calidad_leche', ['Excelente', 'Buena', 'Regular', 'Deficiente'])->nullable();
            $table->string('evidencia_foto', 500)->nullable()->comment('Foto del proceso de ordeño');
            $table->boolean('aprobada')->default(false)->comment('Aprobada por supervisor');
            $table->foreignId('aprobada_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('id_vaca');
            $table->index('fecha_ordeno');
            $table->index('turno');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apoyo_ordeno_pasantes');
    }
};
