<?php

namespace Database\Seeders;

use App\Actions\CreateSuperadmin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        app(CreateSuperadmin::class)->execute();

        $this->call([
            VotTypeSeeder::class,
            AgencySeeder::class,
            SubagencySeeder::class,
            AgencyOfficerSeeder::class,
            StateSeeder::class,
        ]);
    }
}
