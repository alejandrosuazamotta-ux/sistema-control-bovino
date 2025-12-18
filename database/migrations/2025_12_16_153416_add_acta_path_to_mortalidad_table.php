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
        if (!Schema::hasColumn('mortalidad', 'acta_path')) {
            Schema::table('mortalidad', function (Blueprint $table) {
                $table->string('acta_path')->nullable()->after('acta')->comment('Ruta del archivo de acta/evidencia (imagen/PDF)');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('mortalidad', 'acta_path')) {
            Schema::table('mortalidad', function (Blueprint $table) {
                $table->dropColumn('acta_path');
            });
        }
    }
};
