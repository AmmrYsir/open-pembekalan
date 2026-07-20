<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Contracts\HasAvatarColorContract;
use App\Contracts\HasUuidContract;
use App\Traits\HasAvatarColor;
use App\Traits\HasLinkedAccounts;
use App\Traits\HasRoles;
use App\Traits\HasUuid;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property string $uuid
 * @property string $username
 * @property string $name
 * @property string $email
 * @property string|null $avatar_url
 * @property string $avatar_color
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['uuid', 'username', 'name', 'email', 'avatar_url', 'avatar_color', 'email_verified_at', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements HasAvatarColorContract, HasUuidContract
{
    /** @use HasFactory<UserFactory> */
    use HasAvatarColor, HasFactory, HasLinkedAccounts, HasRoles, HasUuid, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'created_by' => 'date',
        ];
    }

    protected $appends = [
        'avatar',
    ];

    public function hasAvatar(): bool
    {
        if ($this->avatar_url === null || $this->avatar_url === '') {
            return false;
        }

        return Storage::disk('public')->exists($this->avatar_url);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    public function getAvatarColor(): string
    {
        return $this->avatar_color ?? static::generateAvatarColor($this->email);
    }

    public function getAvatarAttribute(): string
    {
        if ($this->hasAvatar()) {
            return Storage::disk('public')->image($this->avatar_url);
        }

        return $this->getAvatarColor();
    }

    /**
     * Get the agency officer profile associated with the user.
     *
     * @return HasOne<AgencyOfficer, $this>
     */
    public function agencyOfficer(): HasOne
    {
        return $this->hasOne(AgencyOfficer::class);
    }
}
