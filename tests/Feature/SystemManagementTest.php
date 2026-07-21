<?php

use App\Models\User;

test('system management routes render successfully for authenticated users', function (string $url) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get($url);

    $response->assertStatus(200);
})->with([
    '/admin/features',
    '/admin/suppliers',
    '/admin/agencies',
    '/admin/subagencies',
    '/admin/agency-officers',
    '/admin/committees',
    '/admin/mof-categories',
    '/admin/mof-subcategories',
    '/admin/mof-codes',
    '/admin/states',
    '/admin/vot-types',
]);

test('system management routes redirect guest users to login', function (string $url) {
    $response = $this->get($url);

    $response->assertRedirect('/login');
})->with([
    '/admin/features',
    '/admin/suppliers',
    '/admin/agencies',
    '/admin/subagencies',
    '/admin/agency-officers',
    '/admin/committees',
    '/admin/mof-categories',
    '/admin/mof-subcategories',
    '/admin/mof-codes',
    '/admin/states',
    '/admin/vot-types',
]);
