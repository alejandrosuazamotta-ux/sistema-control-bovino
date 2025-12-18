<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('registros_reproductivos', function (Blueprint $table) {
            // Agregar campos de palpación
            $table->enum('resultado_palpacion', ['Vacia', 'Preñada'])->nullable()->after('tipo_evento');
            $table->integer('tiempo_gestacion_dias')->nullable()->after('resultado_palpacion')->comment('Días de gestación al momento de la palpación');
            $table->date('fecha_probable_parto')->nullable()->after('tiempo_gestacion_dias')->comment('Calculado automáticamente');
            $table->string('especialista', 100)->nullable()->after('fecha_probable_parto')->comment('Nombre del especialista que realizó la palpación');
            $table->integer('dias_abiertos')->nullable()->after('especialista')->comment('Días entre último parto y nueva preñez');
        });

        // Actualizar el enum tipo_evento para incluir 'Palpación'
        // MySQL no permite modificar ENUM directamente, necesitamos usar ALTER TABLE
        DB::statement("ALTER TABLE registros_reproductivos MODIFY COLUMN tipo_evento ENUM('Inseminación','Parto','Celo','Días abiertos','Palpación') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registros_reproductivos', function (Blueprint $table) {
            $table->dropColumn([
                'resultado_palpacion',
                'tiempo_gestacion_dias',
                'fecha_probable_parto',
                'especialista',
                'dias_abiertos'
            ]);
        });

        // Revertir el enum a su estado original
        DB::statement("ALTER TABLE registros_reproductivos MODIFY COLUMN tipo_evento ENUM('Inseminación','Parto','Celo','Días abiertos') NOT NULL");
    }
};
