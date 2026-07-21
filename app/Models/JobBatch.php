<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $name
 * @property int $total_jobs
 * @property int $pending_jobs
 * @property int $failed_jobs
 * @property string $failed_job_ids
 * @property string|null $options
 * @property int|null $cancelled_at
 * @property int $created_at
 * @property int|null $finished_at
 */
class JobBatch extends Model
{
    protected $table = 'job_batches';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;
}
