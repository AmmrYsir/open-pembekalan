<?php

namespace App\Models;

use App\Enums\AcquisitionMethod;
use App\Enums\AcquisitionType;
use Database\Factories\AcquisitionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acquisition extends Model
{
    /** @use HasFactory<AcquisitionFactory> */
    use HasFactory;

    protected $fillable = [
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

    public function votType()
    {
        return $this->belongsTo(VotType::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function subagency()
    {
        return $this->belongsTo(Subagency::class);
    }
}
