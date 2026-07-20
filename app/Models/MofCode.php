<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class MofCode extends Model
{
    protected $fillable = [
        'code',
        'name',
        'mof_subcategory_id',
    ];

    /**
     * @return HasOneThrough<MofCategory, MofSubcategory, $this>
     */
    public function category(): HasOneThrough
    {
        return $this->hasOneThrough(
            MofCategory::class,
            MofSubcategory::class,
            'id', // Foreign key on the MofSubcategory table...
            'id', // Foreign key on the MofCategory table...
            'mof_subcategory_id', // Local key on the MofCode table...
            'mof_category_id' // Local key on the MofSubcategory table...
        );
    }

    /**
     * @return BelongsTo<MofSubcategory, $this>
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(MofSubcategory::class, 'mof_subcategory_id');
    }
}
