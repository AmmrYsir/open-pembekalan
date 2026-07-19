<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    protected $fillable = [
        'uuid',
        'slug',
        'name',
        'position',
    ];

    protected $casts = [
        'position' => 'array',
    ];
}
