<?php

namespace Database\Seeders;

use App\Models\VotType;
use Illuminate\Database\Seeder;

class VotTypeSeeder extends Seeder
{
    public function run(): void
    {
        VotType::factory()->count(5)->create();
    }
}
