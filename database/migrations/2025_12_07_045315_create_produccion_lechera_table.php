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
       Schema::create('produccion_lechera', function (Blueprint $table) {
            $table->id('id_produccion');
            $table->foreignId('id_vaca')->constrained('vacas','id_vaca');
            $table->date('fecha');
            $table->decimal('cantidad_leche',5,2);
            $table->foreignId('id_personal')->nullable()->constrained('personal','id_personal');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produccion_lechera');
    }
};
