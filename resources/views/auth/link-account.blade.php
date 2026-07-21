<x-layouts.guest>
    <x-slot:title>Link Account - openPembekalan</x-slot:title>

    <x-card>
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 mb-3 shadow-xs">
                <x-heroicon-o-user-plus class="w-6 h-6" />
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Link Another Account</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1.5">Sign in to connect an additional user account to your switcher</p>
        </div>

        @livewire('auth.link-account-form')

        <x-slot:footer>
            <span class="text-xs text-zinc-500 dark:text-zinc-400">Changed your mind?</span>
            <a href="/dashboard" class="text-xs font-bold text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300">Return to Dashboard</a>
        </x-slot:footer>
    </x-card>
</x-layouts.guest>
