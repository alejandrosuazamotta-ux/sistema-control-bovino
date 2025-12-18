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
            if (!Schema::hasColumn('uso_medicamentos', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id_personal')->constrained('users')->onDelete('set null');
                $table->index('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uso_medicamentos', function (Blueprint $table) {
            if (Schema::hasColumn('uso_medicamentos', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropIndex(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
