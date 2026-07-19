<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    private array $roles = [
        [
            'slug' => 'superadmin',
            'name' => 'Super Admin',
            'description' => 'Administrator role with full access to the system.',
            'is_active' => true,
            'is_hidden' => false,
        ],
        [
            'slug' => 'officer',
            'name' => 'Officer',
            'description' => 'Officer role with limited access to the system.',
            'is_active' => true,
            'is_hidden' => false,
        ],
        [
            'slug' => 'supplier',
            'name' => 'Supplier',
            'description' => 'Supplier role with access to supplier-related features.',
            'is_active' => true,
            'is_hidden' => false,
        ],
    ];

    public function run(): void
    {
        Role::upsert($this->roles, ['slug']);
    }
}
