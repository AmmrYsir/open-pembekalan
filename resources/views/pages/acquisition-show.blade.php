<x-layouts.app>
    <x-slot:title>
        {{ $acquisition->project_name }} — Acquisition Details
    </x-slot:title>

    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
            <a href="{{ route('acquisition') }}" class="hover:text-zinc-900 dark:hover:text-zinc-100 transition-colors">Acquisitions</a>
            <x-heroicon-o-chevron-right class="w-3 h-3 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
            <span class="font-mono text-zinc-700 dark:text-zinc-300 font-medium">{{ $acquisition->project_number }}</span>
        </div>
    </div>

    @livewire('acquisition.show', ['acquisition' => $acquisition])
</x-layouts.app>
