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
        Schema::table('potreros', function (Blueprint $table) {
            $table->decimal('area_hectareas', 10, 2)->nullable()->after('capacidad')->comment('Área del potrero en hectáreas');
            $table->integer('dias_descanso_recomendado')->default(30)->after('area_hectareas')->comment('Días de descanso recomendado entre rotaciones');
            $table->decimal('aforo_actual', 8, 2)->nullable()->after('dias_descanso_recomendado')->comment('Aforo actual en UGG/ha');
            $table->decimal('aforo_maximo', 8, 2)->nullable()->after('aforo_actual')->comment('Aforo máximo recomendado en UGG/ha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('potreros', function (Blueprint $table) {
            $table->dropColumn(['area_hectareas', 'dias_descanso_recomendado', 'aforo_actual', 'aforo_maximo']);
        });
    }
};
