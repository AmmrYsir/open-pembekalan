<?php

use App\Models\User;

test('boilerplate preview routes return successful response', function (string $route) {
    if (in_array($route, ['/dashboard', '/profile', '/agency', '/email/verify'])) {
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
    '/email/verify',
    '/dashboard',
    '/profile',
    '/404',
    '/403',
    '/agency',
    '/portal',
]);
