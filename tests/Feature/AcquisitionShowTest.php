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

test('acquisition show livewire component supports 7 tabs including scoring', function () {
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
        ->assertSee('Overall Scoring & Evaluation Matrix');
});

test('acquisition show component supports weightage ratios and primary checklist links', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $acquisition = Acquisition::create([
        'project_number' => 'PRJ-2026-555',
        'project_name' => 'Scoring & Weightage Test',
    ]);

    Livewire::actingAs($user)
        ->test('acquisition.show', ['acquisition' => $acquisition])
        ->call('setTab', 'scoring')
        ->set('techWeightageRatio', 80.0)
        ->set('finWeightageRatio', 20.0)
        ->assertSee('Ratio: 80% Technical : 20% Financial');
});
