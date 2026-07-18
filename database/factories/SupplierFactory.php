<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'company_name' => $this->faker->company,
            'ssm_type' => $this->faker->randomElement(['Type A', 'Type B', 'Type C']),
            'ssm_number' => $this->faker->unique()->numerify('SSM-#####'),
            'old_registration_number' => $this->faker->optional()->numerify('OLD-#####'),
            'mobile_no' => $this->faker->phoneNumber,
            'telephone_no' => $this->faker->optional()->phoneNumber,
            'operating_area' => $this->faker->city,
            'established_date' => $this->faker->date(),
            'website_link' => $this->faker->optional()->url,
            'cert_verified_code' => $this->faker->optional()->regexify('[A-Z0-9]{10}'),
            'tax_reference_number' => $this->faker->optional()->numerify('TAX-#####'),
            'cjcp_reference_number' => $this->faker->optional()->numerify('CJCP-#####'),
            'ssm_start_date' => $this->faker->optional()->date(),
            'ssm_expiry_date' => $this->faker->optional()->date(),
            'kpb_active_date' => $this->faker->optional()->date(),
            'kpb_expiry_date' => $this->faker->optional()->date(),
            'cidb_active_date' => $this->faker->optional()->date(),
            'cidb_bumiputera_active_date' => $this->faker->optional()->date(),
            'cidb_expiry_date' => $this->faker->optional()->date(),
            'cidb_bumiputera_expiry_date' => $this->faker->optional()->date(),
            'mof_active_date' => $this->faker->optional()->date(),
            'mof_bumiputera_active_date' => $this->faker->optional()->date(),
            'mof_expiry_date' => $this->faker->optional()->date(),
            'mof_bumiputera_expiry_date' => $this->faker->optional()->date(),
        ];
    }
}
