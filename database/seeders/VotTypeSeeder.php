<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\VotType;

class VotTypeSeeder extends Seeder
{
    public function run(): void
    {
		VotType::factory()->count(5)->create();
    }
}
