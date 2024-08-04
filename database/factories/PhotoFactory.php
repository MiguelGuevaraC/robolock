<?php

namespace Database\Factories;

use App\Models\Photo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Photo>
 */
class PhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
  

    public function definition()
    {
        return [
            'photoPath' => $this->faker->imageUrl(),
            'state' => true,
            'status' => 'Active',
            'authorized_person_id' => null
        ];
    }
}
