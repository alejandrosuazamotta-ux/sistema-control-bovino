<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Agrega índices NO destructivos para optimizar consultas frecuentes
     */
    public function up(): void
    {
        Schema::table('salud', function (Blueprint $table) {
            // Índice en fecha para consultas por rango de fechas
            if (!$this->hasIndex('salud', 'salud_fecha_index')) {
                $table->index('fecha', 'salud_fecha_index');
            }
            
            // Índice compuesto en (tipo_registro, resultado_prueba) para filtros combinados
            if (Schema::hasColumn('salud', 'tipo_registro') && Schema::hasColumn('salud', 'resultado_prueba')) {
                if (!$this->hasIndex('salud', 'salud_tipo_resultado_index')) {
                    $table->index(['tipo_registro', 'resultado_prueba'], 'salud_tipo_resultado_index');
                }
            }
            
            // Índice compuesto en (id_vaca, fecha) para historial de salud por vaca
            if (!$this->hasIndex('salud', 'salud_vaca_fecha_index')) {
                $table->index(['id_vaca', 'fecha'], 'salud_vaca_fecha_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salud', function (Blueprint $table) {
            $table->dropIndex('salud_fecha_index');
            if (Schema::hasColumn('salud', 'tipo_registro') && Schema::hasColumn('salud', 'resultado_prueba')) {
                $table->dropIndex('salud_tipo_resultado_index');
            }
            $table->dropIndex('salud_vaca_fecha_index');
        });
    }

    /**
     * Verificar si un índice ya existe
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();
        
        $result = $connection->select(
            "SELECT COUNT(*) as count 
             FROM information_schema.statistics 
             WHERE table_schema = ? 
             AND table_name = ? 
             AND index_name = ?",
            [$databaseName, $table, $indexName]
        );
        
        return $result[0]->count > 0;
    }
};
