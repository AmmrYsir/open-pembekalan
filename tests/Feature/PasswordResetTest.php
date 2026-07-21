<?php

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

test('forgot password page can be rendered', function () {
    $response = $this->get(route('password.request'));

    $response->assertOk()
        ->assertSee('Forgot Password');
});

test('password reset link can be requested', function () {
    Notification::fake();

    $user = User::factory()->create();

    $response = $this->post(route('password.email'), [
        'email' => $user->email,
    ]);

    $response->assertSessionHas('status');

    Notification::assertSentTo($user, ResetPasswordNotification::class);
});

test('reset password page can be rendered with token', function () {
    $user = User::factory()->create();
    $token = Password::createToken($user);

    $response = $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]));

    $response->assertOk()
        ->assertSee('Set New Password');
});

test('password can be reset with valid token and dispatches SystemNotification', function () {
    Notification::fake();

    $user = User::factory()->create();
    $token = Password::createToken($user);

    $response = $this->post(route('password.update'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-secure-password',
        'password_confirmation' => 'new-secure-password',
    ]);

    $response->assertRedirect(route('login'));

    expect(Hash::check('new-secure-password', $user->fresh()->password))->toBeTrue();

    Notification::assertSentTo(
        $user,
        SystemNotification::class,
        fn ($notification) => $notification->title === 'Password Reset Successful'
    );
});

test('password cannot be reset with invalid token', function () {
    $user = User::factory()->create();

    $response = $this->post(route('password.update'), [
        'token' => 'invalid-token',
        'email' => $user->email,
        'password' => 'new-secure-password',
        'password_confirmation' => 'new-secure-password',
    ]);

    $response->assertSessionHasErrors('email');

    expect(Hash::check('new-secure-password', $user->fresh()->password))->toBeFalse();
});
