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
        Schema::create('pruebas_sanitarias', function (Blueprint $table) {
            $table->id('id_prueba');
            $table->unsignedBigInteger('id_vaca');
            $table->enum('tipo_prueba', ['Mastitis', 'Brucelosis', 'Tuberculosis'])->comment('Tipo de prueba sanitaria');
            $table->date('fecha_prueba')->comment('Fecha en que se realizó la prueba');
            $table->enum('resultado', ['Positivo', 'Negativo', 'Pendiente'])->default('Pendiente')->comment('Resultado de la prueba');
            $table->date('fecha_resultado')->nullable()->comment('Fecha en que se obtuvo el resultado');
            $table->enum('severidad', ['Leve', 'Moderada', 'Severa'])->nullable()->comment('Severidad (solo para Mastitis)');
            $table->text('tratamiento_sugerido')->nullable()->comment('Tratamiento sugerido');
            $table->boolean('restriccion_ordeño')->default(false)->comment('Si aplica restricción de ordeño (Mastitis positiva)');
            $table->boolean('inhabilitada')->default(false)->comment('Si la vaca está inhabilitada (Brucelosis/Tuberculosis positiva)');
            $table->string('acta', 100)->nullable()->comment('Número de acta o documento');
            $table->string('responsable_prueba', 100)->nullable()->comment('Nombre del responsable de realizar la prueba');
            $table->unsignedBigInteger('id_personal')->nullable()->comment('Personal que registró la prueba');
            $table->unsignedBigInteger('user_id')->nullable()->comment('Usuario que creó el registro (para Pasante)');
            $table->string('evidencia_archivo')->nullable()->comment('Ruta del archivo de evidencia (imagen/PDF)');
            $table->string('evidencia_tipo')->nullable()->comment('Tipo de evidencia: imagen o pdf');
            $table->text('observaciones')->nullable()->comment('Observaciones adicionales');
            $table->boolean('cerrada')->default(false)->comment('Si la prueba está cerrada (no se puede editar)');
            $table->timestamp('fecha_cierre')->nullable()->comment('Fecha en que se cerró la prueba');
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_vaca')->references('id_vaca')->on('vacas')->onDelete('restrict');
            $table->foreign('id_personal')->references('id_personal')->on('personal')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Índices
            $table->index('id_vaca');
            $table->index('tipo_prueba');
            $table->index('resultado');
            $table->index('fecha_prueba');
            $table->index('cerrada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pruebas_sanitarias');
    }
};
