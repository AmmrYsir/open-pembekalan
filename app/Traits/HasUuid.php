<?php

namespace App\Traits;

use App\Contracts\HasUuidContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @phpstan-require-implements HasUuidContract
 */
trait HasUuid
{
    /**
     * Automatically boot the trait.
     * Laravel looks for a method named boot[TraitName]
     */
    protected static function bootHasUuid(): void
    {
        static::creating(function (Model $model) {
            $column = static::uuidColumnName();

            // Only generate if the UUID column is empty
            if (empty($model->{$column})) {
                $model->{$column} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the column name for the UUID (static, safe to call in boot closures).
     * Overridable in individual models if needed.
     */
    public static function uuidColumnName(): string
    {
        return 'uuid';
    }

    /**
     * Instance proxy so existing code calling $model->getUuidColumnName() still works.
     */
    public function getUuidColumnName(): string
    {
        return static::uuidColumnName();
    }

    /**
     * Use the UUID column for Route Model Binding.
     */
    public function getRouteKeyName(): string
    {
        return static::uuidColumnName();
    }
}
