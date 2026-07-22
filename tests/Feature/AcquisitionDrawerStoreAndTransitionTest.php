<?php

use App\Models\Acquisition;
use App\Models\Sequence;
use App\Models\User;
use App\States\Acquisition\Approved;
use App\States\Acquisition\CommitteeAppointment;
use App\States\Acquisition\Draft;
use App\States\Acquisition\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Sequence::create([
        'slug' => 'project-number',
        'name' => 'Project Number',
        'format' => 'PN{Y}XXXXXX',
        'value' => 1,
    ]);
});

test('storing new acquisition via drawer succeeds', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('acquisition.drawer')
        ->call('open', 'create')
        ->set('form.type', 'SEBUTHARGA')
        ->set('form.method', 'BEKALAN')
        ->set('form.project_name', 'Test System Procurement')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('mode', 'view');

    $acquisition = Acquisition::first();
    expect($acquisition)->not->toBeNull();
    expect($acquisition->project_name)->toBe('Test System Procurement');
    expect($acquisition->status)->toBeInstanceOf(Draft::class);
});

test('state pipeline transition via drawer transitions through configured states', function () {
    $user = User::factory()->create();

    $acquisition = Acquisition::create([
        'project_number' => 'PRJ-2026-0001',
        'project_name' => 'State Pipeline Test',
    ]);

    expect($acquisition->status)->toBeInstanceOf(Draft::class);

    // Transition Draft -> Verified
    Livewire::actingAs($user)
        ->test('acquisition.drawer')
        ->call('open', 'view', $acquisition->id)
        ->call('transitionTo', Verified::class)
        ->assertHasNoErrors();

    expect($acquisition->fresh()->status)->toBeInstanceOf(Verified::class);

    // Transition Verified -> Approved
    Livewire::actingAs($user)
        ->test('acquisition.drawer')
        ->call('open', 'view', $acquisition->id)
        ->call('transitionTo', Approved::class)
        ->assertHasNoErrors();

    expect($acquisition->fresh()->status)->toBeInstanceOf(Approved::class);

    // Transition Approved -> CommitteeAppointment
    Livewire::actingAs($user)
        ->test('acquisition.drawer')
        ->call('open', 'view', $acquisition->id)
        ->call('transitionTo', CommitteeAppointment::class)
        ->assertHasNoErrors();

    expect($acquisition->fresh()->status)->toBeInstanceOf(CommitteeAppointment::class);
});
