<?php

namespace App\Actions;

use App\Models\User;

class CreateSuperadmin
{
    public function execute(): void
    {
        User::factory()->fill([
            'name' => 'Superadmin',
            'email' => 'superadmin@openpembekalan.com',
        ])->changePassword('secret')->experimentalUser()->create();
    }
}
