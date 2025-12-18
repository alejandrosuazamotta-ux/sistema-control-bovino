<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vaca>
 */
class VacaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'codigo' => $this->faker->unique()->bothify('V###'),
            'raza' => $this->faker->randomElement(['Holstein', 'Jersey', 'Angus', 'Brahman']),
            'fecha_nacimiento' => $this->faker->dateTimeBetween('-10 years', '-1 year'),
            'peso_kg' => $this->faker->numberBetween(300, 700),
            'estado_salud' => $this->faker->randomElement(['Saludable', 'En tratamiento', 'Recuperación']),
            'estado_reproductivo' => $this->faker->randomElement(['Preñada', 'Lactancia', 'Secado', 'En celo']),
            'id_potrero' => null,
        ];
    }
}
