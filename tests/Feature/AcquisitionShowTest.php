<?php

use App\Models\Acquisition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('authenticated user can view dedicated acquisition show page with long title', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $acquisition = Acquisition::create([
        'project_number' => 'PRJ-2026-999',
        'project_name' => 'SEBUT HARGA PERKHIDMATAN KAWALAN KESELAMATAN (TANPA SENJATA API) BAGI TEMPOH DUA (2) TAHUN DI SELADANG CAGE (GIMNASIUM ANGKAT BERAT, TINJU, GIMRAMA, TERJUN), MAJLIS SUKAN NEGERI PERAK',
    ]);

    $response = $this->actingAs($user)->get(route('acquisition.show', $acquisition));

    $response->assertOk();
    $response->assertSee('SEBUT HARGA PERKHIDMATAN KAWALAN KESELAMATAN');
    $response->assertSee('PRJ-2026-999');
});

test('acquisition show livewire component supports title expansion for long titles', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $acquisition = Acquisition::create([
        'project_number' => 'PRJ-2026-777',
        'project_name' => 'SEBUT HARGA PERKHIDMATAN KAWALAN KESELAMATAN (TANPA SENJATA API) BAGI TEMPOH DUA (2) TAHUN DI SELADANG CAGE (GIMNASIUM ANGKAT BERAT, TINJU, GIMRAMA, TERJUN), MAJLIS SUKAN NEGERI PERAK',
    ]);

    Livewire::actingAs($user)
        ->test('acquisition.show', ['acquisition' => $acquisition])
        ->assertSet('expandFullTitle', false)
        ->call('toggleFullTitle')
        ->assertSet('expandFullTitle', true);
});

test('acquisition show livewire component supports segmented tab bar and locked evaluation tabs', function () {
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
        ->assertSee('Meeting Notices Schedule')
        ->call('setTab', 'technical-checklist')
        ->assertSet('activeTab', 'technical-checklist')
        ->assertSee('Akses Tab Penilaian Dikunci')
        ->call('toggleEvaluationLock')
        ->assertSet('isEvaluationUnlocked', true)
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
        ->assertSee('Document for Procurement');
});

test('issuing meeting notice auto unlocks evaluation tabs', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $acquisition = Acquisition::create([
        'project_number' => 'PRJ-2026-333',
        'project_name' => 'Meeting Notice Lock Test',
    ]);

    Livewire::actingAs($user)
        ->test('acquisition.show', ['acquisition' => $acquisition])
        ->assertSet('isEvaluationUnlocked', false)
        ->call('setTab', 'committee')
        ->call('openMeetingNoticeModal')
        ->set('noticeCommitteeType', 'JK Spesifikasi')
        ->set('noticeSubject', 'Mesyuarat Semakan Spesifikasi')
        ->call('saveMeetingNotice')
        ->assertSet('isEvaluationUnlocked', true);
});
