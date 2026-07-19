<?php

namespace App\Models;

use App\Enums\SsmType;
use Database\Factories\SupplierFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasUuid;

class Supplier extends Model
{
    /** @use HasFactory<SupplierFactory> */
    use HasFactory, HasUuid;

    protected $fillable = [
		'uuid',
        'user_id',
        'company_name',
        'ssm_type',
        'ssm_number',
        'old_registration_number',
        'mobile_no',
        'telephone_no',
        'operating_area',
        'established_date',
        'website_link',
        'cert_verified_code',
        'tax_reference_number',
        'cjcp_reference_number',
        'ssm_start_date',
        'ssm_expiry_date',
        'kpb_active_date',
        'kpb_expiry_date',
        'cidb_active_date',
        'cidb_bumiputera_active_date',
        'cidb_expiry_date',
        'cidb_bumiputera_expiry_date',
        'mof_active_date',
        'mof_bumiputera_active_date',
        'mof_expiry_date',
        'mof_bumiputera_expiry_date',
        'cidb_cert_no',
        'cidb_bumiputera_cert_no',
        'cidb_gred_id',
        'cidb_bumi_status',
        'mof_registration_no',
        'mof_bumiputera_registration_no',
        'mof_bumi_status',
        'kpb_status',
        'paid_up_capital',
        'authorized_capital',
        'is_submitted',
        'submitted_at',
        'application_status',
        'application_reviewed_by',
        'application_reviewed_at',
        'application_approved_by',
        'application_approved_at',
    ];

    protected function casts(): array
    {
        return [
            'ssm_type' => SsmType::class,
            'established_date' => 'date',
            'ssm_start_date' => 'date',
            'ssm_expiry_date' => 'date',
            'kpb_active_date' => 'date',
            'kpb_expiry_date' => 'date',
            'cidb_active_date' => 'date',
            'cidb_bumiputera_active_date' => 'date',
            'cidb_expiry_date' => 'date',
            'cidb_bumiputera_expiry_date' => 'date',
            'mof_active_date' => 'date',
            'mof_bumiputera_active_date' => 'date',
            'mof_expiry_date' => 'date',
            'mof_bumiputera_expiry_date' => 'date',
            'is_submitted' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function applicationReviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'application_reviewed_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function applicationApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'application_approved_by');
    }

	public function address()
	{
		return $this->morphOne(Address::class, 'addressable');
	}
}
