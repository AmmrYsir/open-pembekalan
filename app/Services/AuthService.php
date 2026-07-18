<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

class AuthService
{
    public function login(string $email, string $password): void
    {
        if (auth()->attempt(['email' => $email, 'password' => $password])) {
            session()->regenerate();
            dd('Login successful');
        }

        throw ValidationException::withMessages([
            'credentials_error' => 'The provided credentials do not match our records.',
        ]);
    }
}
