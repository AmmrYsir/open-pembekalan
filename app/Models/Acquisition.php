<?php

namespace App\Models;

use App\Enums\AcquisitionMethod;
use App\Enums\AcquisitionType;
use Database\Factories\AcquisitionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUuid;

class Acquisition extends Model
{
    /** @use HasFactory<AcquisitionFactory> */
    use HasFactory, HasUuid;

    protected $fillable = [
		'uuid',
        'type',
        'method',
        'project_number',
        'project_name',
        'status',
        'provision_type',
        'submission_type',
        'vot_type_id',
        'tender_number',
        'siling_price',
        'no_allocation_warrant',
        'agency_id',
        'subagency_id',
        'is_required_kbp',
        'mof_required',
        'cidb_required',
        'committee_type',
    ];

    public function casts(): array
    {
        return [
            'type' => AcquisitionType::class,
            'method' => AcquisitionMethod::class,
            'is_required_kbp' => 'boolean',
            'mof_required' => 'boolean',
            'cidb_required' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<VotType, $this>
     */
    public function votType(): BelongsTo
    {
        return $this->belongsTo(VotType::class);
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
}
