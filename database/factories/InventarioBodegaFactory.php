<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventarioBodega>
 */
class InventarioBodegaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'codigo' => $this->faker->unique()->bothify('INV###'),
            'nombre' => $this->faker->words(3, true),
            'tipo_producto' => $this->faker->randomElement(['Medicamento', 'Insumo', 'Alimento', 'Equipo']),
            'unidad_medida' => $this->faker->randomElement(['Kg', 'L', 'Unidad', 'Caja']),
            'stock_actual' => $this->faker->numberBetween(0, 100),
            'stock_minimo' => $this->faker->numberBetween(5, 20),
            'stock_maximo' => $this->faker->numberBetween(100, 500),
            'precio_unitario' => $this->faker->randomFloat(2, 1000, 50000),
            'proveedor' => $this->faker->optional()->company(),
            'fecha_vencimiento' => $this->faker->optional()->dateTimeBetween('now', '+2 years'),
            'lote' => $this->faker->optional()->bothify('LOT###'),
            'ubicacion_bodega' => $this->faker->optional()->word(),
            'activo' => true,
            'observaciones' => $this->faker->optional()->paragraph(),
        ];
    }
}

