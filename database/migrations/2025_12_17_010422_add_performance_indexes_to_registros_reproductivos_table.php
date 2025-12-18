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
        Schema::table('registros_reproductivos', function (Blueprint $table) {
            // Índice en fecha_evento para consultas por rango de fechas
            if (!$this->hasIndex('registros_reproductivos', 'registros_reproductivos_fecha_evento_index')) {
                $table->index('fecha_evento', 'registros_reproductivos_fecha_evento_index');
            }
            
            // Índice compuesto en (id_vaca, fecha_evento) para historial reproductivo por vaca
            if (!$this->hasIndex('registros_reproductivos', 'registros_reproductivos_vaca_fecha_index')) {
                $table->index(['id_vaca', 'fecha_evento'], 'registros_reproductivos_vaca_fecha_index');
            }
            
            // Índice en tipo_evento para filtros por tipo de evento
            if (Schema::hasColumn('registros_reproductivos', 'tipo_evento')) {
                if (!$this->hasIndex('registros_reproductivos', 'registros_reproductivos_tipo_evento_index')) {
                    $table->index('tipo_evento', 'registros_reproductivos_tipo_evento_index');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registros_reproductivos', function (Blueprint $table) {
            $table->dropIndex('registros_reproductivos_fecha_evento_index');
            $table->dropIndex('registros_reproductivos_vaca_fecha_index');
            if (Schema::hasColumn('registros_reproductivos', 'tipo_evento')) {
                $table->dropIndex('registros_reproductivos_tipo_evento_index');
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
