<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('authenticated user can view advertisement index page', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $response = $this->actingAs($user)->get(route('advertisement'));

    $response->assertOk();
    $response->assertSee('Procurement Advertisements');
});

test('advertisement table livewire component renders advertisement notices in english', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    Livewire::actingAs($user)
        ->test('advertisement.table')
        ->assertSee('ADV-2026-001')
        ->assertSee('TENDER FOR SECURITY GUARD SERVICES')
        ->set('search', 'ADV-2026-002')
        ->assertSee('ADV-2026-002');
});

test('advertisement drawer can be triggered via open-advertisement-drawer event', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    Livewire::actingAs($user)
        ->test('advertisement.drawer')
        ->assertSet('isOpen', false)
        ->dispatch('open-advertisement-drawer')
        ->assertSet('isOpen', true)
        ->set('title', 'Test New Tender Notice')
        ->call('save')
        ->assertSet('isOpen', false);
});

test('advertisement show supports corrigendum and cancellation workflows', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    Livewire::actingAs($user)
        ->test('advertisement.show', ['advertisementId' => '1'])
        ->assertSet('showCorrigendumModal', false)
        ->call('openCorrigendumModal')
        ->assertSet('showCorrigendumModal', true)
        ->set('corrigendumReason', 'Closing date extension due to administrative request')
        ->call('issueCorrigendum')
        ->assertSet('showCorrigendumModal', false)
        ->assertSee('OFFICIAL CORRIGENDUM / ADDENDUM NOTICE ISSUED')
        ->call('openCancellationModal')
        ->assertSet('showCancellationModal', true)
        ->set('cancelReason', 'Project budget restructured by treasury')
        ->call('cancelAdvertisement')
        ->assertSet('showCancellationModal', false)
        ->assertSee('THIS ADVERTISEMENT NOTICE HAS BEEN OFFICIALLY CANCELLED');
});
