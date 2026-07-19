<?php

namespace App\Models;

use App\Traits\HasUuid;
use Database\Factories\AgencyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agency extends Model
{
    /** @use HasFactory<AgencyFactory> */
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'code',
        'name',
        'is_active',
    ];

    /**
     * @return HasMany<Subagency, $this>
     */
    public function subagencies(): HasMany
    {
        return $this->hasMany(Subagency::class);
    }
}
