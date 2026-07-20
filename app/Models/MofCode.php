<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MofCode extends Model
{
    protected $fillable = [
        'code',
        'name',
        'mof_subcategory_id',
    ];

    public function category()
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

    public function subcategory()
    {
        return $this->belongsTo(MofSubcategory::class, 'mof_subcategory_id');
    }
}
