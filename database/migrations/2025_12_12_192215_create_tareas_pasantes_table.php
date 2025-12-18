<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tareas_pasantes', function (Blueprint $table) {
            $table->id('id_tarea');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('Pasante asignado');
            $table->foreignId('asignada_por')->constrained('users')->onDelete('cascade')->comment('Usuario que asigna la tarea');
            $table->string('titulo', 200)->comment('Título de la tarea');
            $table->text('descripcion')->comment('Descripción de la tarea');
            $table->enum('prioridad', ['Baja', 'Media', 'Alta', 'Urgente'])->default('Media');
            $table->enum('estado', ['Pendiente', 'En Progreso', 'Completada', 'Cancelada'])->default('Pendiente');
            $table->date('fecha_asignacion')->comment('Fecha de asignación');
            $table->date('fecha_limite')->nullable()->comment('Fecha límite para completar');
            $table->date('fecha_completada')->nullable()->comment('Fecha en que se completó');
            $table->text('observaciones')->nullable();
            $table->string('evidencia_foto', 500)->nullable()->comment('Ruta de foto de evidencia');
            $table->string('evidencia_documento', 500)->nullable()->comment('Ruta de documento de evidencia');
            $table->boolean('aprobada')->default(false)->comment('Aprobada por supervisor');
            $table->foreignId('aprobada_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('fecha_asignacion');
            $table->index('estado');
            $table->index('prioridad');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tareas_pasantes');
    }
};
