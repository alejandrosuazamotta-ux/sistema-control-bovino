<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividades_pasantes', function (Blueprint $table) {
            $table->id('id_actividad');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('Pasante que realiza la actividad');
            $table->string('titulo', 200)->comment('Título de la actividad');
            $table->text('descripcion')->comment('Descripción detallada de la actividad');
            $table->enum('tipo_actividad', ['Ordeño', 'Reproductivo', 'Rotación Potreros', 'Alimentación', 'Salud', 'General'])->comment('Tipo de actividad');
            $table->date('fecha_actividad')->comment('Fecha en que se realizó la actividad');
            $table->time('hora_inicio')->nullable()->comment('Hora de inicio');
            $table->time('hora_fin')->nullable()->comment('Hora de finalización');
            $table->enum('estado', ['Pendiente', 'En Progreso', 'Completada', 'Cancelada'])->default('Pendiente');
            $table->text('observaciones')->nullable();
            $table->string('evidencia_foto', 500)->nullable()->comment('Ruta de foto de evidencia');
            $table->string('evidencia_documento', 500)->nullable()->comment('Ruta de documento de evidencia');
            $table->boolean('aprobada')->default(false)->comment('Aprobada por supervisor');
            $table->foreignId('aprobada_por')->nullable()->constrained('users')->onDelete('set null')->comment('Usuario que aprobó');
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('fecha_actividad');
            $table->index('tipo_actividad');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividades_pasantes');
    }
};
