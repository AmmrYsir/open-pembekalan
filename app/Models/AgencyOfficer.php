<?php

namespace App\Models;

use App\Traits\HasUuid;
use Database\Factories\AgencyOfficerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgencyOfficer extends Model
{
    /** @use HasFactory<AgencyOfficerFactory> */
    use HasFactory;

    use HasUuid;

    protected $fillable = [
        'uuid',
        'user_id',
        'agency_id',
        'subagency_id',
        'title',
        'nric',
        'position',
        'mobile_number',
        'home_phone_number',
        'created_by',
        'updated_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function subagency()
    {
        return $this->belongsTo(Subagency::class);
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }
}
