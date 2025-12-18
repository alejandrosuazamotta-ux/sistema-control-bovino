<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rotacion_potreros_pasantes', function (Blueprint $table) {
            $table->id('id_rotacion');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('Pasante que realiza la rotación');
            $table->foreignId('id_potrero_origen')->constrained('potreros', 'id_potrero')->onDelete('cascade');
            $table->foreignId('id_potrero_destino')->constrained('potreros', 'id_potrero')->onDelete('cascade');
            $table->foreignId('id_vaca')->constrained('vacas', 'id_vaca')->onDelete('cascade');
            $table->date('fecha_rotacion')->comment('Fecha de la rotación');
            $table->text('motivo')->nullable()->comment('Motivo de la rotación');
            $table->text('observaciones')->nullable();
            $table->string('evidencia_foto', 500)->nullable()->comment('Foto de evidencia');
            $table->boolean('aprobada')->default(false)->comment('Aprobada por supervisor');
            $table->foreignId('aprobada_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('id_potrero_origen');
            $table->index('id_potrero_destino');
            $table->index('id_vaca');
            $table->index('fecha_rotacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rotacion_potreros_pasantes');
    }
};
