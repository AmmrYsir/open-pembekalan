<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MofSubcategory extends Model
{
    protected $fillable = [
        'code',
        'name',
        'mof_category_id',
    ];

    /**
     * @return BelongsTo<MofCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(MofCategory::class, 'mof_category_id');
    }

    /**
     * @return HasMany<MofCode, $this>
     */
    public function mofCodes(): HasMany
    {
        return $this->hasMany(MofCode::class);
    }
}
