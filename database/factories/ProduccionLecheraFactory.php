<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProduccionLechera>
 */
class ProduccionLecheraFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_vaca' => \App\Models\Vaca::factory(),
            'fecha' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'turno' => $this->faker->randomElement(['Mañana', 'Tarde', 'Noche']),
            'cantidad_leche' => $this->faker->randomFloat(2, 5, 25),
            'destino' => $this->faker->randomElement(['Venta', 'Consumo', 'Desecho']),
            'valor_unidad' => $this->faker->randomFloat(2, 1000, 3000),
            'valor_total' => function (array $attributes) {
                return $attributes['cantidad_leche'] * $attributes['valor_unidad'];
            },
            'excluida_por_retiro' => false,
            'excluida_por_sanidad' => false,
            'id_personal' => \App\Models\Personal::factory(),
            'observaciones' => $this->faker->optional()->sentence(),
        ];
    }
}
