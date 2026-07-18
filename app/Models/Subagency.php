<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subagency extends Model
{
    /** @use HasFactory<\Database\Factories\SubagencyFactory> */
    use HasFactory;

    protected $fillable = [
        'agency_id',
        'code',
        'name',
        'is_active',
    ];
}
