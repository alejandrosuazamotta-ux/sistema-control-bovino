<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Personal>
 */
class PersonalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->name(),
            'rol' => $this->faker->randomElement(['Veterinario', 'Técnico', 'Ordeñador', 'Administrador']),
            'fecha_contratacion' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'email' => $this->faker->unique()->safeEmail(),
            'telefono' => $this->faker->optional()->phoneNumber(),
            'direccion' => $this->faker->optional()->address(),
        ];
    }
}

