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
        if (!Schema::hasColumn('notificaciones', 'estado')) {
            Schema::table('notificaciones', function (Blueprint $table) {
                $table->enum('estado', ['pendiente', 'vista', 'atendida'])->default('pendiente')->after('leida')->comment('Estado de la alerta: pendiente, vista, atendida');
            });
        }

        if (!Schema::hasColumn('notificaciones', 'user_id')) {
            Schema::table('notificaciones', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('estado')->comment('Usuario asignado (opcional, para pasantes)');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                $table->index('user_id');
            });
        }

        if (!Schema::hasColumn('notificaciones', 'fecha_atendida')) {
            Schema::table('notificaciones', function (Blueprint $table) {
                $table->timestamp('fecha_atendida')->nullable()->after('fecha_leida')->comment('Fecha en que se atendió la alerta');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('notificaciones', 'fecha_atendida')) {
            Schema::table('notificaciones', function (Blueprint $table) {
                $table->dropColumn('fecha_atendida');
            });
        }

        if (Schema::hasColumn('notificaciones', 'user_id')) {
            Schema::table('notificaciones', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropIndex(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        if (Schema::hasColumn('notificaciones', 'estado')) {
            Schema::table('notificaciones', function (Blueprint $table) {
                $table->dropColumn('estado');
            });
        }
    }
};
