<?php

namespace Database\Seeders;

use App\Models\Agency;
use Illuminate\Database\Seeder;

class SubagencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Agency::all() as $agency) {
            $subagencyCount = rand(1, 5); // Random number of subagencies for each agency
            for ($i = 0; $i < $subagencyCount; $i++) {
                $agency->subagencies()->create([
					'uuid' => fake()->uuid(),
                    'code' => fake()->unique()->bothify('A##'),
                    'name' => fake()->company(),
                    'is_active' => true,
                ]);
            }
        }
    }
}
