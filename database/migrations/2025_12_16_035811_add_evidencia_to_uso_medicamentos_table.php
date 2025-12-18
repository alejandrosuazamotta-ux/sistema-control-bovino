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
        Schema::table('uso_medicamentos', function (Blueprint $table) {
            if (!Schema::hasColumn('uso_medicamentos', 'evidencia_path')) {
                $table->string('evidencia_path')->nullable()->after('observaciones')->comment('Ruta del archivo de evidencia (imagen/PDF)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uso_medicamentos', function (Blueprint $table) {
            if (Schema::hasColumn('uso_medicamentos', 'evidencia_path')) {
                $table->dropColumn('evidencia_path');
            }
        });
    }
};
