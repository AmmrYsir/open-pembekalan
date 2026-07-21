<?php

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

new class extends Component
{
    public ?User $user = null;
    public bool $showConfirmModal = false;

    public ?User $pendingSwitchUser = null;
    public string $confirm_password = '';

    public function mount(?User $user = null): void
    {
        $this->user = $user ?? Auth::user();
    }

    public function initiateSwitch(int $targetUserId): void
    {
        $this->user = Auth::user();

        if (! $this->user || $targetUserId === $this->user->id) {
            return;
        }

        $targetUser = User::find($targetUserId);

        if (! $targetUser || ! $this->user->canSwitchTo($targetUser)) {
            session()->flash('error', 'Unauthorized account switch attempt.');
            return;
        }

        if ($this->user->isSwitchVerified($targetUser)) {
            $switched = $this->user->switchAccount($targetUser);
            if ($switched) {
                session()->flash('status', "Switched to account {$targetUser->name}.");
                $this->redirect(route('profile'));
            }
            return;
        }

        $this->pendingSwitchUser = $targetUser;
        $this->confirm_password = '';
        $this->resetErrorBag();
        $this->showConfirmModal = true;
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

        $this->user->markSwitchVerified($this->pendingSwitchUser);
        $switched = $this->user->switchAccount($this->pendingSwitchUser, bypassVerification: true);

        if ($switched) {
            $this->showConfirmModal = false;
            $this->confirm_password = '';
            session()->flash('status', "Verified ownership. Switched to {$this->pendingSwitchUser->name}.");
            $this->redirect(route('profile'));
        }
    }

    public function cancelConfirmModal(): void
    {
        $this->showConfirmModal = false;
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
            session()->flash('message', 'Account disconnected successfully.');
        }
    }
};
?>

<div class="md:col-span-2">
    <x-ui.card>
        <div class="space-y-4">
            <div class="flex items-center justify-between flex-wrap gap-2 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                <div>
                    <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-1.5">
                        <x-heroicon-o-user-group class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                        Linked User Accounts
                    </h4>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Connect multiple accounts to switch seamlessly across sessions.</p>
                </div>
                <a
                    href="{{ route('accounts.link') }}"
                    class="px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 rounded-xl shadow-xs transition-colors flex items-center gap-1.5 cursor-pointer"
                >
                    <x-heroicon-o-user-plus class="w-4 h-4" />
                    Link New Account
                </a>
            </div>

            @if($user)
                @php
                    $linkedAccounts = $user->getSwitchableAccounts();
                @endphp

                @if($linkedAccounts->isEmpty())
                    <div class="p-6 text-center rounded-xl bg-zinc-50 dark:bg-zinc-800/40 border border-dashed border-zinc-200 dark:border-zinc-800 space-y-2">
                        <x-heroicon-o-user-group class="w-8 h-8 text-zinc-400 mx-auto" />
                        <p class="text-xs font-medium text-zinc-600 dark:text-zinc-400">No linked accounts found.</p>
                        <p class="text-[11px] text-zinc-400 dark:text-zinc-500">Link another account to quickly switch profiles without signing out.</p>
                    </div>
                @else
                    <div class="space-y-2">
                        @foreach($linkedAccounts as $linked)
                            @php
                                $isVerified = $user->isSwitchVerified($linked);
                            @endphp
                            <div class="p-3.5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900/60 flex items-center justify-between flex-wrap gap-3 hover:border-emerald-500/30 transition-colors">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-lg text-white font-bold text-xs shrink-0 shadow-xs" style="background-color: {{ $linked->getAvatarColor() }};">
                                        {{ $linked->initials() }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <p class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate">{{ $linked->name }}</p>
                                            @if($isVerified)
                                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-md border border-emerald-200/60 dark:border-emerald-800/50">
                                                    <x-heroicon-s-shield-check class="w-3 h-3 text-emerald-500" /> Session Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-2 py-0.5 rounded-md border border-amber-200/60 dark:border-amber-800/50">
                                                    <x-heroicon-o-lock-closed class="w-3 h-3 text-amber-500" /> Password Required
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 truncate">{{ $linked->email }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        wire:click="initiateSwitch({{ $linked->id }})"
                                        class="px-3 py-1.5 text-xs font-semibold text-emerald-700 dark:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 rounded-xl border border-emerald-200/80 dark:border-emerald-800/60 transition-colors flex items-center gap-1 cursor-pointer"
                                    >
                                        <x-heroicon-o-arrows-right-left class="w-3.5 h-3.5" />
                                        Switch
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="unlinkAccount({{ $linked->id }})"
                                        title="Disconnect Account"
                                        class="p-1.5 text-zinc-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-xl transition-colors cursor-pointer"
                                    >
                                        <x-heroicon-o-trash class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </x-ui.card>

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
                    Please enter the password for <strong class="text-zinc-900 dark:text-white">{{ $pendingSwitchUser?->name }}</strong> to verify ownership before switching.
                </p>

                <form wire:submit.prevent="confirmAndSwitch" class="space-y-3">
                    <div>
                        <label for="profile_confirm_password" class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">
                            Password for {{ $pendingSwitchUser?->email }}
                        </label>
                        <input
                            type="password"
                            id="profile_confirm_password"
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
