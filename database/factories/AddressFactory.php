<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'address_line_1' => $this->faker->streetAddress(),
            'address_line_2' => $this->faker->secondaryAddress(),
            'address_line_3' => $this->faker->city(),
            'postal_code' => $this->faker->postcode(),
            'district' => $this->faker->citySuffix(),
            'city' => $this->faker->city(),
            'state_id' => State::inRandomOrder()->first()->id,
        ];
    }

    public function assignTo($addressable)
    {
        return $this->state(function (array $attributes) use ($addressable) {
            return [
                'addressable_id' => $addressable->id,
                'addressable_type' => get_class($addressable),
            ];
        });
    }

    public function state(State $state)
    {
        return $this->state(function (array $attributes) use ($state) {
            return [
                'state_id' => $state->id,
            ];
        });
    }
}
