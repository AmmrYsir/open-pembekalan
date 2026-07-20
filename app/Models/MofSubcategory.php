<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MofSubcategory extends Model
{
    protected $fillable = [
        'code',
        'name',
        'mof_category_id',
    ];

    public function category()
    {
        return $this->belongsTo(MofCategory::class, 'mof_category_id');
    }

    public function mofCodes()
    {
        return $this->hasMany(MofCode::class);
    }
}
