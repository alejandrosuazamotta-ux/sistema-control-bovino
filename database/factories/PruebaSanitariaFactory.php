<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PruebaSanitaria>
 */
class PruebaSanitariaFactory extends Factory
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
            'tipo_prueba' => $this->faker->randomElement(['Mastitis', 'Brucelosis', 'Tuberculosis']),
            'resultado' => $this->faker->randomElement(['Positivo', 'Negativo', 'Pendiente']),
            'fecha_prueba' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'restriccion_ordeño' => false,
            'inhabilitada' => false,
            'cerrada' => false,
            'observaciones' => $this->faker->optional()->paragraph(),
        ];
    }
}

