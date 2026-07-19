<?php

namespace App\Traits;

use App\Contracts\HasAvatarColorContract;
use Illuminate\Database\Eloquent\Model;

/**
 * @phpstan-require-implements HasAvatarColorContract
 */
trait HasAvatarColor
{
    /**
     * Color palette — a curated set of accessible, visually distinct hex colors
     * used to generate deterministic avatar backgrounds.
     *
     * @var list<string>
     */
    protected static array $avatarColorPalette = [
        '#0ea5e9', // sky-500
        '#6366f1', // indigo-500
        '#8b5cf6', // violet-500
        '#ec4899', // pink-500
        '#f59e0b', // amber-500
        '#10b981', // emerald-500
        '#ef4444', // red-500
        '#f97316', // orange-500
        '#06b6d4', // cyan-500
        '#a855f7', // purple-500
        '#14b8a6', // teal-500
        '#3b82f6', // blue-500
        '#e11d48', // rose-600
        '#7c3aed', // violet-600
        '#0891b2', // cyan-600
    ];

    /**
     * Automatically boot the trait.
     * Laravel calls boot[TraitName] on every model that uses the trait.
     */
    protected static function bootHasAvatarColor(): void
    {
        static::creating(function (Model $model) {
            $column = static::avatarColorColumnName();

            if (empty($model->{$column})) {
                $model->{$column} = static::generateAvatarColor((string) $model->getAttribute('email'));
            }
        });
    }

    /**
     * Derive a deterministic hex color from an e-mail address (or any string).
     */
    public static function generateAvatarColor(string $seed): string
    {
        $palette = static::$avatarColorPalette;

        return $palette[abs(crc32($seed)) % count($palette)];
    }

    /**
     * Get the column that stores the avatar color (static, safe to call in boot closures).
     * Overridable per model if the column name differs.
     */
    public static function avatarColorColumnName(): string
    {
        return 'avatar_color';
    }

    /**
     * Instance proxy so existing code calling $model->getAvatarColorColumnName() still works.
     */
    public function getAvatarColorColumnName(): string
    {
        return static::avatarColorColumnName();
    }
}
