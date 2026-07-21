<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Pennant\Feature;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('experimental user renders account switcher feature while regular user gets standard user card', function () {
    $experimentalUser = User::factory()->create([
        'is_experimental_user' => true,
    ]);

    $regularUser = User::factory()->create([
        'is_experimental_user' => false,
    ]);

    expect(Feature::for($experimentalUser)->active('linked-accounts'))->toBeTrue()
        ->and(Feature::for($regularUser)->active('linked-accounts'))->toBeFalse();
});

test('link account page is accessible for authenticated users and renders link account form', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/link-account');

    $response->assertStatus(200);
    $response->assertSeeLivewire('auth.link-account-form');
});

test('link account form prevents linking the currently logged in account', function () {
    $user = User::factory()->create(['email' => 'active@example.com', 'password' => Hash::make('password123')]);

    Livewire::actingAs($user)
        ->test('auth.link-account-form')
        ->set('email', 'active@example.com')
        ->set('password', 'password123')
        ->call('linkAccount')
        ->assertHasErrors(['credentials_error' => 'You are already logged in to this account.']);
});

test('link account form prevents linking an already linked account', function () {
    $user1 = User::factory()->create(['email' => 'user1@example.com']);
    $user2 = User::factory()->create(['email' => 'user2@example.com', 'password' => Hash::make('password123')]);

    $user1->linkAccount($user2);

    Livewire::actingAs($user1)
        ->test('auth.link-account-form')
        ->set('email', 'user2@example.com')
        ->set('password', 'password123')
        ->call('linkAccount')
        ->assertHasErrors(['credentials_error' => 'This account is already linked to your account switcher.']);
});

test('link account form successfully links new account with valid credentials', function () {
    $primaryUser = User::factory()->create(['email' => 'primary@example.com']);
    $secondaryUser = User::factory()->create([
        'email' => 'secondary@example.com',
        'password' => Hash::make('secret123'),
    ]);

    Livewire::actingAs($primaryUser)
        ->test('auth.link-account-form')
        ->set('email', 'secondary@example.com')
        ->set('password', 'secret123')
        ->call('linkAccount')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    expect($primaryUser->canSwitchTo($secondaryUser))->toBeTrue()
        ->and($primaryUser->isSwitchVerified($secondaryUser))->toBeTrue();
});

test('switching to unverified account opens 2nd security confirmation modal', function () {
    $primaryUser = User::factory()->create(['email' => 'primary@example.com']);
    $secondaryUser = User::factory()->create([
        'email' => 'secondary@example.com',
        'password' => Hash::make('password123'),
    ]);

    $primaryUser->linkAccount($secondaryUser);
    $primaryUser->clearSwitchVerification($secondaryUser);

    Livewire::actingAs($primaryUser)
        ->test('account-switcher')
        ->call('initiateSwitch', $secondaryUser->id)
        ->assertSet('showConfirmModal', true)
        ->assertSet('pendingSwitchUserId', $secondaryUser->id);
});

test('confirming with invalid password fails security confirmation', function () {
    $primaryUser = User::factory()->create(['email' => 'primary@example.com']);
    $secondaryUser = User::factory()->create([
        'email' => 'secondary@example.com',
        'password' => Hash::make('correctpassword'),
    ]);

    $primaryUser->linkAccount($secondaryUser);
    $primaryUser->clearSwitchVerification($secondaryUser);

    Livewire::actingAs($primaryUser)
        ->test('account-switcher')
        ->call('initiateSwitch', $secondaryUser->id)
        ->set('confirm_password', 'wrongpassword')
        ->call('confirmAndSwitch')
        ->assertHasErrors(['confirm_password'])
        ->assertSet('showConfirmModal', true);

    expect(auth()->id())->toBe($primaryUser->id);
});

test('confirming with correct password verifies ownership and switches account', function () {
    $primaryUser = User::factory()->create(['email' => 'primary@example.com']);
    $secondaryUser = User::factory()->create([
        'email' => 'secondary@example.com',
        'password' => Hash::make('correctpassword'),
    ]);

    $primaryUser->linkAccount($secondaryUser);
    $primaryUser->clearSwitchVerification($secondaryUser);

    Livewire::actingAs($primaryUser)
        ->test('account-switcher')
        ->call('initiateSwitch', $secondaryUser->id)
        ->set('confirm_password', 'correctpassword')
        ->call('confirmAndSwitch')
        ->assertHasNoErrors()
        ->assertSet('showConfirmModal', false);

    expect(auth()->id())->toBe($secondaryUser->id)
        ->and(session('account_switch_verified_'.$primaryUser->id))->not()->toBeNull();
});

test('verified account permits direct switching with sliding activity session renewal', function () {
    $primaryUser = User::factory()->create(['email' => 'primary@example.com']);
    $secondaryUser = User::factory()->create(['email' => 'secondary@example.com']);

    $primaryUser->linkAccount($secondaryUser);
    $primaryUser->markSwitchVerified($secondaryUser);

    expect($primaryUser->isSwitchVerified($secondaryUser))->toBeTrue();

    Livewire::actingAs($primaryUser)
        ->test('account-switcher')
        ->call('initiateSwitch', $secondaryUser->id)
        ->assertSet('showConfirmModal', false);

    expect(auth()->id())->toBe($secondaryUser->id);
});

test('inactivity beyond timeout flushes verification session and requires 2nd confirmation again', function () {
    $primaryUser = User::factory()->create(['email' => 'primary@example.com']);
    $secondaryUser = User::factory()->create(['email' => 'secondary@example.com']);

    $primaryUser->linkAccount($secondaryUser);

    // Simulate activity timestamp 20 minutes ago (beyond 15-minute timeout)
    session(['account_switch_verified_'.$secondaryUser->id => now()->subMinutes(20)->timestamp]);

    expect($primaryUser->isSwitchVerified($secondaryUser, timeoutMinutes: 15))->toBeFalse()
        ->and(session('account_switch_verified_'.$secondaryUser->id))->toBeNull();

    Livewire::actingAs($primaryUser)
        ->test('account-switcher')
        ->call('initiateSwitch', $secondaryUser->id)
        ->assertSet('showConfirmModal', true);
});

test('user can unlink an account from switcher component', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $user1->linkAccount($user2);
    $user1->markSwitchVerified($user2);

    Livewire::actingAs($user1)
        ->test('account-switcher')
        ->call('unlinkAccount', $user2->id);

    expect($user1->canSwitchTo($user2))->toBeFalse()
        ->and(session('account_switch_verified_'.$user2->id))->toBeNull();
});

test('profile linked accounts component renders linked accounts and allows unlinking', function () {
    $user1 = User::factory()->create(['name' => 'User One']);
    $user2 = User::factory()->create(['name' => 'User Two']);

    $user1->linkAccount($user2);

    Livewire::actingAs($user1)
        ->test('user.linked-accounts', ['user' => $user1])
        ->assertSee('User Two')
        ->call('unlinkAccount', $user2->id);

    expect($user1->canSwitchTo($user2))->toBeFalse();
});
