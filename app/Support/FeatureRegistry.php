<?php

namespace App\Support;

class FeatureRegistry
{
    /**
     * Get all seeded application features with metadata.
     *
     * @return array<int, array{
     *     key: string,
     *     name: string,
     *     description: string,
     *     scope: string,
     *     scope_label: string,
     *     default_active: bool,
     *     icon: string
     * }>
     */
    public static function all(): array
    {
        return [
            [
                'key' => 'experimental-features',
                'name' => 'Experimental Features Lab',
                'description' => 'Unlocks early-access & experimental features for users with the is_experimental_user flag enabled.',
                'scope' => 'experimental',
                'scope_label' => 'Experimental Users',
                'default_active' => true,
                'icon' => 'beaker',
            ],
            [
                'key' => 'system-notifications',
                'name' => 'System Notifications',
                'description' => 'Enables real-time notification bell dropdown in header and notification center dashboard.',
                'scope' => 'global',
                'scope_label' => 'All Authenticated Users',
                'default_active' => true,
                'icon' => 'bell',
            ],
            [
                'key' => 'linked-accounts',
                'name' => 'Linked Accounts & Switching',
                'description' => 'Enables linking multiple user accounts for quick switching without re-authenticating.',
                'scope' => 'user',
                'scope_label' => 'User Profiles',
                'default_active' => false,
                'icon' => 'user-group',
            ],
        ];
    }

    /**
     * Find metadata for a specific feature key.
     *
     * @return array{key: string, name: string, description: string, scope: string, scope_label: string, default_active: bool, icon: string}|null
     */
    public static function find(string $key): ?array
    {
        foreach (static::all() as $feature) {
            if ($feature['key'] === $key) {
                return $feature;
            }
        }

        return null;
    }
}
