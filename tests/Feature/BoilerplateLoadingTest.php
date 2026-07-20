<?php

use App\Models\User;

test('boilerplate preview routes return successful response', function (string $route) {
    if (in_array($route, ['/dashboard', '/profile', '/agency', '/verify-email'])) {
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    $response = $this->get($route);
    $response->assertStatus(200);
})->with([
    '/',
    '/login',
    '/register',
    '/forgot-password',
    '/verify-email',
    '/dashboard',
    '/profile',
    '/404',
    '/403',
    '/agency',
    '/portal',
]);
