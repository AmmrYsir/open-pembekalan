<?php

use App\Models\Acquisition;
use App\Models\User;
use App\States\Acquisition\Draft;
use App\States\Acquisition\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('authenticated user can view dedicated acquisition show page', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $acquisition = Acquisition::create([
        'project_number' => 'PRJ-2026-999',
        'project_name' => 'Full Page Acquisition Test',
    ]);

    $response = $this->actingAs($user)->get(route('acquisition.show', $acquisition));

    $response->assertOk();
    $response->assertSee('Full Page Acquisition Test');
    $response->assertSee('PRJ-2026-999');
});

test('acquisition show livewire component supports tabs and editing', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $acquisition = Acquisition::create([
        'project_number' => 'PRJ-2026-888',
        'project_name' => 'Tabbed Component Test',
    ]);

    Livewire::actingAs($user)
        ->test('acquisition.show', ['acquisition' => $acquisition])
        ->assertSet('activeTab', 'project-info')
        ->call('setTab', 'committee')
        ->assertSet('activeTab', 'committee')
        ->call('setTab', 'technical-checklist')
        ->assertSet('activeTab', 'technical-checklist')
        ->call('setTab', 'financial-checklist')
        ->assertSet('activeTab', 'financial-checklist')
        ->call('setTab', 'project-info')
        ->call('enableEdit')
        ->assertSet('isEditing', true)
        ->set('form.project_name', 'Updated Project Name')
        ->set('form.type', 'SEBUTHARGA')
        ->set('form.method', 'BEKALAN')
        ->call('save')
        ->assertSet('isEditing', false);

    expect($acquisition->fresh()->project_name)->toBe('Updated Project Name');
});

test('acquisition show livewire component supports adding custom checklist items and supplier preview', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $acquisition = Acquisition::create([
        'project_number' => 'PRJ-2026-666',
        'project_name' => 'Checklist Builder Test',
    ]);

    Livewire::actingAs($user)
        ->test('acquisition.show', ['acquisition' => $acquisition])
        ->call('openAddItemModal', 'technical')
        ->assertSet('showAddItemModal', true)
        ->set('newItemTitle', 'Custom Spec Document')
        ->set('newItemDesc', 'Custom spec instructions for vendor')
        ->set('newItemInputType', 'file_download_upload')
        ->set('newItemTemplateFilename', 'Custom_Spec_Template.pdf')
        ->call('saveChecklistItem')
        ->assertSet('showAddItemModal', false)
        ->assertSee('Custom Spec Document')
        ->call('$toggle', 'previewSupplierMode')
        ->assertSet('previewSupplierMode', true)
        ->assertSee('Custom_Spec_Template.pdf');
});

test('acquisition show livewire component supports state transition', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $acquisition = Acquisition::create([
        'project_number' => 'PRJ-2026-777',
        'project_name' => 'Transition Test',
    ]);

    Livewire::actingAs($user)
        ->test('acquisition.show', ['acquisition' => $acquisition])
        ->call('transitionTo', Verified::class)
        ->assertHasNoErrors();

    expect($acquisition->fresh()->status)->toBeInstanceOf(Verified::class);
});
