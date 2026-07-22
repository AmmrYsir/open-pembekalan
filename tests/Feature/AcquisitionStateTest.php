<?php

use App\Models\Acquisition;
use App\Models\Assignment;
use App\Models\User;
use App\States\Acquisition\Baru;
use App\States\Acquisition\DisahkanDaftar;
use App\States\Acquisition\DisemakButiranKuiri;
use App\States\Acquisition\DisemakDokumenSebutHargaTender;
use App\States\Acquisition\MenungguPengesahanButiran;
use App\States\Acquisition\MenungguPengesahanDaftar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\ModelStates\Exceptions\TransitionNotFound;

uses(RefreshDatabase::class);

test('acquisition defaults to BARU state', function () {
    $acquisition = Acquisition::create([
        'project_number' => 'PRJ-2026-001',
        'project_name' => 'Projek Bekalan Komputer',
    ]);

    expect($acquisition->status)->toBeInstanceOf(Baru::class);
    expect($acquisition->status->getValue())->toBe('BARU');
    expect($acquisition->status->label())->toBe('BARU');
    expect($acquisition->status->color())->toBe('slate');
});

test('acquisition transitions through pipeline and updates single assignment record and user_ids', function () {
    $user = User::factory()->create();

    $acquisition = Acquisition::create([
        'project_number' => 'PRJ-2026-002',
        'project_name' => 'Projek Perabot Pejabat',
        'user_id' => $user->id,
    ]);

    // Transition BARU -> MENUNGGU_PENGESAHAN_DAFTAR with updated user_ids [2, 3]
    $acquisition->status->transitionTo(MenungguPengesahanDaftar::class, userIds: [2, 3]);

    expect($acquisition->fresh()->status)->toBeInstanceOf(MenungguPengesahanDaftar::class);
    expect($acquisition->fresh()->status->getValue())->toBe('MENUNGGU_PENGESAHAN_DAFTAR');

    // Verify single Assignment updated with new status and assigned user_ids
    expect(Assignment::where('acquisition_id', $acquisition->id)->count())->toBe(1);
    $assignment = Assignment::where('acquisition_id', $acquisition->id)->first();
    expect($assignment->status)->toBe('MENUNGGU_PENGESAHAN_DAFTAR');
    expect($assignment->title)->toBe('MENUNGGU PENGESAHAN DAFTAR');
    expect($assignment->user_ids)->toBe([2, 3]);

    // Next transition MENUNGGU_PENGESAHAN_DAFTAR -> DISAHKAN_DAFTAR with user_ids [4]
    $acquisition->status->transitionTo(DisahkanDaftar::class, userIds: [4]);

    expect($acquisition->fresh()->status)->toBeInstanceOf(DisahkanDaftar::class);
    expect(Assignment::where('acquisition_id', $acquisition->id)->count())->toBe(1);
    $assignment->refresh();
    expect($assignment->status)->toBe('DISAHKAN_DAFTAR');
    expect($assignment->user_ids)->toBe([4]);
});

test('kuiri transitions allow returning to review state', function () {
    $acquisition = Acquisition::create([
        'project_number' => 'PRJ-2026-003',
        'project_name' => 'Projek Sistem Rangkaian',
    ]);

    $acquisition->status->transitionTo(MenungguPengesahanDaftar::class);
    $acquisition->status->transitionTo(DisahkanDaftar::class);
    $acquisition->status->transitionTo(MenungguPengesahanButiran::class);

    // Transition into KUIRI
    $acquisition->status->transitionTo(DisemakButiranKuiri::class);
    expect($acquisition->fresh()->status)->toBeInstanceOf(DisemakButiranKuiri::class);
    expect($acquisition->fresh()->status->label())->toBe('KUIRI');

    // Transition out of KUIRI back to MENUNGGU_PENGESAHAN_BUTIRAN
    $acquisition->status->transitionTo(MenungguPengesahanButiran::class);
    expect($acquisition->fresh()->status)->toBeInstanceOf(MenungguPengesahanButiran::class);
});

test('invalid transition throws exception', function () {
    $acquisition = Acquisition::create([
        'project_number' => 'PRJ-2026-004',
        'project_name' => 'Projek Invalid Transition',
    ]);

    // BARU direct to DISEMAK_DOKUMEN_SEBUTHARGA_TENDER should be disallowed
    expect(fn () => $acquisition->status->transitionTo(DisemakDokumenSebutHargaTender::class))
        ->toThrow(TransitionNotFound::class);
});
