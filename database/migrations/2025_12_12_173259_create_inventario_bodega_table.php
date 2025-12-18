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
        Schema::create('inventario_bodega', function (Blueprint $table) {
            $table->id('id_inventario');
            $table->string('codigo', 50)->unique()->comment('Código único del producto');
            $table->string('nombre', 200)->comment('Nombre del producto/insumo');
            $table->enum('tipo_producto', ['Medicamento', 'Insumo', 'Alimento', 'Equipo', 'Otro'])->default('Insumo');
            $table->unsignedBigInteger('id_medicamento')->nullable()->comment('FK a medicamentos si es medicamento');
            $table->string('unidad_medida', 20)->default('Unidad')->comment('Unidad, Litro, Kilogramo, etc.');
            $table->decimal('stock_actual', 10, 2)->default(0)->comment('Cantidad actual en inventario');
            $table->decimal('stock_minimo', 10, 2)->default(0)->comment('Stock mínimo requerido (para alertas)');
            $table->decimal('stock_maximo', 10, 2)->nullable()->comment('Stock máximo recomendado');
            $table->decimal('precio_unitario', 10, 2)->default(0)->comment('Precio por unidad');
            $table->string('proveedor', 200)->nullable()->comment('Proveedor del producto');
            $table->date('fecha_vencimiento')->nullable()->comment('Fecha de vencimiento (si aplica)');
            $table->string('lote', 50)->nullable()->comment('Número de lote');
            $table->text('ubicacion_bodega')->nullable()->comment('Ubicación física en bodega');
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            // Índices
            $table->index('tipo_producto');
            $table->index('activo');
            $table->index('stock_actual');
            $table->index('fecha_vencimiento');
            
            // Foreign key a medicamentos
            $table->foreign('id_medicamento')
                  ->references('id_medicamento')
                  ->on('medicamentos')
                  ->onDelete('set null');
        });
        
        // Tabla de movimientos de inventario (entradas y salidas)
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id('id_movimiento');
            $table->unsignedBigInteger('id_inventario');
            $table->enum('tipo_movimiento', ['Entrada', 'Salida', 'Ajuste'])->comment('Tipo de movimiento');
            $table->decimal('cantidad', 10, 2)->comment('Cantidad movida');
            $table->decimal('precio_unitario', 10, 2)->nullable()->comment('Precio al momento del movimiento');
            $table->decimal('valor_total', 10, 2)->nullable()->comment('Valor total del movimiento');
            $table->date('fecha_movimiento')->comment('Fecha del movimiento');
            $table->string('motivo', 200)->nullable()->comment('Motivo del movimiento');
            $table->unsignedBigInteger('id_personal')->nullable()->comment('Personal que realizó el movimiento');
            $table->unsignedBigInteger('id_vaca')->nullable()->comment('Vaca relacionada (si es salida por uso)');
            $table->unsignedBigInteger('id_uso_medicamento')->nullable()->comment('Uso de medicamento relacionado');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            // Índices
            $table->index('id_inventario');
            $table->index('tipo_movimiento');
            $table->index('fecha_movimiento');
            
            // Foreign keys
            $table->foreign('id_inventario')
                  ->references('id_inventario')
                  ->on('inventario_bodega')
                  ->onDelete('cascade');
                  
            $table->foreign('id_personal')
                  ->references('id_personal')
                  ->on('personal')
                  ->onDelete('set null');
                  
            $table->foreign('id_vaca')
                  ->references('id_vaca')
                  ->on('vacas')
                  ->onDelete('set null');
                  
            $table->foreign('id_uso_medicamento')
                  ->references('id_uso')
                  ->on('uso_medicamentos')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
        Schema::dropIfExists('inventario_bodega');
    }
};
