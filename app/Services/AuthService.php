<?php

namespace App\Services;

class AuthService
{
    public function login(string $email, string $password): void
    {
        if (! auth()->attempt(['email' => $email, 'password' => $password])) {
            throw new \Exception('Invalid credentials');
        }

        session()->regenerate();

        dd('Login successful');
    }
}
