<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MofCategory extends Model
{
    protected $fillable = [
        'code',
        'name',
    ];

    public function subcategories()
    {
        return $this->hasMany(MofSubcategory::class);
    }
}
