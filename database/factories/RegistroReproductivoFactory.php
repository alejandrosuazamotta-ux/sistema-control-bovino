<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RegistroReproductivo>
 */
class RegistroReproductivoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fechaEvento = $this->faker->dateTimeBetween('-1 year', 'now');
        
        return [
            'id_vaca' => \App\Models\Vaca::factory(),
            'tipo_evento' => $this->faker->randomElement(['Inseminación', 'Celo', 'Palpación', 'Parto']),
            'fecha_evento' => $fechaEvento,
            'resultado_palpacion' => $this->faker->optional()->randomElement(['Positivo', 'Negativo', 'Inconcluso']),
            'tiempo_gestacion_dias' => $this->faker->optional()->numberBetween(200, 290),
            'fecha_probable_parto' => function (array $attributes) use ($fechaEvento) {
                if ($attributes['tipo_evento'] === 'Inseminación' && isset($attributes['tiempo_gestacion_dias'])) {
                    return Carbon::parse($fechaEvento)->addDays($attributes['tiempo_gestacion_dias']);
                }
                return null;
            },
            'especialista' => $this->faker->optional()->name(),
            'dias_abiertos' => $this->faker->optional()->numberBetween(30, 200),
            'observaciones' => $this->faker->optional()->paragraph(),
            'id_personal' => \App\Models\Personal::factory(),
        ];
    }
}

