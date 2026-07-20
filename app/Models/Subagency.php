<?php

namespace App\Models;

use App\Contracts\HasUuidContract;
use App\Traits\HasUuid;
use Database\Factories\SubagencyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subagency extends Model implements HasUuidContract
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

    /**
     * @return BelongsTo<Agency, $this>
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}
