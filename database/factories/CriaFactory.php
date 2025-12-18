<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cria>
 */
class CriaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_vaca_madre' => \App\Models\Vaca::factory(),
            'nombre_cria' => $this->faker->optional()->name(),
            'sexo' => $this->faker->randomElement(['Macho', 'Hembra']),
            'fecha_nacimiento' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'peso' => $this->faker->randomFloat(2, 20, 50),
            'concepcion' => $this->faker->randomElement(['Natural', 'Inseminación']),
            'estado_destete' => $this->faker->randomElement(['No destetado', 'Destetado', 'En proceso']),
            'observaciones' => $this->faker->optional()->paragraph(),
        ];
    }
}

