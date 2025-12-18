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
        Schema::create('personal', function (Blueprint $table) {
            $table->id('id_personal');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nombre',100);
            $table->enum('rol',['Pasante','Supervisor','Otro']);
            $table->date('fecha_contratacion')->nullable();
            $table->string('email',100)->nullable();
            $table->string('telefono',20)->nullable();
            $table->text('direccion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal');
    }
};
