<x-layouts.app>
    <x-slot:title>Notifications</x-slot:title>

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100 font-sans">Notifications</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Manage and view your system alerts and notifications.</p>
        </div>

        @livewire('notification-list')
    </div>
</x-layouts.app>
