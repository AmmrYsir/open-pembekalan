<?php

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

new class extends Component
{
    public ?User $user = null;
    public bool $dropdownOpen = false;
    public bool $showConfirmModal = false;

    public ?int $pendingSwitchUserId = null;
    public ?User $pendingSwitchUser = null;
    public string $confirm_password = '';

    public function mount(): void
    {
        $this->user = Auth::user();
    }

    public function toggleDropdown(): void
    {
        $this->dropdownOpen = ! $this->dropdownOpen;
    }

    public function initiateSwitch(int $targetUserId): void
    {
        $this->user = Auth::user();

        if (! $this->user) {
            return;
        }

        if ($targetUserId === $this->user->id) {
            $this->dropdownOpen = false;
            return;
        }

        $targetUser = User::find($targetUserId);

        if (! $targetUser || ! $this->user->canSwitchTo($targetUser)) {
            $this->addError('switch', 'Unauthorized account switch attempt.');
            return;
        }

        // Check if verified with sliding activity session
        if ($this->user->isSwitchVerified($targetUser)) {
            $switched = $this->user->switchAccount($targetUser);
            if ($switched) {
                session()->flash('success', "Switched to account {$targetUser->name}.");
                $this->redirect(request()->header('Referer') ?: route('home'));
            }
            return;
        }

        // Verification required - open confirmation modal
        $this->pendingSwitchUserId = $targetUser->id;
        $this->pendingSwitchUser = $targetUser;
        $this->confirm_password = '';
        $this->resetErrorBag();
        $this->showConfirmModal = true;
        $this->dropdownOpen = false;
    }

    public function confirmAndSwitch(): void
    {
        $this->user = Auth::user();

        if (! $this->user || ! $this->pendingSwitchUser) {
            return;
        }

        $this->validate([
            'confirm_password' => ['required', 'string'],
        ]);

        if (! Hash::check($this->confirm_password, $this->pendingSwitchUser->password)) {
            $this->addError('confirm_password', 'Invalid password for account ' . $this->pendingSwitchUser->email);
            return;
        }

        // Mark switch as verified in session
        $this->user->markSwitchVerified($this->pendingSwitchUser);

        $switched = $this->user->switchAccount($this->pendingSwitchUser, bypassVerification: true);

        if ($switched) {
            $this->showConfirmModal = false;
            $this->confirm_password = '';
            session()->flash('success', "Security confirmation verified. Switched to {$this->pendingSwitchUser->name}.");
            $this->redirect(request()->header('Referer') ?: route('home'));
        }
    }

    public function cancelConfirmModal(): void
    {
        $this->showConfirmModal = false;
        $this->pendingSwitchUserId = null;
        $this->pendingSwitchUser = null;
        $this->confirm_password = '';
        $this->resetErrorBag();
    }

    public function unlinkAccount(int $targetUserId): void
    {
        $this->user = Auth::user();

        if ($this->user) {
            $targetUser = User::find($targetUserId);
            $this->user->unlinkAccount($targetUserId);
            $this->user->clearSwitchVerification($targetUserId);
            session()->flash('info', 'Account unlinked successfully.');
        }
    }

    public function signOut(): void
    {
        Auth::logout();
        $this->redirect(route('home'));
    }

    public function signOutAll(): void
    {
        if (request()->hasSession()) {
            foreach (session()->all() as $key => $val) {
                if (str_starts_with($key, 'account_switch_verified_')) {
                    session()->forget($key);
                }
            }
        }
        Auth::logout();
        $this->redirect(route('home'));
    }
};
?>

<div class="border-t border-zinc-100 dark:border-zinc-800/50 flex items-center relative" x-data="{ open: @entangle('dropdownOpen') }">
    <!-- Main Footer Card User Row -->
    <div class="p-3 flex items-center gap-3 flex-1 min-w-0 hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-colors duration-200 rounded-lg select-none cursor-pointer" @click="open = !open">
        <div class="relative">
            @if($user && $user->hasAvatar())
                <img
                    class="h-9 w-9 rounded-md object-cover ring-2 ring-emerald-500/20"
                    src="{{ $user->avatar }}"
                    alt="{{ $user->name }}'s avatar"
                >
            @elseif($user)
                <div
                    class="flex h-9 w-9 items-center justify-center rounded-md ring-2 ring-emerald-500/20 shadow-sm"
                    style="background-color: {{ $user->getAvatarColor() }};"
                >
                    <span class="text-sm font-bold leading-none text-white select-none">
                        {{ $user->initials() }}
                    </span>
                </div>
            @else
                <div class="flex h-9 w-9 items-center justify-center rounded-md ring-2 ring-emerald-500/20 bg-zinc-200 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500">
                    <x-heroicon-o-user class="w-5 h-5" />
                </div>
            @endif

            <!-- Experimental badge indicator dot -->
            <span class="absolute -top-1 -right-1 flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500 border-2 border-white dark:border-zinc-900"></span>
            </span>
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-1.5">
                <p class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 truncate">{{ $user?->name ?? 'Guest User' }}</p>
                <span class="px-1.5 py-0.25 text-[9px] font-medium tracking-wide bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 rounded-md border border-emerald-200/50 dark:border-emerald-800/50">Multi</span>
            </div>
            <p class="text-[10px] text-zinc-400 dark:text-zinc-500 truncate">{{ $user?->email }}</p>
        </div>

        <button type="button" class="p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 rounded-md hover:bg-zinc-100 dark:hover:bg-zinc-700/50 transition-colors">
            <x-heroicon-o-arrows-up-down class="w-4 h-4" />
        </button>
    </div>

    <div class="flex flex-initial items-center gap-1 p-2 h-full">
        <button wire:click.prevent="signOut" title="Sign Out" class="p-1.5 cursor-pointer text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800">
            <x-heroicon-o-arrow-left-on-rectangle class="w-5 h-5" />
        </button>
    </div>

    <!-- Account Switcher Popover Dropdown -->
    <div
        x-show="open"
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-2 scale-95"
        class="absolute bottom-full left-0 mb-2 w-72 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl z-50 overflow-hidden p-2 text-zinc-800 dark:text-zinc-100"
        style="display: none;"
    >
        <!-- Dropdown Header -->
        <div class="px-3 py-2 border-b border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between">
            <div class="flex items-center gap-1.5">
                <x-heroicon-o-user-group class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                <span class="text-xs font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Switch Account</span>
            </div>
            <span class="text-[9px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-400">
                Security Verified
            </span>
        </div>

        <!-- Accounts List -->
        <div class="py-2 max-h-60 overflow-y-auto space-y-1">
            <!-- Active Account -->
            @if($user)
                <div class="p-2 rounded-xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-800/50 flex items-center justify-between">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="flex h-7 w-7 items-center justify-center rounded-md text-white font-bold text-xs shrink-0" style="background-color: {{ $user->getAvatarColor() }};">
                            {{ $user->initials() }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 truncate">{{ $user->name }}</p>
                            <p class="text-[10px] text-zinc-500 dark:text-zinc-400 truncate">{{ $user->email }}</p>
                        </div>
                    </div>
                    <span class="flex items-center gap-1 text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 bg-white dark:bg-zinc-900 px-2 py-0.5 rounded-md shadow-xs">
                        <x-heroicon-s-check-circle class="w-3.5 h-3.5 text-emerald-500" /> Active
                    </span>
                </div>
            @endif

            <!-- Switchable Linked Accounts -->
            @if($user)
                @foreach($user->getSwitchableAccounts() as $linked)
                    @php
                        $isVerified = $user->isSwitchVerified($linked);
                    @endphp
                    <div class="p-2 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800/80 transition-colors flex items-center justify-between group">
                        <button type="button" wire:click="initiateSwitch({{ $linked->id }})" class="flex items-center gap-2.5 min-w-0 flex-1 text-left cursor-pointer">
                            <div class="flex h-7 w-7 items-center justify-center rounded-md text-white font-bold text-xs shrink-0" style="background-color: {{ $linked->getAvatarColor() }};">
                                {{ $linked->initials() }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1">
                                    <p class="text-xs font-medium text-zinc-800 dark:text-zinc-200 truncate group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $linked->name }}</p>
                                    @if($isVerified)
                                        <span title="Verified sliding session active" class="text-emerald-500">
                                            <x-heroicon-s-shield-check class="w-3.5 h-3.5" />
                                        </span>
                                    @else
                                        <span title="Password confirmation required" class="text-amber-500">
                                            <x-heroicon-o-lock-closed class="w-3.5 h-3.5" />
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[10px] text-zinc-400 dark:text-zinc-500 truncate">{{ $linked->email }}</p>
                            </div>
                        </button>

                        <button
                            type="button"
                            wire:click.prevent="unlinkAccount({{ $linked->id }})"
                            title="Unlink Account"
                            class="opacity-0 group-hover:opacity-100 p-1 text-zinc-400 hover:text-red-600 dark:hover:text-red-400 rounded-md hover:bg-red-50 dark:hover:bg-red-950/40 transition-all cursor-pointer"
                        >
                            <x-heroicon-o-x-mark class="w-4 h-4" />
                        </button>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Dropdown Actions -->
        <div class="pt-2 mt-1 border-t border-zinc-100 dark:border-zinc-800/80 space-y-1">
            <a
                href="{{ route('accounts.link') }}"
                class="w-full px-3 py-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 rounded-xl flex items-center justify-center gap-1.5 transition-colors cursor-pointer"
            >
                <x-heroicon-o-user-plus class="w-4 h-4" />
                Link Another Account
            </a>

            <button
                type="button"
                wire:click="signOutAll"
                class="w-full px-3 py-1.5 text-xs font-medium text-zinc-500 dark:text-zinc-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-xl flex items-center justify-center gap-1.5 transition-colors cursor-pointer"
            >
                <x-heroicon-o-arrow-left-start-on-rectangle class="w-4 h-4" />
                Sign Out All Accounts
            </button>
        </div>
    </div>

    <!-- Security Confirmation Modal -->
    @if($showConfirmModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-950/60 backdrop-blur-xs">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-2xl max-w-md w-full p-6 text-zinc-900 dark:text-zinc-100 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 rounded-xl">
                        <x-heroicon-o-shield-exclamation class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Security Confirmation</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Confirm ownership before switching accounts</p>
                    </div>
                </div>

                <div class="p-3 bg-zinc-50 dark:bg-zinc-800/60 rounded-xl border border-zinc-100 dark:border-zinc-800 flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg text-white font-bold text-sm shrink-0" style="background-color: {{ $pendingSwitchUser?->getAvatarColor() }};">
                        {{ $pendingSwitchUser?->initials() }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold truncate">{{ $pendingSwitchUser?->name }}</p>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 truncate">{{ $pendingSwitchUser?->email }}</p>
                    </div>
                </div>

                <p class="text-xs text-zinc-600 dark:text-zinc-300 leading-relaxed">
                    Please enter the password for <strong class="text-zinc-900 dark:text-white">{{ $pendingSwitchUser?->name }}</strong> to verify ownership. Once verified, you can switch seamlessly while actively using the account.
                </p>

                <form wire:submit.prevent="confirmAndSwitch" class="space-y-3">
                    <div>
                        <label for="confirm_password" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
                            Password for {{ $pendingSwitchUser?->email }}
                        </label>
                        <input
                            type="password"
                            id="confirm_password"
                            wire:model="confirm_password"
                            placeholder="Enter password"
                            class="w-full px-3 py-2 text-sm rounded-xl bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                            required
                        >
                        @error('confirm_password')
                            <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button
                            type="button"
                            wire:click="cancelConfirmModal"
                            class="px-4 py-2 text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl transition-colors cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl shadow-md transition-colors cursor-pointer flex items-center gap-1.5"
                        >
                            <x-heroicon-o-check-circle class="w-4 h-4" />
                            Confirm & Switch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
