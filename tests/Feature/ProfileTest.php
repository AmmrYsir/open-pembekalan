<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('profile info component displays verified email badge in email suffix for verified user', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test('user.profile-info', ['user' => $user])
        ->assertSee('Verified');
});

test('profile info component displays unverified email badge in email suffix for unverified user', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    Livewire::actingAs($user)
        ->test('user.profile-info', ['user' => $user])
        ->assertSee('Unverified');
});

test('profile page displays fallback Procurement Officer badge when user has no assigned roles', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('profile'))
        ->assertOk()
        ->assertSee('Procurement Officer');
});

test('profile page displays single role badge when user has 1 role', function () {
    $role = Role::create([
        'slug' => 'manager',
        'name' => 'Department Manager',
        'description' => 'Manager role',
        'is_active' => true,
        'is_hidden' => false,
    ]);

    $user = User::factory()->create();
    $user->assignRole($role);

    $this->actingAs($user)
        ->get(route('profile'))
        ->assertOk()
        ->assertSee('Department Manager');
});

test('profile page displays group of role badges when user has multiple roles', function () {
    $role1 = Role::create([
        'slug' => 'officer',
        'name' => 'Procurement Officer',
        'is_active' => true,
        'is_hidden' => false,
    ]);
    $role2 = Role::create([
        'slug' => 'approver',
        'name' => 'Financial Approver',
        'is_active' => true,
        'is_hidden' => false,
    ]);

    $user = User::factory()->create();
    $user->assignRole($role1, $role2);

    $this->actingAs($user)
        ->get(route('profile'))
        ->assertOk()
        ->assertSee('Procurement Officer')
        ->assertSee('Financial Approver');
});

test('profile info component updates user name, email, and username with leading @ stripped', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'username' => 'olduser',
        'email' => 'old@example.com',
        'email_verified_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test('user.profile-info', ['user' => $user])
        ->assertSet('full_name', 'Old Name')
        ->assertSet('username', 'olduser')
        ->assertSet('email', 'old@example.com')
        ->set('full_name', 'New Name')
        ->set('username', '@newjohn_doe')
        ->set('email', 'old@example.com') // unchanged email
        ->call('updateInformation')
        ->assertHasNoErrors();

    $user->refresh();
    expect($user->name)->toBe('New Name')
        ->and($user->username)->toBe('newjohn_doe')
        ->and($user->email_verified_at)->not->toBeNull();
});

test('changing email unverifies user email and sets email_verified_at to null', function () {
    $user = User::factory()->create([
        'email' => 'verified@example.com',
        'email_verified_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test('user.profile-info', ['user' => $user])
        ->set('email', 'new_email@example.com')
        ->call('updateInformation')
        ->assertHasNoErrors();

    $user->refresh();
    expect($user->email)->toBe('new_email@example.com')
        ->and($user->email_verified_at)->toBeNull();
});

test('profile info component prevents duplicate usernames', function () {
    User::factory()->create(['username' => 'existing_user']);
    $user = User::factory()->create(['username' => 'my_username']);

    Livewire::actingAs($user)
        ->test('user.profile-info', ['user' => $user])
        ->set('username', '@existing_user')
        ->call('updateInformation')
        ->assertHasErrors(['username' => 'unique']);

    expect($user->fresh()->username)->toBe('my_username');
});
