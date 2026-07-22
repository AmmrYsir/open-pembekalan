<?php

use App\Models\Acquisition;
use App\Models\Assignment;
use App\Models\User;
use App\States\Acquisition\Approved;
use App\States\Acquisition\CommitteeAppointment;
use App\States\Acquisition\Draft;
use App\States\Acquisition\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('acquisition defaults to DRAFT state', function () {
    $acquisition = Acquisition::create([
        'project_number' => 'PRJ-2026-001',
        'project_name' => 'Projek Bekalan Komputer',
    ]);

    expect($acquisition->status)->toBeInstanceOf(Draft::class);
    expect($acquisition->status->getValue())->toBe('DRAFT');
    expect($acquisition->status->label())->toBe('DRAF');
    expect($acquisition->status->color())->toBe('slate');
});

test('acquisition transitions through pipeline and updates single assignment record', function () {
    $user = User::factory()->create();

    $acquisition = Acquisition::create([
        'project_number' => 'PRJ-2026-002',
        'project_name' => 'Projek Perabot Pejabat',
        'user_id' => $user->id,
    ]);

    // Transition DRAFT -> VERIFIED
    $acquisition->status->transitionTo(Verified::class);

    expect($acquisition->fresh()->status)->toBeInstanceOf(Verified::class);
    expect($acquisition->fresh()->status->getValue())->toBe('VERIFIED');

    // Next transition VERIFIED -> APPROVED
    $acquisition->status->transitionTo(Approved::class);

    expect($acquisition->fresh()->status)->toBeInstanceOf(Approved::class);

    // Next transition APPROVED -> COMMITTEE_APPOINTMENT
    $acquisition->status->transitionTo(CommitteeAppointment::class);

    expect($acquisition->fresh()->status)->toBeInstanceOf(CommitteeAppointment::class);
});
