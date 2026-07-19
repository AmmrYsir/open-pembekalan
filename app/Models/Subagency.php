<?php

namespace App\Models;

use Database\Factories\SubagencyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Subagency extends Model
{
    /** @use HasFactory<SubagencyFactory> */
    use HasFactory, HasUuid;

    protected $fillable = [
		'uuid',
        'agency_id',
        'code',
        'name',
        'is_active',
    ];
}
