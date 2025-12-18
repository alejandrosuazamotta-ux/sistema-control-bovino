<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apoyo_reproductivo_pasantes', function (Blueprint $table) {
            $table->id('id_apoyo_reproductivo');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('Pasante que realiza el apoyo');
            $table->foreignId('id_vaca')->constrained('vacas', 'id_vaca')->onDelete('cascade');
            $table->date('fecha_actividad')->comment('Fecha de la actividad reproductiva');
            $table->enum('tipo_actividad', ['Palpación', 'Inseminación', 'Seguimiento Celo', 'Control Gestación', 'Parto', 'Otro'])->comment('Tipo de actividad');
            $table->text('observaciones')->nullable();
            $table->string('evidencia_foto', 500)->nullable()->comment('Foto de evidencia');
            $table->string('evidencia_documento', 500)->nullable()->comment('Documento de evidencia');
            $table->boolean('aprobada')->default(false)->comment('Aprobada por supervisor');
            $table->foreignId('aprobada_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('id_vaca');
            $table->index('fecha_actividad');
            $table->index('tipo_actividad');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apoyo_reproductivo_pasantes');
    }
};
