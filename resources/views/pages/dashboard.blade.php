<x-layouts.app>
    <x-slot:title>Dashboard</x-slot:title>

    @if (auth()->user()?->hasRole('superadmin'))
        @include('pages.dashboards.superadmin')
    @elseif (auth()->user()?->hasRole('supplier'))
        @include('pages.dashboards.supplier')
    @else
        @include('pages.dashboards.officer')
    @endif
</x-layouts.app>
