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

test('advertisement show component renders 6 tabs including mof codes tab and supports tab switching', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $response = $this->actingAs($user)->get(route('advertisement.show', 1));
    $response->assertOk();

    Livewire::actingAs($user)
        ->test('advertisement.show', ['advertisementId' => '1'])
        ->assertSet('activeTab', 'details')
        ->call('setTab', 'mof-codes')
        ->assertSet('activeTab', 'mof-codes')
        ->assertSee('220801')
        ->assertSee('Mandatory Code (Wajib)')
        ->call('toggleMofRequirement', 'mof_1')
        ->assertSee('Optional Code (Pilihan)')
        ->call('setTab', 'documents')
        ->assertSet('activeTab', 'documents')
        ->assertSee('Official_Tender_Advertisement_Notice.pdf')
        ->call('setTab', 'briefing')
        ->assertSet('activeTab', 'briefing')
        ->assertSee('Briefing')
        ->call('setTab', 'submissions')
        ->assertSet('activeTab', 'submissions')
        ->assertSee('MEGA SECURITY SERVICES SDN BHD')
        ->call('setTab', 'preview')
        ->assertSet('activeTab', 'preview')
        ->assertSee('Supplier Public Portal View Simulation');
});
