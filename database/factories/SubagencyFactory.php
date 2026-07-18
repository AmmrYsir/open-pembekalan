<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\Subagency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subagency>
 */
class SubagencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'agency_id' => Agency::factory(),
            'code' => fake()->unique()->word(),
            'name' => fake()->name(),
            'is_active' => true,
        ];
    }
}
