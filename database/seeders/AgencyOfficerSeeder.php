<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\AgencyOfficer;

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
