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
        Schema::table('asignacion_potreros', function (Blueprint $table) {
            $table->date('fecha_salida')->nullable()->after('fecha_asignacion')->comment('Fecha de salida del potrero');
            $table->integer('dias_estancia')->nullable()->after('fecha_salida')->comment('Días de estancia en el potrero');
            $table->integer('dias_descanso')->nullable()->after('dias_estancia')->comment('Días de descanso del potrero después de la salida');
            $table->decimal('ugg_total', 8, 2)->nullable()->after('dias_descanso')->comment('Unidades Gran Ganado totales durante la estancia');
            $table->text('observaciones')->nullable()->after('ugg_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asignacion_potreros', function (Blueprint $table) {
            $table->dropColumn(['fecha_salida', 'dias_estancia', 'dias_descanso', 'ugg_total', 'observaciones']);
        });
    }
};
