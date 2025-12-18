<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('produccion_lechera', function (Blueprint $table) {
            // Agregar campo turno (AM/PM) con default para datos existentes
            $table->enum('turno', ['AM', 'PM'])->default('AM')->after('fecha');
            
            // Agregar campo destino con default
            $table->enum('destino', ['Agroindustria', 'Lechero', 'Particular', 'Consumo'])->default('Agroindustria')->after('cantidad_leche');
            
            // Agregar campos de valorización
            $table->decimal('valor_unidad', 10, 2)->nullable()->after('destino');
            $table->decimal('valor_total', 10, 2)->nullable()->after('valor_unidad');
            
            // Agregar campo de exclusión por retiro
            $table->boolean('excluida_por_retiro')->default(false)->after('valor_total');
        });
        
        // Actualizar registros existentes con valores por defecto (por si acaso)
        DB::statement("UPDATE produccion_lechera SET turno = 'AM' WHERE turno IS NULL");
        DB::statement("UPDATE produccion_lechera SET destino = 'Agroindustria' WHERE destino IS NULL");
        
        // Agregar índice único compuesto después de asegurar que no hay NULLs
        Schema::table('produccion_lechera', function (Blueprint $table) {
            $table->unique(['id_vaca', 'fecha', 'turno'], 'produccion_lechera_unique_vaca_fecha_turno');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produccion_lechera', function (Blueprint $table) {
            // Eliminar índice único
            $table->dropUnique('produccion_lechera_unique_vaca_fecha_turno');
            
            // Eliminar campos
            $table->dropColumn([
                'turno',
                'destino',
                'valor_unidad',
                'valor_total',
                'excluida_por_retiro'
            ]);
        });
    }
};
