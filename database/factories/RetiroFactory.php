<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Retiro>
 */
class RetiroFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fechaInicio = $this->faker->dateTimeBetween('-1 month', 'now');
        $fechaFin = (clone $fechaInicio)->modify('+' . $this->faker->numberBetween(7, 30) . ' days');
        
        return [
            'id_vaca' => \App\Models\Vaca::factory(),
            'tipo_retiro' => $this->faker->randomElement(['Ordeño', 'Producción']),
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'motivo' => $this->faker->sentence(),
            'observaciones' => $this->faker->optional()->paragraph(),
        ];
    }
}
