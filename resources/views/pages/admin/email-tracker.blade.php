<x-layouts.app>
    <x-slot:title>Email Tracker - openPembekalan</x-slot:title>

    <x-page-header
        title="Email Tracker & Mail Jobs"
        subtitle="Track outbound emails, inspect rendered HTML/text content, monitor failed mail jobs, and resend messages."
    />

    @livewire('email-tracker.manager')
</x-layouts.app>
