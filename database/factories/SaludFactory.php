<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Salud>
 */
class SaludFactory extends Factory
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
            'tipo_registro' => $this->faker->randomElement(['Prueba mastitis', 'Prueba Brucelosis', 'Prueba Tuberculosis', 'Tratamiento', 'Vacunación']),
            'tipo_prueba' => function (array $attributes) {
                if (str_contains($attributes['tipo_registro'], 'Prueba')) {
                    return str_replace('Prueba ', '', $attributes['tipo_registro']);
                }
                return null;
            },
            'resultado' => $this->faker->randomElement(['Positivo', 'Negativo', 'Pendiente']),
            'fecha' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'restriccion_ordeño' => false,
            'vaca_inhabilitada' => false,
            'id_personal' => \App\Models\Personal::factory(),
            'observaciones' => $this->faker->optional()->paragraph(),
        ];
    }
}
