<?php

namespace App\Models;

use App\Contracts\HasUuidContract;
use App\Traits\HasUuid;
use Database\Factories\AgencyOfficerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class AgencyOfficer extends Model implements HasUuidContract
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

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Agency, $this>
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * @return BelongsTo<Subagency, $this>
     */
    public function subagency(): BelongsTo
    {
        return $this->belongsTo(Subagency::class);
    }

    /**
     * @return MorphOne<Address, $this>
     */
    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }
}
