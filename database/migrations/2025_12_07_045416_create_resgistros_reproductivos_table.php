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
        Schema::create('registros_reproductivos', function (Blueprint $table) {
            $table->id('id_registro');
            $table->foreignId('id_vaca')->constrained('vacas','id_vaca');
            $table->enum('tipo_evento',['Inseminación','Parto','Celo','Días abiertos']);
            $table->date('fecha_evento');
            $table->text('observaciones')->nullable();
            $table->foreignId('id_personal')->nullable()->constrained('personal','id_personal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros_reproductivos');
    }
};
