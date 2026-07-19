<?php

namespace Database\Factories;

use App\Models\State;
use App\Actions\GetAbbreviation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<State>
 */
class StateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
		$state = $this->faker->state();

        return [
			'code' => GetAbbreviation::execute($state),
			'shortname' => $state,
			'fullname' => $state,
			'capital' => $this->faker->city(),
        ];
    }
}
