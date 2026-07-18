<?php

namespace App\Models;

use Database\Factories\AgencyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agency extends Model
{
    /** @use HasFactory<AgencyFactory> */
    use HasFactory;

    protected $fillable = [
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
