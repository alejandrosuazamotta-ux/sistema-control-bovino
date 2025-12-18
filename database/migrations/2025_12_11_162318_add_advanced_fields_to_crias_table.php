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
        Schema::table('crias', function (Blueprint $table) {
            // Agregar campos nuevos
            $table->string('nombre_cria', 100)->nullable()->after('id_vaca_madre')->comment('Nombre de la cría');
            $table->enum('sexo', ['Macho', 'Hembra'])->nullable()->after('nombre_cria')->comment('Sexo de la cría');
            $table->date('fecha_tatuado')->nullable()->after('sexo')->comment('Fecha en que se tatuó la cría');
            $table->enum('concepcion', ['IA', 'Monta Natural', 'Transferencia Embrionaria'])->nullable()->after('fecha_tatuado')->comment('Método de concepción');
            $table->string('sinigan', 50)->nullable()->after('concepcion')->comment('Código SINIGAN');
            $table->date('fecha_destete')->nullable()->after('estado_destete')->comment('Fecha de destete');
            
            // Índices para búsquedas frecuentes
            $table->index('sexo');
            $table->index('concepcion');
            $table->index('fecha_destete');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crias', function (Blueprint $table) {
            $table->dropIndex(['sexo']);
            $table->dropIndex(['concepcion']);
            $table->dropIndex(['fecha_destete']);
            
            $table->dropColumn([
                'nombre_cria',
                'sexo',
                'fecha_tatuado',
                'concepcion',
                'sinigan',
                'fecha_destete'
            ]);
        });
    }
};
