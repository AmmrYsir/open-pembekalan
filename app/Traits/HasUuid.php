<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUuid
{
	/**
     * Automatically boot the trait.
     * Laravel looks for a method named boot[TraitName]
     */
    protected static function bootHasUuid(): void
    {
        static::creating(function (Model $model) {
            // Only generate if the UUID column is empty
            if (empty($model->{$model->getUuidColumnName()})) {
                $model->{$model->getUuidColumnName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the column name for the UUID.
     * Overridable in individual models if needed.
     */
    public function getUuidColumnName(): string
    {
        return 'uuid';
    }

    /**
     * Use the UUID column for Route Model Binding.
     */
    public function getRouteKeyName(): string
    {
        return $this->getUuidColumnName();
    }
}
