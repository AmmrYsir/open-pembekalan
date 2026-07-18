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
        auth()->logout();
        redirect()->route('home');
    }
};
?>

<div class="p-4 border-t border-zinc-100 dark:border-zinc-800/50 flex items-center gap-3">
    <div class="relative">
        <img class="h-10 w-10 rounded-xl object-cover ring-2 ring-emerald-500/10" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Avatar">
        <div class="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-zinc-900"></div>
    </div>
    <div class="flex-1 min-w-0">
        <p class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 truncate">{{ $user?->name ?? 'Guest User' }}</p>
        <p class="text-[10px] text-zinc-400 dark:text-zinc-500 truncate">{{ $user?->email }}</p>
    </div>
    <button wire:click.prevent="signOut" title="Sign Out" class="p-1.5 cursor-pointer text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
        </svg>
    </button>
</div>