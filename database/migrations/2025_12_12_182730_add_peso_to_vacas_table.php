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
        Schema::table('vacas', function (Blueprint $table) {
            $table->decimal('peso_kg', 8, 2)->nullable()->after('estado_reproductivo')->comment('Peso del animal en kilogramos (para cálculo de UGG)');
            $table->index('peso_kg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vacas', function (Blueprint $table) {
            $table->dropIndex(['peso_kg']);
            $table->dropColumn('peso_kg');
        });
    }
};
