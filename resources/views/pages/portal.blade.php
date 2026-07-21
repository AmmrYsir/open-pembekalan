<x-layouts.guest containerClass="w-full max-w-6xl mx-auto py-8 px-4 sm:px-6">
    <x-slot:title>Public Procurement Tenders - openPembekalan</x-slot:title>

    <div class="space-y-8 py-4">
        <!-- Page Title Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-zinc-200/80 dark:border-zinc-800/80 pb-6">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-zinc-950 dark:text-white">Active Tenders & Advertisements</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Explore and filter active procurement announcements in real-time.</p>
            </div>
            <div>
                <a href="/" class="inline-flex items-center gap-1.5 text-xs font-bold text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-colors bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 px-3.5 py-1.5 rounded-xl shadow-xs">
                    &larr; Back to Home
                </a>
            </div>
        </div>

        @livewire('portal.portal-advertisements')
    </div>
</x-layouts.guest>
