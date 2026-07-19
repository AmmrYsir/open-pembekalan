<?php

namespace Database\Factories;

use App\Enums\AcquisitionMethod;
use App\Enums\AcquisitionType;
use App\Models\Acquisition;
use App\Models\Agency;
use App\Models\Subagency;
use App\Models\VotType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Acquisition>
 */
class AcquisitionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
			'uuid' => fake()->uuid(),
            'type' => $this->faker->randomElement(AcquisitionType::values()),
            'method' => $this->faker->randomElement(AcquisitionMethod::values()),
            'project_number' => $this->faker->unique()->numerify('PN-#####'),
            'project_name' => $this->faker->sentence(32),
            'status' => $this->faker->randomElement([]),
            'provision_type' => $this->faker->randomElement([]),
            'submission_type' => $this->faker->randomElement([]),
            'vot_type_id' => VotType::factory(), // You can set this to a valid VotType ID if needed
            'tender_number' => $this->faker->unique()->numerify('TN-#####'),
            'siling_price' => $this->faker->randomFloat(2, 10000, 100000),
            'no_allocation_warrant' => $this->faker->unique()->numerify('NAW####-#####'),
            'agency_id' => Agency::factory(), // You can set this to a valid Agency ID if needed
            'subagency_id' => Subagency::factory(), // You can set this to a valid Subagency ID if needed
        ];
    }
}
