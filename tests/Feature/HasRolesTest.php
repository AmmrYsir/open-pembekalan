<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;

beforeEach(function () {
    $this->adminRole = Role::create([
        'slug' => 'admin',
        'name' => 'Administrator',
        'description' => 'System admin',
        'is_active' => true,
        'is_hidden' => false,
    ]);

    $this->editorRole = Role::create([
        'slug' => 'editor',
        'name' => 'Content Editor',
        'description' => 'Can edit content',
        'is_active' => true,
        'is_hidden' => false,
    ]);

    $this->superAdminRole = Role::create([
        'slug' => 'superadmin',
        'name' => 'Super Administrator',
        'description' => 'Super admin with full access',
        'is_active' => true,
        'is_hidden' => false,
    ]);
});

test('user can be assigned role by slug, Role model, or array', function () {
    $user = User::factory()->create();

    $user->assignRole('admin');
    expect($user->hasRole('admin'))->toBeTrue();

    $user->assignRole($this->editorRole);
    expect($user->hasRole('editor'))->toBeTrue();

    expect($user->getRoleSlugs()->all())->toEqualCanonicalizing(['admin', 'editor']);
});

test('assigning nonexistent role slug throws exception', function () {
    $user = User::factory()->create();

    expect(fn () => $user->assignRole('nonexistent'))
        ->toThrow(Exception::class, "Role 'nonexistent' not found.");
});

test('user role removal and syncing works properly', function () {
    $user = User::factory()->create();
    $user->assignRole('admin', 'editor');

    expect($user->hasAllRoles(['admin', 'editor']))->toBeTrue();

    $user->removeRole('admin');
    expect($user->hasRole('admin'))->toBeFalse();
    expect($user->hasRole('editor'))->toBeTrue();

    $user->syncRoles('superadmin');
    expect($user->hasRole('editor'))->toBeFalse();
    expect($user->hasRole('superadmin'))->toBeTrue();
});

test('role checking methods: hasRole, hasAnyRole, hasAllRoles, unlessRole, hasExactRoles', function () {
    $user = User::factory()->create();
    $user->assignRole('admin', 'editor');

    // hasRole
    expect($user->hasRole('admin'))->toBeTrue();
    expect($user->hasRole(['admin', 'superadmin']))->toBeTrue();

    // hasAnyRole
    expect($user->hasAnyRole(['editor', 'nonexistent_slug']))->toBeTrue();
    expect($user->hasAnyRole(['superadmin']))->toBeFalse();

    // hasAllRoles
    expect($user->hasAllRoles(['admin', 'editor']))->toBeTrue();
    expect($user->hasAllRoles(['admin', 'superadmin']))->toBeFalse();

    // unlessRole
    expect($user->unlessRole('superadmin'))->toBeTrue();
    expect($user->unlessRole('admin'))->toBeFalse();

    // hasExactRoles
    expect($user->hasExactRoles(['editor', 'admin']))->toBeTrue();
    expect($user->hasExactRoles(['admin']))->toBeFalse();
});

test('isSuperAdmin helper works correctly', function () {
    $user = User::factory()->create();
    expect($user->isSuperAdmin())->toBeFalse();

    $user->assignRole('superadmin');
    expect($user->isSuperAdmin())->toBeTrue();
});

test('getRoleSlugs and getRoleNames return correct collections', function () {
    $user = User::factory()->create();
    $user->assignRole('admin', 'editor');

    expect($user->getRoleSlugs()->all())->toEqualCanonicalizing(['admin', 'editor']);
    expect($user->getRoleNames()->all())->toEqualCanonicalizing(['Administrator', 'Content Editor']);
});

test('eloquent query scopes withRole and withoutRole work', function () {
    $userAdmin = User::factory()->create();
    $userAdmin->assignRole('admin');

    $userEditor = User::factory()->create();
    $userEditor->assignRole('editor');

    $adminUsers = User::withRole('admin')->get();
    expect($adminUsers)->toHaveCount(1)
        ->and($adminUsers->first()->id)->toBe($userAdmin->id);

    $nonAdminUsers = User::withoutRole('admin')->get();
    expect($nonAdminUsers->pluck('id'))->toContain($userEditor->id)
        ->and($nonAdminUsers->pluck('id'))->not->toContain($userAdmin->id);
});

test('blade directives @hasRole, @hasAnyRole, @hasAllRoles, @unlessRole work correctly', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $this->actingAs($user);

    $hasRoleBlade = Blade::render('@hasRole("admin") YES @else NO @endhasRole');
    expect(trim($hasRoleBlade))->toBe('YES');

    $roleDirectiveBlade = Blade::render('@role("admin") YES @else NO @endrole');
    expect(trim($roleDirectiveBlade))->toBe('YES');

    $hasAnyRoleBlade = Blade::render('@hasAnyRole(["editor", "admin"]) YES @else NO @endhasAnyRole');
    expect(trim($hasAnyRoleBlade))->toBe('YES');

    $hasAllRolesBlade = Blade::render('@hasAllRoles(["admin", "editor"]) YES @else NO @endhasAllRoles');
    expect(trim($hasAllRolesBlade))->toBe('NO');

    $unlessRoleBlade = Blade::render('@unlessRole("editor") YES @else NO @endunlessRole');
    expect(trim($unlessRoleBlade))->toBe('YES');
});

test('gate allows role checking with role: prefix', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');

    $this->actingAs($user);

    expect(Gate::allows('role:admin'))->toBeTrue();
    expect(Gate::allows('role:editor'))->toBeFalse();
});
