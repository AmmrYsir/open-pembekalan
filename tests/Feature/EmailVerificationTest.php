<?php

use App\Models\Agency;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

test('unverified user is redirected to email verification notice from protected route', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertRedirect(route('verification.notice'));
});

test('email verification notice page can be rendered', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get(route('verification.notice'));

    $response->assertOk()
        ->assertSee('Verify Your Email');
});

test('email can be verified using signed URL and triggers SystemNotification', function () {
    $user = User::factory()->unverified()->create();

    Event::fake([Verified::class]);
    Notification::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class);
    $response->assertRedirect(route('dashboard'))->assertSessionHas('verified', true);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

test('email verification event sends SystemNotification to user', function () {
    $user = User::factory()->unverified()->create();

    Notification::fake();

    event(new Verified($user));

    Notification::assertSentTo(
        $user,
        SystemNotification::class,
        fn ($notification) => $notification->title === 'Email Verified'
    );
});

test('user cannot verify email with invalid hash', function () {
    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('invalid-email@example.com')]
    );

    $this->actingAs($user)->get($verificationUrl);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('verification link can be resent', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->post(route('verification.send'));

    $response->assertSessionHas('status', 'verification-link-sent');

    Notification::assertSentTo($user, VerifyEmailNotification::class);
});

test('supplier registration sends email verification notification', function () {
    Notification::fake();

    Livewire::test('auth.register-form')
        ->set('step', 5)
        ->set('ssm_no', 'SSM-VERIFY-123')
        ->set('name', 'Jane Supplier')
        ->set('email', 'jane.supplier@example.com')
        ->set('company_name', 'Supplier Co')
        ->set('ssm_type', 'ROC: SENDIRIAN BERHAD')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertRedirect(route('verification.notice'));

    $user = User::where('email', 'jane.supplier@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->hasVerifiedEmail())->toBeFalse();

    Notification::assertSentTo($user, VerifyEmailNotification::class);
});

test('agency officer creation sends email verification notification', function () {
    Notification::fake();

    $agency = Agency::create(['name' => 'Ministry of Technology', 'code' => 'MOT']);

    Livewire::test('agency-officer.⚡drawer')
        ->call('open', 'create')
        ->set('name', 'Officer Bob')
        ->set('email', 'officer.bob@agency.gov.my')
        ->set('agency_id', $agency->id)
        ->set('title', 'Encik')
        ->call('save')
        ->assertHasNoErrors();

    $user = User::where('email', 'officer.bob@agency.gov.my')->first();
    expect($user)->not->toBeNull();
    expect($user->hasVerifiedEmail())->toBeFalse();

    Notification::assertSentTo($user, VerifyEmailNotification::class);
});
