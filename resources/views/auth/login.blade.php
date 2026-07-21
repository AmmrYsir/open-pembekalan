<x-layouts.guest>
    <x-slot:title>Sign In - openPembekalan</x-slot:title>

    <x-card>
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Welcome Back</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1.5">Access the procurement & supply portal</p>
        </div>

        @livewire('auth.login-form')

        <x-slot:footer>
            <span class="text-xs text-zinc-500 dark:text-zinc-400">New to the platform?</span>
            <a href="/register" class="text-xs font-bold text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300">Create an account</a>
        </x-slot:footer>
    </x-card>
</x-layouts.guest>
