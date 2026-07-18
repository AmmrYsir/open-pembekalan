<?php

test('boilerplate preview routes return successful response', function (string $route) {
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
]);
