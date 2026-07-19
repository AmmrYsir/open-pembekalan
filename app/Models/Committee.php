<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    use HasUuid;

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
