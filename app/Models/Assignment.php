<?php

namespace App\Models;

use Database\Factories\AssignmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    /** @use HasFactory<AssignmentFactory> */
    use HasFactory;

    protected $fillable = [
        'uuid',
        'acquisition_id',
        'reference_no',
        'title',
        'status',
        'assignable_id',
        'assignable_type',
        'user_ids',
    ];

    protected $casts = [
        'user_ids' => 'array',
    ];

    public function assignable()
    {
        return $this->morphTo();
    }

    public function acquisition()
    {
        return $this->belongsTo(Acquisition::class);
    }

    public function getUserModelsAttribute()
    {
        return User::whereIn('id', $this->user_ids)->get();
    }
}
