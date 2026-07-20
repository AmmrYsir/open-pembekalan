<?php

namespace App\Traits;

use App\Models\LinkedAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

trait HasLinkedAccounts
{
    /**
     * Get accounts linked by this user.
     *
     * @return HasMany<LinkedAccount, $this>
     */
    public function linkedAccounts(): HasMany
    {
        return $this->hasMany(LinkedAccount::class, 'user_id');
    }

    /**
     * Get all users linked to or linking this user account (bidirectional).
     *
     * @return Collection<int, User>
     */
    public function getSwitchableAccounts(): Collection
    {
        $directLinkedUserIds = LinkedAccount::where('user_id', $this->id)->pluck('linked_user_id');
        $reverseLinkedUserIds = LinkedAccount::where('linked_user_id', $this->id)->pluck('user_id');

        $allIds = $directLinkedUserIds->concat($reverseLinkedUserIds)->unique()->reject(fn ($id) => $id === $this->id);

        return User::whereIn('id', $allIds)->get();
    }

    /**
     * Check if current user can switch to target user.
     */
    public function canSwitchTo(User|int $target): bool
    {
        $targetId = $target instanceof User ? $target->id : (int) $target;

        if ($targetId === $this->id) {
            return true;
        }

        return LinkedAccount::where(function ($query) use ($targetId) {
            $query->where('user_id', $this->id)->where('linked_user_id', $targetId);
        })->orWhere(function ($query) use ($targetId) {
            $query->where('user_id', $targetId)->where('linked_user_id', $this->id);
        })->exists();
    }

    /**
     * Link another user account bi-directionally.
     */
    public function linkAccount(User $targetUser, ?string $label = null): void
    {
        if ($targetUser->id === $this->id) {
            return;
        }

        LinkedAccount::firstOrCreate([
            'user_id' => $this->id,
            'linked_user_id' => $targetUser->id,
        ], [
            'label' => $label,
        ]);

        LinkedAccount::firstOrCreate([
            'user_id' => $targetUser->id,
            'linked_user_id' => $this->id,
        ], [
            'label' => $label,
        ]);
    }

    /**
     * Unlink another user account.
     */
    public function unlinkAccount(User|int $target): void
    {
        $targetId = $target instanceof User ? $target->id : (int) $target;

        LinkedAccount::where('user_id', $this->id)->where('linked_user_id', $targetId)->delete();
        LinkedAccount::where('user_id', $targetId)->where('linked_user_id', $this->id)->delete();
    }

    /**
     * Switch authenticated user to target user account.
     */
    public function switchAccount(User|int $target): bool
    {
        $targetUser = $target instanceof User ? $target : User::find((int) $target);

        if (! $targetUser || ! $this->canSwitchTo($targetUser)) {
            return false;
        }

        Auth::login($targetUser);

        if (request()->hasSession()) {
            request()->session()->regenerate();
        }

        return true;
    }
}
