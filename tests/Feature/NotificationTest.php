<?php

use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('it creates a system notification database record', function () {
    $this->user->notify(new SystemNotification(
        title: 'Test Title',
        message: 'Test Message',
        action_url: '/test-url',
        icon: 'success'
    ));

    $this->assertDatabaseHas('notifications', [
        'notifiable_id' => $this->user->id,
        'notifiable_type' => User::class,
    ]);

    $notification = $this->user->notifications()->first();
    expect($notification->data['title'])->toBe('Test Title')
        ->and($notification->data['message'])->toBe('Test Message')
        ->and($notification->data['action_url'])->toBe('/test-url')
        ->and($notification->data['icon'])->toBe('success');
});

test('notification bell displays correct unread counts and lists notifications', function () {
    $this->user->notify(new SystemNotification('Alert 1', 'Message 1'));
    $this->user->notify(new SystemNotification('Alert 2', 'Message 2'));

    $this->actingAs($this->user);

    Livewire::test('notification-bell')
        ->assertSet('unreadCount', 2)
        ->assertSee('Alert 1')
        ->assertSee('Alert 2');
});

test('notification bell can mark all notifications as read', function () {
    $this->user->notify(new SystemNotification('Alert 1', 'Message 1'));
    $this->user->notify(new SystemNotification('Alert 2', 'Message 2'));

    $this->actingAs($this->user);

    expect($this->user->unreadNotifications()->count())->toBe(2);

    Livewire::test('notification-bell')
        ->call('markAllAsRead')
        ->assertSet('unreadCount', 0);

    expect($this->user->unreadNotifications()->count())->toBe(0);
});

test('notification bell can read and redirect', function () {
    $this->user->notify(new SystemNotification('Link Alert', 'Message', '/custom-target-route'));
    $notification = $this->user->notifications()->first();

    $this->actingAs($this->user);

    Livewire::test('notification-bell')
        ->call('readAndRedirect', $notification->id)
        ->assertRedirect('/custom-target-route');

    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('notification list displays, filters, and deletes notifications', function () {
    $this->user->notify(new SystemNotification('Unread Alert', 'Unread Message'));
    $this->user->notify(new SystemNotification('Read Alert', 'Read Message'));

    $unreadNotification = $this->user->notifications()->where('data->title', 'Unread Alert')->first();
    $readNotification = $this->user->notifications()->where('data->title', 'Read Alert')->first();

    $readNotification->markAsRead();

    $this->actingAs($this->user);

    // Initial load defaults to 'unread' filter
    Livewire::test('notification-list')
        ->assertSet('filter', 'unread')
        ->assertSee('Unread Alert')
        ->assertDontSee('Read Alert')

        // Switch to 'read' filter
        ->call('setFilter', 'read')
        ->assertSee('Read Alert')
        ->assertDontSee('Unread Alert')

        // Mark the unread one as read
        ->call('setFilter', 'unread')
        ->call('markAsRead', $unreadNotification->id)
        ->assertDontSee('Unread Alert')

        // Mark it as unread again
        ->call('setFilter', 'read')
        ->call('markAsUnread', $unreadNotification->id)
        ->assertDontSee('Unread Alert') // should be gone from 'read' tab

        // Delete notification
        ->call('setFilter', 'unread')
        ->assertSee('Unread Alert')
        ->call('deleteNotification', $unreadNotification->id)
        ->assertDontSee('Unread Alert');

    $this->assertDatabaseMissing('notifications', [
        'id' => $unreadNotification->id,
    ]);
});

test('notification list can clear all notifications', function () {
    $this->user->notify(new SystemNotification('Alert 1', 'Msg'));
    $this->user->notify(new SystemNotification('Alert 2', 'Msg'));

    $this->actingAs($this->user);

    expect($this->user->notifications()->count())->toBe(2);

    Livewire::test('notification-list')
        ->call('clearAll')
        ->assertSee('No unread notifications');

    expect($this->user->notifications()->count())->toBe(0);
});

test('notification list can simulate alerts', function () {
    $this->actingAs($this->user);

    expect($this->user->notifications()->count())->toBe(0);

    Livewire::test('notification-list')
        ->call('simulateNotification');

    expect($this->user->notifications()->count())->toBe(4);
});
