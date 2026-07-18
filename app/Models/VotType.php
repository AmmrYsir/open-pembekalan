<?php

namespace App\Models;

use Database\Factories\VotTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotType extends Model
{
    /** @use HasFactory<VotTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
    ];
}
