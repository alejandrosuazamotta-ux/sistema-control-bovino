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
        Schema::create('crias', function (Blueprint $table) {
            $table->id('id_cria');
            $table->foreignId('id_vaca_madre')->constrained('vacas','id_vaca');
            $table->date('fecha_nacimiento');
            $table->decimal('peso',5,2)->nullable();
            $table->enum('estado_destete',['No destetada','Destetada'])->default('No destetada');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crias');
    }
};
