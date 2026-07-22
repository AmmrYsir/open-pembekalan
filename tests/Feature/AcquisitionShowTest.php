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

test('acquisition show livewire component supports 8 tabs including document for procurement', function () {
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
        ->assertSee('Primary Item #1')
        ->call('setTab', 'financial-checklist')
        ->assertSet('activeTab', 'financial-checklist')
        ->assertSee('Primary Item #1')
        ->call('setTab', 'technical-spec')
        ->assertSet('activeTab', 'technical-spec')
        ->assertSee('Technical Specification')
        ->call('setTab', 'financial-pricelist')
        ->assertSet('activeTab', 'financial-pricelist')
        ->assertSee('Bill of Quantities')
        ->call('setTab', 'scoring')
        ->assertSet('activeTab', 'scoring')
        ->assertSee('Overall Scoring & Evaluation Matrix')
        ->call('setTab', 'documents')
        ->assertSet('activeTab', 'documents')
        ->assertSee('Document for Procurement')
        ->assertSee('Supplier Tender Statement')
        ->assertSee('Sample Bidder Declaration Letter (Integrity Pact)')
        ->assertSee('Sample Letter of Acceptance (LOA)');
});

test('acquisition show component supports printable procurement document preview modal', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $acquisition = Acquisition::create([
        'project_number' => 'PRJ-2026-555',
        'project_name' => 'Procurement Document Test',
    ]);

    Livewire::actingAs($user)
        ->test('acquisition.show', ['acquisition' => $acquisition])
        ->call('setTab', 'documents')
        ->call('openProcurementDocModal', 'doc_tender_stmt')
        ->assertSet('showProcurementDocModal', true)
        ->assertSee('Supplier Tender Statement')
        ->call('closeProcurementDocModal')
        ->assertSet('showProcurementDocModal', false);
});
