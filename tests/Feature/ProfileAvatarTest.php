<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
    $this->user = User::factory()->create(['avatar_url' => null]);
    $this->actingAs($this->user);
});

it('uploads a valid jpeg and stores it at avatars/{uuid}.jpg', function () {
    $file = UploadedFile::fake()->image('photo.jpg', 200, 200);

    Livewire::test('user.profile-avatar')
        ->set('avatar', $file)
        ->assertHasNoErrors('avatar');

    $path = 'avatars/'.$this->user->uuid.'.jpg';
    Storage::disk('public')->assertExists($path);
    expect($this->user->fresh()->avatar_url)->toBe($path);
});

it('uploads a valid png and stores it with .png extension', function () {
    $file = UploadedFile::fake()->image('photo.png', 200, 200)->mimeType('image/png');

    Livewire::test('user.profile-avatar')
        ->set('avatar', $file)
        ->assertHasNoErrors('avatar');

    $path = 'avatars/'.$this->user->uuid.'.png';
    Storage::disk('public')->assertExists($path);
    expect($this->user->fresh()->avatar_url)->toBe($path);
});

it('replaces an existing avatar and overwrites the old file', function () {
    $existingPath = 'avatars/'.$this->user->uuid.'.jpg';
    Storage::disk('public')->put($existingPath, 'old-content');
    $this->user->update(['avatar_url' => $existingPath]);

    $file = UploadedFile::fake()->image('new.jpg', 200, 200);

    Livewire::test('user.profile-avatar')
        ->set('avatar', $file)
        ->assertHasNoErrors('avatar');

    Storage::disk('public')->assertExists($existingPath);
    expect(Storage::disk('public')->get($existingPath))->not->toBe('old-content');
    expect($this->user->fresh()->avatar_url)->toBe($existingPath);
});

it('rejects non-image files', function () {
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    Livewire::test('user.profile-avatar')
        ->set('avatar', $file)
        ->assertHasErrors('avatar');

    expect($this->user->fresh()->avatar_url)->toBeNull();
    Storage::disk('public')->assertDirectoryEmpty('avatars');
});

it('rejects images over 2 MB', function () {
    $file = UploadedFile::fake()->image('huge.jpg')->size(2500); // 2500 KB

    Livewire::test('user.profile-avatar')
        ->set('avatar', $file)
        ->assertHasErrors('avatar');

    expect($this->user->fresh()->avatar_url)->toBeNull();
});

it('removes the avatar and deletes the file from storage', function () {
    $path = 'avatars/'.$this->user->uuid.'.jpg';
    Storage::disk('public')->put($path, 'some-content');
    $this->user->update(['avatar_url' => $path]);

    Livewire::test('user.profile-avatar')
        ->call('removeAvatar')
        ->assertHasNoErrors();

    Storage::disk('public')->assertMissing($path);
    expect($this->user->fresh()->avatar_url)->toBeNull();
});

it('does nothing when removing an avatar that was never set', function () {
    Livewire::test('user.profile-avatar')
        ->call('removeAvatar')
        ->assertHasNoErrors();

    expect($this->user->fresh()->avatar_url)->toBeNull();
});

it('does not store a file when the user is unauthenticated', function () {
    auth()->logout();

    $file = UploadedFile::fake()->image('photo.jpg');

    Livewire::test('user.profile-avatar')
        ->set('avatar', $file);

    Storage::disk('public')->assertDirectoryEmpty('avatars');
});
