<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Agency;

class SubagencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach(Agency::all() as $agency) {
            $subagencyCount = rand(1, 5); // Random number of subagencies for each agency
            for ($i = 0; $i < $subagencyCount; $i++) {
                $agency->subagencies()->create([
                    'code' => $this->faker->unique()->bothify('A##'),
                    'name' => $this->faker->company(),
                    'is_active' => true,
                ]);
            }
        }
    }
}
