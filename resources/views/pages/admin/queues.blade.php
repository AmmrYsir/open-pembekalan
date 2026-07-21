<x-layouts.app>
    <x-slot:title>Queue Management - openPembekalan</x-slot:title>

    <x-page-header
        title="Queue Management"
        subtitle="Monitor background queue jobs, inspect job payloads, manage failed jobs, and track job batches."
    />

    @livewire('queue.manager')
</x-layouts.app>
