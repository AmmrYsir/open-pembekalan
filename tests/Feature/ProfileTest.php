<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('profile page displays verified email badge for verified user', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user)
        ->get(route('profile'))
        ->assertOk()
        ->assertSee('Verified Email');
});

test('profile page displays unverified email badge for unverified user', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $this->actingAs($user)
        ->get(route('profile'))
        ->assertOk()
        ->assertSee('Unverified Email');
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

test('profile page displays stacked badge when user has multiple roles', function () {
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
        ->assertSee('translate-x-1.5 translate-y-0.5', false);
});

test('profile info component updates user name, email, and username with leading @ stripped', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'username' => 'olduser',
        'email' => 'old@example.com',
    ]);

    Livewire::actingAs($user)
        ->test('user.profile-info', ['user' => $user])
        ->assertSet('full_name', 'Old Name')
        ->assertSet('username', 'olduser')
        ->assertSet('email', 'old@example.com')
        ->set('full_name', 'New Name')
        ->set('username', '@newjohn_doe')
        ->set('email', 'newjohn@example.com')
        ->call('updateInformation')
        ->assertHasNoErrors();

    $user->refresh();
    expect($user->name)->toBe('New Name')
        ->and($user->username)->toBe('newjohn_doe')
        ->and($user->email)->toBe('newjohn@example.com');
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
