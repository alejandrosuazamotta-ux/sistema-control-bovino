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
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id('id_notificacion');
            $table->string('tipo', 50)->comment('preparto, celo, destete, retiro, etc.');
            $table->enum('nivel', ['urgente', 'advertencia', 'informacion'])->default('informacion');
            $table->string('titulo', 200);
            $table->text('mensaje');
            $table->string('entidad_tipo')->nullable()->comment('Clase del modelo relacionado');
            $table->unsignedBigInteger('entidad_id')->nullable()->comment('ID del modelo relacionado');
            $table->date('fecha_referencia')->nullable()->comment('Fecha relevante para la alerta');
            $table->boolean('leida')->default(false);
            $table->timestamp('fecha_leida')->nullable();
            $table->timestamps();
            
            $table->index(['tipo', 'leida']);
            $table->index(['entidad_tipo', 'entidad_id']);
            $table->index('nivel');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
