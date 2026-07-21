<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('user can link and switch between multiple accounts', function () {
    $primaryUser = User::factory()->create(['email' => 'primary@example.com']);
    $secondaryUser = User::factory()->create(['email' => 'secondary@example.com', 'password' => Hash::make('secret123')]);

    $primaryUser->linkAccount($secondaryUser);
    $primaryUser->markSwitchVerified($secondaryUser);

    expect($primaryUser->canSwitchTo($secondaryUser))->toBeTrue()
        ->and($secondaryUser->canSwitchTo($primaryUser))->toBeTrue();

    $this->actingAs($primaryUser);

    Livewire::test('account-switcher')
        ->call('initiateSwitch', $secondaryUser->id);

    expect(auth()->id())->toBe($secondaryUser->id);
});

test('user can unlink an account', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $user1->linkAccount($user2);
    expect($user1->canSwitchTo($user2))->toBeTrue();

    $user1->unlinkAccount($user2);
    expect($user1->canSwitchTo($user2))->toBeFalse();
});

test('account switcher component allows linking new account with valid password', function () {
    $user1 = User::factory()->create(['email' => 'user1@example.com']);
    $user2 = User::factory()->create(['email' => 'user2@example.com', 'password' => Hash::make('password123')]);

    Livewire::actingAs($user1)
        ->test('auth.link-account-form')
        ->set('email', 'user2@example.com')
        ->set('password', 'password123')
        ->call('linkAccount')
        ->assertHasNoErrors();

    expect($user1->canSwitchTo($user2))->toBeTrue();
});

test('profile info component displays user information correctly', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('user.profile-info', ['user' => $user])
        ->assertSet('full_name', $user->name)
        ->assertSet('email', $user->email);
});
