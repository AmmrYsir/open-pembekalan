<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Collection;

new class extends Component
{
    #[Computed]
    public function unreadCount(): int
    {
        return auth()->user() ? auth()->user()->unreadNotifications()->count() : 0;
    }

    #[Computed]
    public function recentNotifications(): Collection
    {
        return auth()->user()
            ? auth()->user()->notifications()->take(5)->get()
            : collect();
    }

    public function markAllAsRead(): void
    {
        if (auth()->user()) {
            auth()->user()->unreadNotifications->markAsRead();
            $this->dispatch('notifications-updated');
        }
    }

    public function readAndRedirect(string $id): mixed
    {
        if (!auth()->user()) {
            return null;
        }

        $notification = auth()->user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            $this->dispatch('notifications-updated');
            
            $actionUrl = $notification->data['action_url'] ?? null;
            if ($actionUrl) {
                return redirect()->to($actionUrl);
            }
        }
    }
};
?>

<div x-data="{ open: false }" x-on:click.outside="open = false" class="relative" x-on:notifications-updated.window="$wire.$refresh()">
    <!-- Bell Button -->
    <button 
        @click="open = !open" 
        class="relative p-2 text-zinc-400 hover:text-zinc-650 dark:hover:text-zinc-350 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors duration-250 cursor-pointer focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
        title="Notifications"
    >
        @if($this->unreadCount > 0)
            <span class="absolute top-1.5 right-1.5 flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
            </span>
        @endif
        <x-heroicon-o-bell class="w-5 h-5 transition-transform duration-200 hover:scale-105" />
    </button>

    <!-- Glassmorphic Dropdown Menu -->
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-1"
        class="absolute right-0 mt-2.5 w-80 rounded-2xl bg-white/95 dark:bg-zinc-900/95 border border-zinc-200/60 dark:border-zinc-800/60 shadow-xl z-50 overflow-hidden backdrop-blur-md" 
        style="display: none;"
    >
        <!-- Header -->
        <div class="px-4.5 py-3.5 border-b border-zinc-100 dark:border-zinc-800/60 flex items-center justify-between">
            <span class="text-xs font-bold text-zinc-900 dark:text-zinc-100">Recent Notifications</span>
            @if($this->unreadCount > 0)
                <button 
                    wire:click="markAllAsRead" 
                    class="text-[10px] font-bold text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300 transition-colors cursor-pointer"
                >
                    Mark all as read
                </button>
            @endif
        </div>

        <!-- Notification Items list -->
        <div class="max-h-80 overflow-y-auto divide-y divide-zinc-50 dark:divide-zinc-850/30">
            @forelse($this->recentNotifications as $notification)
                @php
                    $data = $notification->data;
                    $isUnread = is_null($notification->read_at);
                    $iconType = $data['icon'] ?? 'info';
                    
                    $iconClass = match($iconType) {
                        'success' => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400',
                        'warning' => 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400',
                        'error' => 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400',
                        default => 'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400'
                    };
                @endphp
                <div 
                    wire:click="readAndRedirect('{{ $notification->id }}')" 
                    class="group px-4 py-3 flex gap-3 hover:bg-zinc-50/70 dark:hover:bg-zinc-800/30 cursor-pointer transition-colors duration-150 {{ $isUnread ? 'bg-emerald-50/10 dark:bg-emerald-950/5' : '' }}"
                >
                    <!-- Status Icon -->
                    <div class="shrink-0">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $iconClass }}">
                            @if($iconType === 'success')
                                <x-heroicon-o-check class="w-4 h-4" />
                            @elseif($iconType === 'warning')
                                <x-heroicon-o-exclamation-triangle class="w-4 h-4" />
                            @elseif($iconType === 'error')
                                <x-heroicon-o-x-mark class="w-4 h-4" />
                            @else
                                <x-heroicon-o-information-circle class="w-4 h-4" />
                            @endif
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1.5">
                            <p class="text-[11px] font-bold text-zinc-900 dark:text-zinc-100 truncate {{ $isUnread ? 'font-extrabold' : '' }}">
                                {{ $data['title'] ?? 'System Notification' }}
                            </p>
                            @if($isUnread)
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full shrink-0"></span>
                            @endif
                        </div>
                        <p class="text-[10px] text-zinc-500 dark:text-zinc-400 line-clamp-2 mt-0.5 leading-relaxed">
                            {{ $data['message'] ?? '' }}
                        </p>
                        <span class="text-[9px] text-zinc-400 dark:text-zinc-500 block mt-1">
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="py-8 px-4 text-center">
                    <div class="w-10 h-10 rounded-full bg-zinc-50 dark:bg-zinc-800/50 text-zinc-400 dark:text-zinc-500 flex items-center justify-center mx-auto mb-2.5">
                        <x-heroicon-o-bell-slash class="w-5 h-5" />
                    </div>
                    <p class="text-xs font-medium text-zinc-800 dark:text-zinc-200">All caught up!</p>
                    <p class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-0.5">No notifications right now.</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        <a 
            href="{{ route('notifications') }}" 
            @click="open = false"
            class="block text-center py-2.5 bg-zinc-50/50 hover:bg-zinc-50 dark:bg-zinc-900/50 dark:hover:bg-zinc-800/20 text-[10px] font-bold text-zinc-650 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-200 border-t border-zinc-100 dark:border-zinc-800/60 transition-colors cursor-pointer"
        >
            View all notifications
        </a>
    </div>
</div>
