<?php

namespace App\Traits;

use App\Models\Role;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait HasRoles
{
    /**
     * Get the roles associated with the user.
     *
     * @return BelongsToMany<Role, $this>
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    /**
     * Check if the user has a specific role or any of the given roles.
     *
     * @param  string|int|Role|array<int, mixed>|Collection<int, mixed>  $roles
     */
    public function hasRole(string|int|Role|array|Collection $roles): bool
    {
        return $this->hasAnyRole($roles);
    }

    /**
     * Check if the user has ANY of the given roles.
     *
     * @param  string|int|Role|array<int, mixed>|Collection<int, mixed>  $roles
     */
    public function hasAnyRole(string|int|Role|array|Collection $roles): bool
    {
        $targetSlugs = $this->parseRoleSlugs($roles);

        if (empty($targetSlugs)) {
            return false;
        }

        $userRoleSlugs = $this->getRoleSlugs();

        return $userRoleSlugs->intersect($targetSlugs)->isNotEmpty();
    }

    /**
     * Check if the user has ALL of the given roles.
     *
     * @param  string|int|Role|array<int, mixed>|Collection<int, mixed>  $roles
     */
    public function hasAllRoles(string|int|Role|array|Collection $roles): bool
    {
        $targetSlugs = $this->parseRoleSlugs($roles);

        if (empty($targetSlugs)) {
            return false;
        }

        $userRoleSlugs = $this->getRoleSlugs()->all();

        return count(array_diff($targetSlugs, $userRoleSlugs)) === 0;
    }

    /**
     * Check if the user does NOT have the specified role(s).
     *
     * @param  string|int|Role|array<int, mixed>|Collection<int, mixed>  $roles
     */
    public function unlessRole(string|int|Role|array|Collection $roles): bool
    {
        return ! $this->hasRole($roles);
    }

    /**
     * Check if the user has EXACTLY the given roles (and no others).
     *
     * @param  string|int|Role|array<int, mixed>|Collection<int, mixed>  $roles
     */
    public function hasExactRoles(string|int|Role|array|Collection $roles): bool
    {
        $targetSlugs = array_values(array_unique($this->parseRoleSlugs($roles)));
        $userRoleSlugs = array_values(array_unique($this->getRoleSlugs()->all()));

        sort($targetSlugs);
        sort($userRoleSlugs);

        return $targetSlugs === $userRoleSlugs;
    }

    /**
     * Helper to check if user is a Superadmin.
     */
    public function isSuperAdmin(string $superAdminSlug = 'superadmin'): bool
    {
        return $this->hasRole($superAdminSlug);
    }

    /**
     * Get collection of assigned role slugs.
     *
     * @return Collection<int, string>
     */
    public function getRoleSlugs(): Collection
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles->pluck('slug');
        }

        return $this->roles()->pluck('slug');
    }

    /**
     * Get collection of assigned role names.
     *
     * @return Collection<int, string>
     */
    public function getRoleNames(): Collection
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles->pluck('name');
        }

        return $this->roles()->pluck('name');
    }

    /**
     * Assign role(s) to the user.
     */
    public function assignRole(mixed ...$roles): static
    {
        $roleIds = $this->resolveRoleIds(...$roles);

        if (! empty($roleIds)) {
            $this->roles()->syncWithoutDetaching($roleIds);
            $this->unsetRelation('roles');
        }

        return $this;
    }

    /**
     * Remove role(s) from the user.
     */
    public function removeRole(mixed ...$roles): static
    {
        $roleIds = $this->resolveRoleIds(...$roles);

        if (! empty($roleIds)) {
            $this->roles()->detach($roleIds);
            $this->unsetRelation('roles');
        }

        return $this;
    }

    /**
     * Sync user roles with given list.
     */
    public function syncRoles(mixed ...$roles): static
    {
        $roleIds = $this->resolveRoleIds(...$roles);

        $this->roles()->sync($roleIds);
        $this->unsetRelation('roles');

        return $this;
    }

    /**
     * Scope query to users that have any of the given roles.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeWithRole(Builder $query, mixed ...$roles): Builder
    {
        $slugs = $this->parseRoleSlugs($roles);

        return $query->whereHas('roles', function (Builder $q) use ($slugs) {
            $q->whereIn('slug', $slugs);
        });
    }

    /**
     * Scope query to users that do not have any of the given roles.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeWithoutRole(Builder $query, mixed ...$roles): Builder
    {
        $slugs = $this->parseRoleSlugs($roles);

        return $query->whereDoesntHave('roles', function (Builder $q) use ($slugs) {
            $q->whereIn('slug', $slugs);
        });
    }

    /**
     * Resolve roles to an array of Role IDs.
     *
     * @return array<int, int>
     *
     * @throws Exception
     */
    protected function resolveRoleIds(mixed ...$roles): array
    {
        $flattened = collect($roles)->flatten();
        $ids = [];

        foreach ($flattened as $role) {
            if ($role instanceof Role) {
                $ids[] = $role->id;
            } elseif (is_numeric($role)) {
                $ids[] = (int) $role;
            } elseif (is_string($role)) {
                $found = Role::where('slug', $role)->first();
                if (! $found) {
                    throw new Exception("Role '{$role}' not found.");
                }
                $ids[] = $found->id;
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * Parse input roles into array of slug strings.
     *
     * @return array<int, string>
     */
    protected function parseRoleSlugs(mixed $roles): array
    {
        if ($roles instanceof Collection) {
            $roles = $roles->all();
        }

        if (! is_array($roles)) {
            $roles = [$roles];
        }

        $flattened = collect($roles)->flatten()->all();
        $slugs = [];

        foreach ($flattened as $role) {
            if ($role instanceof Role) {
                $slugs[] = $role->slug;
            } elseif (is_string($role)) {
                foreach (explode(',', $role) as $s) {
                    $trimmed = trim($s);
                    if ($trimmed !== '') {
                        $slugs[] = $trimmed;
                    }
                }
            }
        }

        return array_values(array_unique($slugs));
    }
}
