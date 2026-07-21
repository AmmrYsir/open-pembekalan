<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $uuid
 * @property string $connection
 * @property string $queue
 * @property string $payload
 * @property string $exception
 * @property Carbon $failed_at
 */
#[Fillable(['uuid', 'connection', 'queue', 'payload', 'exception', 'failed_at'])]
class FailedJob extends Model
{
    protected $table = 'failed_jobs';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'failed_at' => 'datetime',
        ];
    }

    public function isMailJob(): bool
    {
        return str_contains($this->payload, 'Mail')
            || str_contains($this->payload, 'Notification')
            || str_contains($this->payload, 'SendEmail')
            || str_contains($this->payload, 'VerifyEmail')
            || str_contains($this->payload, 'ResetPassword');
    }
}
