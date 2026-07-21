<?php

use App\Models\User;
use App\Support\FeatureRegistry;
use Database\Seeders\FeatureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('features page renders successfully for authenticated user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/features');

    $response->assertStatus(200);
    $response->assertSee('Feature Management');
});

test('features page redirects guest user to login', function () {
    $response = $this->get('/features');

    $response->assertRedirect('/login');
});

test('feature seeder populates pennant default features', function () {
    $this->seed(FeatureSeeder::class);

    foreach (FeatureRegistry::all() as $feature) {
        if ($feature['key'] === 'experimental-features') {
            continue;
        }

        expect(Feature::active($feature['key']))->toBe($feature['default_active']);
    }
});

test('feature table component can toggle feature status', function () {
    $this->seed(FeatureSeeder::class);
    $user = User::factory()->create();

    $this->actingAs($user);

    // Toggle OFF system-notifications (initially true)
    Livewire::test('feature.table')
        ->call('toggleFeature', 'system-notifications');

    expect(Feature::active('system-notifications'))->toBeFalse();

    // Toggle ON system-notifications
    Livewire::test('feature.table')
        ->call('toggleFeature', 'system-notifications');

    expect(Feature::active('system-notifications'))->toBeTrue();
});

test('experimental user flag resolves experimental-features correctly', function () {
    $experimentalUser = User::factory()->create([
        'is_experimental_user' => true,
    ]);

    $regularUser = User::factory()->create([
        'is_experimental_user' => false,
    ]);

    // When deactivated globally
    Feature::deactivateForEveryone('experimental-features');

    expect(Feature::for($experimentalUser)->active('experimental-features'))->toBeTrue()
        ->and(Feature::for($regularUser)->active('experimental-features'))->toBeFalse();

    // When activated globally for everyone
    Feature::activateForEveryone('experimental-features');

    expect(Feature::for($experimentalUser)->active('experimental-features'))->toBeTrue()
        ->and(Feature::for($regularUser)->active('experimental-features'))->toBeTrue();
});
