<x-layouts.app>
    <x-slot:title>Agency Officer Management</x-slot:title>

    <x-ui.page-header
        title="Agency Officers"
        subtitle="Manage focal officers, titles, contact numbers, and agency assignments."
    />

    @livewire('agency-officer-table')
</x-layouts.app>
