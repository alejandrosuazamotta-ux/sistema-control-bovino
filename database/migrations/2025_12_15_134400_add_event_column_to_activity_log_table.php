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
        Schema::connection(config('activitylog.database_connection'))->table(config('activitylog.table_name'), function (Blueprint $table) {
            if (!Schema::connection(config('activitylog.database_connection'))->hasColumn(config('activitylog.table_name'), 'event')) {
                $table->string('event')->nullable()->after('subject_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection(config('activitylog.database_connection'))->table(config('activitylog.table_name'), function (Blueprint $table) {
            if (Schema::connection(config('activitylog.database_connection'))->hasColumn(config('activitylog.table_name'), 'event')) {
                $table->dropColumn('event');
            }
        });
    }
};
