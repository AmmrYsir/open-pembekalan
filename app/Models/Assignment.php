<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;
use App\Traits\HasUuid;

class Assignment extends Model
{
	use HasUuid;

    protected $fillable = [
        'uuid',
        'acquisition_id',
        'reference_no',
        'title',
        'status',
        'assignable_id',
        'assignable_type',
        'user_ids',
    ];

    protected $casts = [
        'user_ids' => 'array',
    ];

	/**
     * @return MorphTo<assignable, $this>
     */
    public function assignable(): MorphTo
    {
        return $this->morphTo();
    }

	/**
     * @return BelongsTo<Acquisition, $this>
     */
    public function acquisition(): BelongsTo
    {
        return $this->belongsTo(Acquisition::class);
    }

    public function getUserModelsAttribute(): Collection
    {
        return User::whereIn('id', $this->user_ids)->get();
    }
}
