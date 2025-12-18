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
        Schema::create('retiros', function (Blueprint $table) {
            $table->id('id_retiro');
            $table->foreignId('id_vaca')->constrained('vacas', 'id_vaca')->onDelete('cascade');
            $table->foreignId('id_uso_medicamento')->nullable()->constrained('uso_medicamentos', 'id_uso')->onDelete('set null');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->enum('tipo_retiro', ['Ordeño', 'Producción'])->default('Ordeño');
            $table->boolean('activo')->default(true)->comment('Si el retiro está activo');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index('id_vaca');
            $table->index('activo');
            $table->index('fecha_inicio');
            $table->index('fecha_fin');
            $table->index(['id_vaca', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retiros');
    }
};
