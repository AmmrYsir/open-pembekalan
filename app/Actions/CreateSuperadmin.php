<?php

namespace App\Actions;

use App\Models\User;

class CreateSuperadmin
{
    public function execute(): void
    {
        User::factory()->changeName('Superadmin')->changeEmail('superadmin@openpembekalan.com')->changePassword('secret')->experimentalUser()->assignRole('superadmin')->create();
    }
}
