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
            // Campos avanzados para rotación de potreros
            if (!Schema::hasColumn('asignacion_potreros', 'carga_ugg')) {
                $table->decimal('carga_ugg', 8, 2)->nullable()->after('ugg_total')
                    ->comment('Carga UGG por hectárea durante la estancia');
            }
            
            if (!Schema::hasColumn('asignacion_potreros', 'aforo_kg')) {
                $table->decimal('aforo_kg', 10, 2)->nullable()->after('carga_ugg')
                    ->comment('Aforo en kg por hectárea al momento de la asignación');
            }
            
            if (!Schema::hasColumn('asignacion_potreros', 'peso_ingreso')) {
                $table->decimal('peso_ingreso', 8, 2)->nullable()->after('aforo_kg')
                    ->comment('Peso de la vaca al ingresar al potrero (kg)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asignacion_potreros', function (Blueprint $table) {
            if (Schema::hasColumn('asignacion_potreros', 'peso_ingreso')) {
                $table->dropColumn('peso_ingreso');
            }
            if (Schema::hasColumn('asignacion_potreros', 'aforo_kg')) {
                $table->dropColumn('aforo_kg');
            }
            if (Schema::hasColumn('asignacion_potreros', 'carga_ugg')) {
                $table->dropColumn('carga_ugg');
            }
        });
    }
};
