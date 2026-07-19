<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    use Uuids;

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
