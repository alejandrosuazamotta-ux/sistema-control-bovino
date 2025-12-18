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
        Schema::create('alimentacion', function (Blueprint $table) {
            $table->id('id_alimentacion');
            $table->foreignId('id_vaca')->constrained('vacas','id_vaca');
            $table->date('fecha');
            $table->enum('tipo_alimento',['Ensilaje','Pasto','Concentrado','Subproducto','Otro']);
            $table->decimal('cantidad',5,2)->nullable();
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
        Schema::dropIfExists('alimentacion');
    }
};
