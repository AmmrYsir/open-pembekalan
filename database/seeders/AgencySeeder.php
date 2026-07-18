<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Agency;

class AgencySeeder extends Seeder
{
    public function run(): void
    {
		Agency::factory()->active()->count(10)->create();
    }
}
