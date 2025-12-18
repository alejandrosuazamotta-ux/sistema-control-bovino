<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vacas', function (Blueprint $table) {
            $table->string('foto', 500)->nullable()->after('peso_kg')->comment('Ruta de la foto del animal');
            $table->index('foto');
        });
    }

    public function down(): void
    {
        Schema::table('vacas', function (Blueprint $table) {
            $table->dropIndex(['foto']);
            $table->dropColumn('foto');
        });
    }
};
