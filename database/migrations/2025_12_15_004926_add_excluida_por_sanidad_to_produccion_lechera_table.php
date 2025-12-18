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
        Schema::table('produccion_lechera', function (Blueprint $table) {
            if (!Schema::hasColumn('produccion_lechera', 'excluida_por_sanidad')) {
                $table->boolean('excluida_por_sanidad')->default(false)->after('excluida_por_retiro')->comment('Indica si la producción fue excluida por prueba sanitaria positiva (mastitis)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produccion_lechera', function (Blueprint $table) {
            if (Schema::hasColumn('produccion_lechera', 'excluida_por_sanidad')) {
                $table->dropColumn('excluida_por_sanidad');
            }
        });
    }
};
