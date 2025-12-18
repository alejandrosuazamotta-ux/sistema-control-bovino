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
        Schema::table('produccion_lechera', function (Blueprint $table) {
            // Índice en fecha para consultas por rango de fechas (reportes diarios/mensuales)
            if (!$this->hasIndex('produccion_lechera', 'produccion_lechera_fecha_index')) {
                $table->index('fecha', 'produccion_lechera_fecha_index');
            }
            
            // Índice compuesto en (id_vaca, fecha) para consultas de producción por vaca
            if (!$this->hasIndex('produccion_lechera', 'produccion_lechera_vaca_fecha_index')) {
                $table->index(['id_vaca', 'fecha'], 'produccion_lechera_vaca_fecha_index');
            }
            
            // Índice en turno para filtros por turno (AM/PM)
            if (Schema::hasColumn('produccion_lechera', 'turno')) {
                if (!$this->hasIndex('produccion_lechera', 'produccion_lechera_turno_index')) {
                    $table->index('turno', 'produccion_lechera_turno_index');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produccion_lechera', function (Blueprint $table) {
            $table->dropIndex('produccion_lechera_fecha_index');
            $table->dropIndex('produccion_lechera_vaca_fecha_index');
            if (Schema::hasColumn('produccion_lechera', 'turno')) {
                $table->dropIndex('produccion_lechera_turno_index');
            }
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
