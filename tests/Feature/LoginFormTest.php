<?php

use App\Services\AuthService;
use Livewire\Livewire;

use function Pest\Laravel\mock;

it('renders the login page successfully', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSeeLivewire('auth.login-form');
});

it('requires email and password fields', function () {
    Livewire::test('auth.login-form')
        ->call('login')
        ->assertHasErrors([
            'email' => 'required',
            'password' => 'required',
        ]);
});

it('requires a valid email address format', function () {
    Livewire::test('auth.login-form')
        ->set('email', 'invalid-email')
        ->set('password', 'secret')
        ->call('login')
        ->assertHasErrors([
            'email' => 'email',
        ]);
});

it('displays an error for invalid credentials', function () {
    // Runs the actual AuthService, which attempts login and fails because user does not exist
    Livewire::test('auth.login-form')
        ->set('email', 'wrong@openpembekalan.com')
        ->set('password', 'incorrect-password')
        ->call('login')
        ->assertHasErrors(['credentials_error']);
});

it('submits credentials to AuthService on successful form post', function () {
    $mock = mock(AuthService::class);
    $mock->shouldReceive('login')
        ->once()
        ->with('test@openpembekalan.com', 'password123');

    app()->instance(AuthService::class, $mock);

    Livewire::test('auth.login-form')
        ->set('email', 'test@openpembekalan.com')
        ->set('password', 'password123')
        ->call('login')
        ->assertHasNoErrors();
});
