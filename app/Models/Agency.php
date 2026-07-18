<?php

namespace App\Models;

use Database\Factories\AgencyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    /** @use HasFactory<AgencyFactory> */ //
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'is_active',
    ];
}
