<?php

use App\Models\User;

test('system management routes render successfully for authenticated users', function (string $url) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get($url);

    $response->assertStatus(200);
})->with([
    '/suppliers',
    '/agencies',
    '/subagencies',
    '/agency-officers',
    '/committees',
    '/mof-categories',
    '/mof-subcategories',
    '/mof-codes',
    '/states',
    '/vot-types',
]);

test('system management routes redirect guest users to login', function (string $url) {
    $response = $this->get($url);

    $response->assertRedirect('/login');
})->with([
    '/suppliers',
    '/agencies',
    '/subagencies',
    '/agency-officers',
    '/committees',
    '/mof-categories',
    '/mof-subcategories',
    '/mof-codes',
    '/states',
    '/vot-types',
]);
