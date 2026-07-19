<?php

namespace Database\Factories;

use App\Models\AgencyOfficer;
use App\Models\Agency;
use App\Models\Subagency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AgencyOfficer>
 */
class AgencyOfficerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
			'user_id' => \App\Models\User::factory(),
			'agency_id' => \App\Models\Agency::factory(),
			'subagency_id' => \App\Models\Subagency::factory(),
			'title' => $this->faker->jobTitle(),
			'nric' => $this->faker->numerify('##########'),
			'position' => $this->faker->jobTitle(),
			'mobile_number' => $this->faker->phoneNumber(),
			'home_phone_number' => $this->faker->phoneNumber(),
			'created_by' => \App\Models\User::factory(),
			'updated_by' => \App\Models\User::factory(),
        ];
    }

	public function randomizeExistingAgency()
	{
		return $this->state(function (array $attributes) {
			$agency = Agency::inRandomOrder()->first();
			$subagency = Subagency::where('agency_id', $agency->id)->inRandomOrder()->first();

			return [
				'agency_id' => $agency->id,
				'subagency_id' => $subagency ? $subagency->id : null,
			];
		});
	}
}
