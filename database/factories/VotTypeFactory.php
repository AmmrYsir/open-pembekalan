<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VotTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('A##'),
            'name' => $this->faker->company(),
        ];
    }
}
