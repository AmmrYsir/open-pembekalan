<?php

namespace Database\Factories;

use App\Models\Agency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Agency>
 */
class AgencyFactory extends Factory
{
    public function definition(): array
    {
        return [
			'uuid' => fake()->uuid(),
            'code' => $this->faker->unique()->bothify('A##'),
            'name' => $this->faker->company(),
            'is_active' => $this->faker->boolean(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
}
