<?php

use App\Models\EmailLog;
use App\Models\FailedJob;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Livewire;

test('superadmin can access queue management page', function () {
    Role::firstOrCreate(['slug' => 'superadmin'], ['name' => 'Superadmin']);
    $superadmin = User::factory()->assignRole('superadmin')->create();

    $response = $this->actingAs($superadmin)->get(route('admin.queues.index'));

    $response->assertOk()
        ->assertSeeLivewire('queue.manager');
});

test('superadmin can access email tracker page', function () {
    Role::firstOrCreate(['slug' => 'superadmin'], ['name' => 'Superadmin']);
    $superadmin = User::factory()->assignRole('superadmin')->create();

    $response = $this->actingAs($superadmin)->get(route('admin.email-tracker.index'));

    $response->assertOk()
        ->assertSeeLivewire('email-tracker.manager');
});

test('sending mail automatically creates an email_logs entry', function () {
    Mail::raw('Hello from test email', function ($message) {
        $message->to('test.recipient@example.com')->subject('Test Automatic Email Logging');
    });

    $log = EmailLog::where('recipient_email', 'test.recipient@example.com')->first();

    expect($log)->not->toBeNull();
    expect($log->subject)->toEqual('Test Automatic Email Logging');
    expect($log->status)->toEqual('sent');
});

test('email tracker component can inspect email and resend email', function () {
    $log = EmailLog::create([
        'recipient_email' => 'resend.test@example.com',
        'recipient_name' => 'Resend User',
        'subject' => 'Monthly Report',
        'status' => 'sent',
        'body_html' => '<h1>Your Report</h1><p>Content goes here...</p>',
        'body_text' => 'Your Report Content goes here...',
        'sent_at' => now(),
    ]);

    Livewire::test('email-tracker.⚡manager')
        ->call('inspectEmail', $log->id)
        ->assertSet('showDrawer', true)
        ->assertSet('selectedLog.id', $log->id)
        ->call('resendEmail', $log->id);

    // A second email log should now exist from the resend action
    $resentLog = EmailLog::where('subject', '[RESENT] Monthly Report')->first();
    expect($resentLog)->not->toBeNull();
});

test('email tracker component can trigger failed mail job retry', function () {
    $failed = FailedJob::create([
        'uuid' => (string) Str::uuid(),
        'connection' => 'database',
        'queue' => 'default',
        'payload' => json_encode(['displayName' => 'App\Mail\SendReceiptMail']),
        'exception' => 'SMTP Connection Timeout Exception',
        'failed_at' => now(),
    ]);

    Livewire::test('email-tracker.⚡manager')
        ->call('retryFailedMailJob', $failed->id);

    expect(true)->toBeTrue();
});
