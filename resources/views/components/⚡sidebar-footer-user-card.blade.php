<?php

use Livewire\Component;
use App\Models\User;

new class extends Component
{
    public ?User $user = null;

    public function mount(): void
    {
        $this->user = auth()->user();
    }

    public function signOut(): void
    {
        auth()->logout(); redirect()->route('home');
    }
};
?>

<div class="border-t border-zinc-100 dark:border-zinc-800/50 flex items-center">
    <a href="{{ route('profile') }}" class="p-4 flex items-center gap-3 flex-1 min-w-0  hover:bg-zinc-50 dark:hover:bg-zinc-800 cursor-pointer transition-colors duration-200">
        <div class="relative">
            @if($user && $user->hasAvatar())
                <img
                    class="h-8 w-8 rounded-sm object-cover ring-4 ring-emerald-500/10"
                    src="{{ $user->avatar }}"
                    alt="{{ $user->name }}'s avatar"
                >
            @elseif($user)
                <div
                    class="flex h-8 w-8 items-center justify-center rounded-sm ring-4 ring-emerald-500/10"
                    style="background-color: {{ $user->getAvatarColor() }};"
                >
                    <span class="text-lg font-bold leading-none text-white select-none">
                        {{ $user->initials() }}
                    </span>
                </div>
            @else
                <div
                    class="flex h-8 w-8 items-center justify-center rounded-sm ring-4 ring-emerald-500/10 bg-zinc-200 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500"
                >
                    <x-heroicon-o-user class="w-4 h-4" />
                </div>
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 truncate">{{ $user?->name ?? 'Guest User' }}</p>
            <p class="text-[10px] text-zinc-400 dark:text-zinc-500 truncate">{{ $user?->email }}</p>
        </div>
    </a>
    <div class="flex flex-initial items-center gap-2 p-2 h-full">
        <button wire:click.prevent="signOut" title="Sign Out" class="p-1.5 cursor-pointer text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800">
            <x-heroicon-o-arrow-left-on-rectangle class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
        </button>
    </div>
</div>