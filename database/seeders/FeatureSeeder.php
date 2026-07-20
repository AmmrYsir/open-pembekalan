<?php

namespace Database\Seeders;

use App\Support\FeatureRegistry;
use Illuminate\Database\Seeder;
use Laravel\Pennant\Feature;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (FeatureRegistry::all() as $feature) {
            if ($feature['default_active']) {
                Feature::activateForEveryone($feature['key']);
            } else {
                Feature::deactivateForEveryone($feature['key']);
            }
        }
    }
}
