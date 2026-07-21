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
     * Check if switch to target user is verified and within activity timeout window.
     */
    public function isSwitchVerified(User|int $target, int $timeoutMinutes = 15): bool
    {
        $targetId = $target instanceof User ? $target->id : (int) $target;

        if ($targetId === $this->id) {
            return true;
        }

        if (! app()->bound('session')) {
            return false;
        }

        $lastActivityTimestamp = (int) session('account_switch_verified_'.$targetId);

        if ($lastActivityTimestamp <= 0) {
            return false;
        }

        $elapsedSeconds = now()->getTimestamp() - $lastActivityTimestamp;
        $maxSeconds = $timeoutMinutes * 60;

        if ($elapsedSeconds <= $maxSeconds) {
            // Touch / auto-renew the activity timestamp for sliding session
            session(['account_switch_verified_'.$targetId => now()->getTimestamp()]);

            return true;
        }

        // Expired - flush session key
        session()->forget('account_switch_verified_'.$targetId);

        return false;
    }

    /**
     * Mark switch to target account as verified with current timestamp.
     */
    public function markSwitchVerified(User|int $target): void
    {
        $targetId = $target instanceof User ? $target->id : (int) $target;

        if (app()->bound('session')) {
            session(['account_switch_verified_'.$targetId => now()->getTimestamp()]);
        }
    }

    /**
     * Clear verification status for a target account.
     */
    public function clearSwitchVerification(User|int $target): void
    {
        $targetId = $target instanceof User ? $target->id : (int) $target;

        if (app()->bound('session')) {
            session()->forget('account_switch_verified_'.$targetId);
        }
    }

    /**
     * Switch authenticated user to target user account with sliding activity verification.
     */
    public function switchAccount(User|int $target, bool $bypassVerification = false, int $timeoutMinutes = 15): bool
    {
        $targetUser = $target instanceof User ? $target : User::find((int) $target);

        if (! $targetUser || ! $this->canSwitchTo($targetUser)) {
            return false;
        }

        if (! $bypassVerification && ! $this->isSwitchVerified($targetUser, $timeoutMinutes)) {
            return false;
        }

        // Backup current verified sessions before session regeneration
        $verifiedSessions = [];
        if (app()->bound('session')) {
            foreach (session()->all() as $key => $val) {
                if (str_starts_with($key, 'account_switch_verified_')) {
                    $verifiedSessions[$key] = $val;
                }
            }
        }

        Auth::login($targetUser);

        if (app()->bound('session')) {
            if (request()->hasSession()) {
                request()->session()->regenerate();
            }

            // Restore verified sessions after regeneration and mark current & previous user verified
            foreach ($verifiedSessions as $key => $val) {
                session([$key => $val]);
            }

            session([
                'account_switch_verified_'.$this->id => now()->getTimestamp(),
                'account_switch_verified_'.$targetUser->id => now()->getTimestamp(),
            ]);
        }

        return true;
    }
}
