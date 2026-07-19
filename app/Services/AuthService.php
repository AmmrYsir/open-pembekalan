<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportRedirects\Redirector;

class AuthService
{
    public function login(string $email, string $password): Redirector
    {
        if (auth()->attempt(['email' => $email, 'password' => $password])) {
            session()->regenerate();

            return redirect()->route('dashboard');
        }

        throw ValidationException::withMessages([
            'credentials_error' => 'The provided credentials do not match our records.',
        ]);
    }
}
