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
        Schema::table('salud', function (Blueprint $table) {
            // Agregar campos para pruebas sanitarias completas
            if (!Schema::hasColumn('salud', 'tipo_prueba')) {
                $table->enum('tipo_prueba', ['Mastitis', 'Brucelosis', 'Tuberculosis', 'Otra'])->nullable()->after('tipo_registro');
            }
            if (!Schema::hasColumn('salud', 'resultado')) {
                $table->enum('resultado', ['Positivo', 'Negativo', 'Pendiente'])->nullable()->after('resultado_prueba');
            }
            if (!Schema::hasColumn('salud', 'fecha_resultado')) {
                $table->date('fecha_resultado')->nullable()->after('resultado');
            }
            if (!Schema::hasColumn('salud', 'severidad')) {
                $table->enum('severidad', ['Leve', 'Moderada', 'Severa'])->nullable()->after('fecha_resultado');
            }
            if (!Schema::hasColumn('salud', 'tratamiento_sugerido')) {
                $table->text('tratamiento_sugerido')->nullable()->after('severidad');
            }
            if (!Schema::hasColumn('salud', 'restriccion_ordeño')) {
                $table->boolean('restriccion_ordeño')->default(false)->after('tratamiento_sugerido');
            }
            if (!Schema::hasColumn('salud', 'inhabilitada')) {
                $table->boolean('inhabilitada')->default(false)->after('restriccion_ordeño');
            }
            if (!Schema::hasColumn('salud', 'acta')) {
                $table->string('acta', 100)->nullable()->after('inhabilitada');
            }
            if (!Schema::hasColumn('salud', 'responsable_prueba')) {
                $table->string('responsable_prueba', 100)->nullable()->after('acta');
            }
            if (!Schema::hasColumn('salud', 'observaciones')) {
                $table->text('observaciones')->nullable()->after('responsable_prueba');
            }

            // Actualizar enum de tipo_registro para incluir todas las pruebas
            // Nota: MySQL no permite modificar ENUM directamente, se hace con raw SQL si es necesario
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salud', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_prueba',
                'resultado',
                'fecha_resultado',
                'severidad',
                'tratamiento_sugerido',
                'restriccion_ordeño',
                'inhabilitada',
                'acta',
                'responsable_prueba',
                'observaciones'
            ]);
        });
    }
};
