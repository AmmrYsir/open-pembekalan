<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MofCategory extends Model
{
    protected $fillable = [
        'code',
        'name',
    ];

    /**
     * @return HasMany<MofSubcategory, $this>
     */
    public function subcategories(): HasMany
    {
        return $this->hasMany(MofSubcategory::class);
    }
}
