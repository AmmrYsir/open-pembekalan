<x-layouts.app>
    <x-slot:title>Supplier Management</x-slot:title>

    <x-ui.page-header
        title="Supplier Management"
        subtitle="Manage vendor registrations, SSM records, MOF/CIDB certifications, and company details."
    />

    @livewire('supplier-table')
</x-layouts.app>
