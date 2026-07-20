<?php

use App\Models\Agency;
use App\Models\Role;
use App\Models\Subagency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('user can link and switch between multiple accounts', function () {
    $primaryUser = User::factory()->create(['email' => 'primary@example.com']);
    $secondaryUser = User::factory()->create(['email' => 'secondary@example.com', 'password' => 'secret123']);

    $primaryUser->linkAccount($secondaryUser);

    expect($primaryUser->canSwitchTo($secondaryUser))->toBeTrue()
        ->and($secondaryUser->canSwitchTo($primaryUser))->toBeTrue();

    $this->actingAs($primaryUser);

    Livewire::test('sidebar-footer-user-card')
        ->call('switchAccount', $secondaryUser->id);

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

test('sidebar footer card allows linking new account with valid password', function () {
    $user1 = User::factory()->create(['email' => 'user1@example.com']);
    $user2 = User::factory()->create(['email' => 'user2@example.com', 'password' => bcrypt('password123')]);

    Livewire::actingAs($user1)
        ->test('sidebar-footer-user-card')
        ->set('link_email', 'user2@example.com')
        ->set('link_password', 'password123')
        ->call('linkNewAccount')
        ->assertHasNoErrors();

    expect($user1->canSwitchTo($user2))->toBeTrue();
});

test('standard user cannot edit agency dropdown in profile info component', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('user.profile-info', ['user' => $user])
        ->assertSet('canEditAgency', false)
        ->assertDontSee('Select Agency...');
});

test('superadmin or admin user can edit agency and subagency in profile info component', function () {
    $adminRole = Role::create([
        'slug' => 'admin',
        'name' => 'Administrator',
        'is_active' => true,
        'is_hidden' => false,
    ]);

    $admin = User::factory()->create();
    $admin->assignRole($adminRole);

    $agency = Agency::create(['code' => 'MOF', 'name' => 'Kementerian Kewangan', 'is_active' => true]);
    $subagency = Subagency::create(['agency_id' => $agency->id, 'code' => 'PERO', 'name' => 'Bahagian Perolehan', 'is_active' => true]);

    Livewire::actingAs($admin)
        ->test('user.profile-info', ['user' => $admin])
        ->assertSet('canEditAgency', true)
        ->set('agency_id', $agency->id)
        ->set('subagency_id', $subagency->id)
        ->call('updateInformation')
        ->assertHasNoErrors();

    expect($admin->fresh()->agencyOfficer->agency_id)->toBe($agency->id)
        ->and($admin->fresh()->agencyOfficer->subagency_id)->toBe($subagency->id);
});
