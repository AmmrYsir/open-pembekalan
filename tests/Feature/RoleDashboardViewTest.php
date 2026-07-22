<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('officer user sees officer dashboard view', function () {
    $role = Role::create([
        'slug' => 'officer',
        'name' => 'Officer',
        'is_active' => true,
        'is_hidden' => false,
    ]);

    $user = User::factory()->create();
    $user->assignRole('officer');

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee("Here is what's happening with the acquisition projects today");
});

test('supplier user sees supplier dashboard view', function () {
    $role = Role::create([
        'slug' => 'supplier',
        'name' => 'Supplier',
        'is_active' => true,
        'is_hidden' => false,
    ]);

    $user = User::factory()->create();
    $user->assignRole('supplier');

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Manage your active bids, monitor open tender opportunities');
});

test('superadmin user sees superadmin dashboard view', function () {
    $role = Role::create([
        'slug' => 'superadmin',
        'name' => 'Super Admin',
        'is_active' => true,
        'is_hidden' => false,
    ]);

    $user = User::factory()->create();
    $user->assignRole('superadmin');

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Overview of system health, active user roles, agency configurations');
});
