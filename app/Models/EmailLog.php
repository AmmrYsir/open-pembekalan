<?php

namespace App\Models;

use App\Contracts\HasUuidContract;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $uuid
 * @property string $recipient_email
 * @property string|null $recipient_name
 * @property string $subject
 * @property string|null $mailable_class
 * @property string $status
 * @property string|null $body_html
 * @property string|null $body_text
 * @property string|null $error_message
 * @property string|null $failed_job_id
 * @property Carbon|null $sent_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'uuid',
    'recipient_email',
    'recipient_name',
    'subject',
    'mailable_class',
    'status',
    'body_html',
    'body_text',
    'error_message',
    'failed_job_id',
    'sent_at',
])]
class EmailLog extends Model implements HasUuidContract
{
    use HasUuid;

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }
}
