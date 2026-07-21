<x-layouts.guest>
    <x-slot:title>Supplier Onboarding - openPembekalan</x-slot:title>

    <x-card>
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold tracking-tight text-zinc-950 dark:text-white">Supplier Onboarding</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1.5">Join the openPembekalan procurement portal</p>
        </div>

        @livewire('auth.register-form')

        <x-slot:footer>
            <span class="text-xs text-zinc-500 dark:text-zinc-400">Already registered?</span>
            <a href="/login" class="text-xs font-bold text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300">Sign in instead</a>
        </x-slot:footer>
    </x-card>
</x-layouts.guest>
