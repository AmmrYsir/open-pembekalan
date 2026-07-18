<?php

namespace Database\Factories;

use App\Models\VotType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VotType>
 */
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
