<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AccessLog>
 */
class AccessLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
  
        return [

            'state' => true,
            'status' => $this->faker->randomElement(['Authorizado', 'No Authorizado']),
            'breakPoint' => $this->faker->randomElement(['RFID', 'FR']),

            'person_id' => null,
        ];
    }
}
