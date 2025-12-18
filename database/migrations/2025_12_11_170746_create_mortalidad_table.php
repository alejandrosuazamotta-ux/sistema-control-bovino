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
        Schema::create('mortalidad', function (Blueprint $table) {
            $table->id('id_mortalidad');
            $table->date('fecha')->comment('Fecha de la muerte');
            $table->time('hora')->nullable()->comment('Hora de la muerte');
            
            // Relación polimórfica: puede ser Vaca o Cria
            $table->string('animal_type')->comment('Tipo de animal: App\Models\Vaca o App\Models\Cria');
            $table->unsignedBigInteger('animal_id')->comment('ID del animal (vaca o cría)');
            
            $table->enum('clasificacion', [
                'Ternero',
                'Novilla',
                'Vaca',
                'Toro',
                'Becerro',
                'Becerra'
            ])->comment('Clasificación del animal');
            
            $table->decimal('peso', 8, 2)->nullable()->comment('Peso al momento de la muerte (kg)');
            $table->string('causa', 500)->comment('Causa de la muerte');
            $table->text('acta')->nullable()->comment('Acta o documento relacionado');
            $table->text('observaciones')->nullable()->comment('Observaciones adicionales');
            
            $table->timestamps();
            
            // Índices
            $table->index('fecha');
            $table->index(['animal_type', 'animal_id']);
            $table->index('clasificacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mortalidad');
    }
};
