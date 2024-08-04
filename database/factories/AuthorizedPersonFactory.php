<?php

namespace Database\Factories;

use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Person>
 */
class PersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Person::class;
    public function definition()
    {
        return [
            'typeofDocument' => $this->faker->randomElement(['DNI', 'Passport']),
            'documentNumber' => $this->faker->unique()->numberBetween(10000000, 99999999),
            'names' => $this->faker->firstName,
            'fatherSurname' => $this->faker->lastName,
            'motherSurname' => $this->faker->lastName,
            'dateBirth' => $this->faker->date(),
            'email' => $this->faker->unique()->safeEmail,
            'telephone' => $this->faker->phoneNumber,
            'status' => 'Active',
            'state' => true,
        ];
    }
}
