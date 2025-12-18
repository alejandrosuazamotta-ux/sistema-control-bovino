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
            if (!Schema::hasColumn('potreros', 'en_mantenimiento')) {
                $table->boolean('en_mantenimiento')->default(false)->after('aforo_maximo')
                    ->comment('Indica si el potrero está en mantenimiento y no disponible para asignaciones');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('potreros', function (Blueprint $table) {
            if (Schema::hasColumn('potreros', 'en_mantenimiento')) {
                $table->dropColumn('en_mantenimiento');
            }
        });
    }
};
