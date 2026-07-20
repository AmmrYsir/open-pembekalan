<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

new class extends Component
{
    public ?User $user = null;

    public bool $showLinkModal = false;

    public string $link_email = '';

    public string $link_password = '';

    public string $link_error = '';

    public function mount(): void
    {
        $this->user = auth()->user();
    }

    public function switchAccount(int $targetUserId): void
    {
        if ($this->user && $this->user->switchAccount($targetUserId)) {
            $this->redirect(request()->header('Referer', route('dashboard')));
        }
    }

    public function linkNewAccount(): void
    {
        $this->link_error = '';

        $this->validate([
            'link_email' => ['required', 'email'],
            'link_password' => ['required', 'string'],
        ]);

        $targetUser = User::where('email', $this->link_email)->first();

        if (! $targetUser || ! Hash::check($this->link_password, $targetUser->password)) {
            $this->link_error = 'Invalid email or password for target account.';

            return;
        }

        if ($targetUser->id === $this->user?->id) {
            $this->link_error = 'You are already logged into this account.';

            return;
        }

        $this->user?->linkAccount($targetUser);

        $this->reset(['link_email', 'link_password', 'showLinkModal']);
        session()->flash('message', 'Account linked successfully!');
        $this->redirect(request()->header('Referer', route('dashboard')));
    }

    public function unlinkAccount(int $targetUserId): void
    {
        $this->user?->unlinkAccount($targetUserId);
        $this->user = $this->user?->fresh();
    }

    public function signOut(): void
    {
        Auth::logout();
        if (request()->hasSession()) {
            session()->invalidate();
            session()->regenerateToken();
        }
        $this->redirect(route('home'));
    }
};
?>

<div x-data="{ open: false }" class="relative border-t border-zinc-100 dark:border-zinc-800/50">

	{{-- ── Multi-Account Popover Menu ────────────────────────────────────────── --}}
	<div
		x-show="open"
		@click.outside="open = false"
		x-transition:enter="transition ease-out duration-150"
		x-transition:enter-start="opacity-0 scale-95 translate-y-2"
		x-transition:enter-end="opacity-100 scale-100 translate-y-0"
		x-transition:leave="transition ease-in duration-100"
		x-transition:leave-start="opacity-100 scale-100 translate-y-0"
		x-transition:leave-end="opacity-0 scale-95 translate-y-2"
		class="absolute bottom-full left-3 right-3 mb-2 z-50 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-3 shadow-xl space-y-3"
	>
		<!-- Active Account Header -->
		<div class="px-2 pt-1 flex items-center justify-between">
			<span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Active Account</span>
			<span class="inline-flex items-center gap-1 text-[10px] font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded-full border border-emerald-200 dark:border-emerald-800">
				<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
				Active
			</span>
		</div>

		<!-- Current Account Details -->
		<div class="flex items-center gap-3 p-2 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-100 dark:border-zinc-800">
			<div class="relative shrink-0">
				@if($user && $user->hasAvatar())
					<img class="h-9 w-9 rounded-xl object-cover ring-2 ring-emerald-500/20" src="{{ $user->avatar }}" alt="{{ $user->name }}">
				@elseif($user)
					<div class="flex h-9 w-9 items-center justify-center rounded-xl ring-2 ring-emerald-500/20" style="background-color: {{ $user->getAvatarColor() }};">
						<span class="text-sm font-bold text-white select-none">{{ $user->initials() }}</span>
					</div>
				@endif
			</div>
			<div class="flex-1 min-w-0">
				<p class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate">{{ $user?->name }}</p>
				<p class="text-[10px] text-zinc-400 dark:text-zinc-500 truncate">{{ $user?->email }}</p>
			</div>
		</div>

		<!-- Linked Accounts Section -->
		@php
			$linkedUsers = $user?->getSwitchableAccounts() ?? collect();
		@endphp

		<div class="space-y-1.5">
			<div class="px-2 flex items-center justify-between">
				<span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Switch Account</span>
				@if($linkedUsers->isNotEmpty())
					<span class="text-[10px] text-zinc-400 dark:text-zinc-500">{{ $linkedUsers->count() }} Linked</span>
				@endif
			</div>

			@if($linkedUsers->isNotEmpty())
				<div class="max-h-40 overflow-y-auto space-y-1 pr-1">
					@foreach($linkedUsers as $linked)
						<div class="flex items-center justify-between p-2 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800/80 transition-colors group">
							<button wire:click="switchAccount({{ $linked->id }})" class="flex items-center gap-2.5 flex-1 min-w-0 text-left cursor-pointer">
								<div class="shrink-0">
									@if($linked->hasAvatar())
										<img class="h-7 w-7 rounded-lg object-cover" src="{{ $linked->avatar }}" alt="{{ $linked->name }}">
									@else
										<div class="flex h-7 w-7 items-center justify-center rounded-lg text-xs font-bold text-white" style="background-color: {{ $linked->getAvatarColor() }};">
											{{ $linked->initials() }}
										</div>
									@endif
								</div>
								<div class="flex-1 min-w-0">
									<p class="text-xs font-medium text-zinc-800 dark:text-zinc-200 truncate group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $linked->name }}</p>
									<p class="text-[10px] text-zinc-400 dark:text-zinc-500 truncate">{{ $linked->email }}</p>
								</div>
							</button>
							<div class="flex items-center gap-1">
								<button wire:click="switchAccount({{ $linked->id }})" class="px-2 py-1 text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition-colors cursor-pointer">
									Switch
								</button>
								<button wire:click="unlinkAccount({{ $linked->id }})" title="Unlink Account" class="p-1 text-zinc-300 hover:text-rose-500 transition-colors cursor-pointer">
									<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
								</button>
							</div>
						</div>
					@endforeach
				</div>
			@else
				<p class="px-2 text-[11px] text-zinc-400 dark:text-zinc-500 italic">No other accounts linked yet.</p>
			@endif
		</div>

		<!-- Action Options -->
		<div class="pt-2 border-t border-zinc-100 dark:border-zinc-800/80 space-y-1">
			<button @click="$wire.showLinkModal = true; open = false" class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 transition-colors cursor-pointer">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
				<span>Link Another Account</span>
			</button>
			<a href="{{ route('profile') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
				<svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
				<span>Account Settings</span>
			</a>
			<button wire:click="signOut" class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-medium text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors cursor-pointer">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
				<span>Sign Out</span>
			</button>
		</div>
	</div>

	{{-- ── Main Footer User Card Trigger ───────────────────────────────────── --}}
	<div class="flex items-center justify-between p-3">
		<button @click="open = !open" class="flex items-center gap-3 flex-1 min-w-0 text-left cursor-pointer p-1.5 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800/80 transition-colors">
			<div class="relative shrink-0">
				@if($user && $user->hasAvatar())
					<img class="h-8 w-8 rounded-xl object-cover ring-2 ring-emerald-500/10" src="{{ $user->avatar }}" alt="{{ $user->name }}">
				@elseif($user)
					<div class="flex h-8 w-8 items-center justify-center rounded-xl ring-2 ring-emerald-500/10" style="background-color: {{ $user->getAvatarColor() }};">
						<span class="text-xs font-bold text-white select-none">{{ $user->initials() }}</span>
					</div>
				@endif
			</div>
			<div class="flex-1 min-w-0">
				<p class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate">{{ $user?->name ?? 'Guest User' }}</p>
				<p class="text-[10px] text-zinc-400 dark:text-zinc-500 truncate">{{ $user?->email }}</p>
			</div>
			<svg class="w-4 h-4 text-zinc-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
			</svg>
		</button>
	</div>

	{{-- ── Link Another Account Modal ────────────────────────────────────── --}}
	@if($showLinkModal)
		<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs p-4" x-data @keydown.escape.window="$wire.showLinkModal = false">
			<div class="w-full max-w-md rounded-2xl bg-white dark:bg-zinc-900 p-6 shadow-2xl border border-zinc-200 dark:border-zinc-800 space-y-4">
				<div class="flex items-center justify-between">
					<h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
						<svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
						Link Another Account
					</h3>
					<button wire:click="$set('showLinkModal', false)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 cursor-pointer">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
					</button>
				</div>

				<p class="text-xs text-zinc-500 dark:text-zinc-400">
					Enter credentials for another account you own to link it to your current session for instant account switching.
				</p>

				@if($link_error)
					<x-ui.alert variant="error" dismissible>
						{{ $link_error }}
					</x-ui.alert>
				@endif

				<form wire:submit="linkNewAccount" class="space-y-3">
					<x-ui.input wire:model="link_email" id="link_email" type="email" label="Account Email" placeholder="user@example.com" required :error="$errors->first('link_email')" />
					<x-ui.input wire:model="link_password" id="link_password" type="password" label="Account Password" placeholder="••••••••" required :error="$errors->first('link_password')" />

					<div class="pt-2 flex items-center justify-end gap-2">
						<x-ui.button type="button" variant="outline" wire:click="$set('showLinkModal', false)">Cancel</x-ui.button>
						<x-ui.button type="submit" loadingTarget="linkNewAccount">Link & Verify Account</x-ui.button>
					</div>
				</form>
			</div>
		</div>
	@endif

</div>