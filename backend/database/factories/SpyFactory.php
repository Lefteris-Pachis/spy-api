<?php

namespace Database\Factories;

use App\Models\Spy;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpyFactory extends Factory
{
    protected $model = Spy::class;

    public function definition()
    {
        return [
            'name' => $this->faker->firstName(),
            'surname' => $this->faker->lastName(),
            'agency' => $this->faker->randomElement(['MI6', 'KGB', 'CIA']),
            'country_of_operation' => $this->faker->country(),
            'date_of_birth' => $this->faker->date('Y-m-d', '-30 years'),
            'date_of_death' => '',
        ];
    }
}
