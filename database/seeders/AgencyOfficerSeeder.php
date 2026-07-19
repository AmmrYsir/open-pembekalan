<?php

namespace Database\Seeders;

use App\Models\AgencyOfficer;
use Illuminate\Database\Seeder;

class AgencyOfficerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AgencyOfficer::factory()->randomizeExistingAgency()->count(10)->create();
    }
}
