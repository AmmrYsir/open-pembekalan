<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Collection;
use App\Notifications\SystemNotification;

new class extends Component
{
    public string $filter = 'unread'; // 'unread', 'read', 'all'

    #[Computed]
    public function counts(): array
    {
        $user = auth()->user();
        if (!$user) {
            return ['all' => 0, 'unread' => 0, 'read' => 0];
        }

        return [
            'all' => $user->notifications()->count(),
            'unread' => $user->unreadNotifications()->count(),
            'read' => $user->readNotifications()->count(),
        ];
    }

    #[Computed]
    public function notifications(): Collection
    {
        $user = auth()->user();
        if (!$user) {
            return collect();
        }

        $query = $user->notifications();

        if ($this->filter === 'unread') {
            $query->unread();
        } elseif ($this->filter === 'read') {
            $query->whereNotNull('read_at');
        }

        return $query->latest()->get();
    }

    public function setFilter(string $filter): void
    {
        if (in_array($filter, ['unread', 'read', 'all'])) {
            $this->filter = $filter;
        }
    }

    public function markAsRead(string $id): void
    {
        $notification = auth()->user()?->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
            $this->dispatch('notifications-updated');
        }
    }

    public function markAsUnread(string $id): void
    {
        $notification = auth()->user()?->notifications()->find($id);
        if ($notification) {
            $notification->update(['read_at' => null]);
            $this->dispatch('notifications-updated');
        }
    }

    public function deleteNotification(string $id): void
    {
        $notification = auth()->user()?->notifications()->find($id);
        if ($notification) {
            $notification->delete();
            $this->dispatch('notifications-updated');
        }
    }

    public function markAllAsRead(): void
    {
        if (auth()->user()) {
            auth()->user()->unreadNotifications->markAsRead();
            $this->dispatch('notifications-updated');
            session()->flash('message', 'All notifications marked as read.');
        }
    }

    public function clearAll(): void
    {
        if (auth()->user()) {
            auth()->user()->notifications()->delete();
            $this->dispatch('notifications-updated');
            session()->flash('message', 'All notifications cleared.');
        }
    }

    public function simulateNotification(): void
    {
        $user = auth()->user();
        if (!$user) {
            return;
        }

        $simulations = [
            [
                'title' => 'Project Approved',
                'message' => 'The acquisition request "PRJ-2026-004: Fiber Optic Setup" has been officially approved by the central committee.',
                'action_url' => route('dashboard'),
                'icon' => 'success',
            ],
            [
                'title' => 'Vendor Proposal Received',
                'message' => 'A new tender proposal has been submitted by "Nexus Solutions Sdn Bhd" for the Cloud Storage expansion project.',
                'action_url' => null,
                'icon' => 'info',
            ],
            [
                'title' => 'Budget Warning',
                'message' => 'The security upgrade project for the Office Building is currently 15% over its allocated buffer budget.',
                'action_url' => null,
                'icon' => 'warning',
            ],
            [
                'title' => 'Document Verification Failed',
                'message' => 'The verification process for the vendor "Quantum Technologies" has failed due to expired corporate licenses.',
                'action_url' => null,
                'icon' => 'error',
            ],
        ];

        foreach ($simulations as $sim) {
            $user->notify(new SystemNotification(
                title: $sim['title'],
                message: $sim['message'],
                action_url: $sim['action_url'],
                icon: $sim['icon']
            ));
        }

        $this->dispatch('notifications-updated');
        session()->flash('message', '4 Demo notifications successfully simulated!');
    }
};
?>

<div class="space-y-6" x-on:notifications-updated.window="$wire.$refresh()">
    @if (session()->has('message'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200/30 text-emerald-800 dark:text-emerald-300 text-xs font-semibold flex items-center justify-between shadow-sm animate-fade-in">
            <span>{{ session('message') }}</span>
            <button class="text-emerald-550 dark:text-emerald-450 hover:text-emerald-700 font-bold ml-4 cursor-pointer" @click="$el.parentElement.remove()">Dismiss</button>
        </div>
    @endif

    <x-card>
        <!-- Top Toolbar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-zinc-100 dark:border-zinc-800/60">
            <!-- Filter Tabs -->
            <div class="flex gap-2">
                @foreach (['unread' => 'Unread', 'read' => 'Read', 'all' => 'All'] as $type => $label)
                    @php
                        $count = $this->counts[$type];
                        $isActive = $this->filter === $type;
                    @endphp
                    <button 
                        wire:click="setFilter('{{ $type }}')"
                        class="px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 cursor-pointer flex items-center gap-1.5 {{ $isActive ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 shadow-sm' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-300' }}"
                    >
                        <span>{{ $label }}</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $isActive ? 'bg-emerald-500 text-white font-black' : 'bg-zinc-100 dark:bg-zinc-800/80 text-zinc-650 dark:text-zinc-400 font-bold' }}">
                            {{ $count }}
                        </span>
                    </button>
                @endforeach
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 self-end sm:self-auto">
                <button 
                    wire:click="simulateNotification"
                    class="px-3.5 py-2 text-xs font-bold rounded-lg border border-zinc-200 hover:border-zinc-300 dark:border-zinc-800 dark:hover:border-zinc-700 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/20 transition-all cursor-pointer flex items-center gap-1.5"
                >
                    <x-heroicon-o-bolt class="w-3.5 h-3.5 text-amber-500" />
                    Simulate Demo Alerts
                </button>

                @if($this->counts['unread'] > 0)
                    <button 
                        wire:click="markAllAsRead"
                        class="px-3.5 py-2 text-xs font-bold rounded-lg bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/20 dark:hover:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 transition-colors cursor-pointer"
                    >
                        Mark all as read
                    </button>
                @endif

                @if($this->counts['all'] > 0)
                    <button 
                        wire:click="clearAll"
                        wire:confirm="Are you sure you want to delete all notifications? This cannot be undone."
                        class="px-3.5 py-2 text-xs font-bold rounded-lg bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/20 dark:hover:bg-rose-900/30 text-rose-700 dark:text-rose-400 transition-colors cursor-pointer"
                    >
                        Clear All
                    </button>
                @endif
            </div>
        </div>

        <!-- List Section -->
        <div class="mt-4 divide-y divide-zinc-100 dark:divide-zinc-800/60">
            @forelse ($this->notifications as $notification)
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
                <div class="py-4.5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-all duration-150 hover:bg-zinc-50/20 dark:hover:bg-zinc-800/5 {{ $isUnread ? 'bg-emerald-50/5 dark:bg-emerald-950/2' : '' }}">
                    <div class="flex items-start gap-4">
                        <!-- Icon -->
                        <div class="shrink-0 mt-0.5">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $iconClass }}">
                                @if($iconType === 'success')
                                    <x-heroicon-o-check class="w-5 h-5" />
                                @elseif($iconType === 'warning')
                                    <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                                @elseif($iconType === 'error')
                                    <x-heroicon-o-x-mark class="w-5 h-5" />
                                @else
                                    <x-heroicon-o-information-circle class="w-5 h-5" />
                                @endif
                            </div>
                        </div>

                        <!-- Text Details -->
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 {{ $isUnread ? 'font-extrabold' : '' }}">
                                    {{ $data['title'] ?? 'System Notification' }}
                                </h3>
                                @if($isUnread)
                                    <span class="px-1.5 py-0.5 rounded-full text-[9px] font-black bg-emerald-500 text-white uppercase tracking-wider">New</span>
                                @endif
                            </div>
                            <p class="text-xs text-zinc-550 dark:text-zinc-400 mt-1 leading-relaxed max-w-2xl">
                                {{ $data['message'] ?? '' }}
                            </p>
                            <div class="flex items-center gap-3 mt-2.5">
                                <span class="text-[10px] text-zinc-400 dark:text-zinc-500 flex items-center gap-1">
                                    <x-heroicon-o-clock class="w-3.5 h-3.5" />
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>

                                @if(!empty($data['action_url']))
                                    <span class="text-[10px] text-zinc-300 dark:text-zinc-700">&bull;</span>
                                    <a 
                                        href="{{ $data['action_url'] }}"
                                        wire:click="markAsRead('{{ $notification->id }}')"
                                        class="text-[10px] font-bold text-emerald-600 hover:text-emerald-550 dark:text-emerald-400 dark:hover:text-emerald-300 transition-colors inline-flex items-center gap-0.5 cursor-pointer"
                                    >
                                        View Details
                                        <x-heroicon-o-chevron-right class="w-3 h-3" />
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Individual Actions -->
                    <div class="flex items-center gap-2 self-end sm:self-auto shrink-0 border-t border-zinc-50 dark:border-zinc-800/40 sm:border-0 pt-3 sm:pt-0 w-full sm:w-auto justify-end">
                        @if ($isUnread)
                            <button 
                                wire:click="markAsRead('{{ $notification->id }}')"
                                class="p-2 text-zinc-400 hover:text-emerald-655 dark:hover:text-emerald-400 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors cursor-pointer"
                                title="Mark as Read"
                            >
                                <x-heroicon-o-check-circle class="w-5 h-5" />
                            </button>
                        @else
                            <button 
                                wire:click="markAsUnread('{{ $notification->id }}')"
                                class="p-2 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-350 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors cursor-pointer"
                                title="Mark as Unread"
                            >
                                <x-heroicon-o-arrow-path class="w-5 h-5" />
                            </button>
                        @endif

                        <button 
                            wire:click="deleteNotification('{{ $notification->id }}')"
                            class="p-2 text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors cursor-pointer"
                            title="Delete Notification"
                        >
                            <x-heroicon-o-trash class="w-5 h-5" />
                        </button>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="py-16 text-center max-w-sm mx-auto">
                    <div class="w-16 h-16 rounded-full bg-zinc-50 dark:bg-zinc-800/40 text-zinc-400 dark:text-zinc-500 flex items-center justify-center mx-auto mb-4 border border-zinc-100 dark:border-zinc-800/50 shadow-inner">
                        <x-heroicon-o-bell-slash class="w-7 h-7" />
                    </div>
                    <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-200">
                        @if ($filter === 'unread')
                            No unread notifications
                        @elseif ($filter === 'read')
                            No read notifications
                        @else
                            No notifications yet
                        @endif
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1.5 leading-relaxed">
                        @if ($filter === 'unread')
                            You are all caught up! There are no unread notifications waiting for your review.
                        @elseif ($filter === 'read')
                            You haven't read any notifications yet. Go to your unread tab to see recent alerts.
                        @else
                            No notifications have been triggered for your account. You can click below to simulate dummy system alerts.
                        @endif
                    </p>
                    <div class="mt-6 flex justify-center">
                        <x-button wire:click="simulateNotification" variant="primary" size="sm">
                            <x-heroicon-o-bolt class="w-4 h-4 mr-2" />
                            Simulate Notifications
                        </x-button>
                    </div>
                </div>
            @endforelse
        </div>
    </x-card>
</div>
