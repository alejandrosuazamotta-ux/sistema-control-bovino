<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notificacion>
 */
class NotificacionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tipo' => $this->faker->randomElement(['preparto', 'celo', 'destete', 'retiro_activo', 'stock_bajo']),
            'nivel' => $this->faker->randomElement(['urgente', 'advertencia', 'informacion']),
            'titulo' => $this->faker->sentence(),
            'mensaje' => $this->faker->paragraph(),
            'entidad_tipo' => $this->faker->randomElement([
                \App\Models\Vaca::class,
                \App\Models\RegistroReproductivo::class,
                \App\Models\Retiro::class,
            ]),
            'entidad_id' => $this->faker->numberBetween(1, 100),
            'leida' => false,
            'estado' => 'pendiente',
        ];
    }
}

