<x-layouts.guest>
    <x-slot:title>Forgot Password - openPembekalan</x-slot:title>

    <x-ui.card>
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 mb-4 shadow-sm">
                <x-heroicon-o-key class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Forgot Password</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1.5">We will send reset instructions to your email</p>
        </div>

        <form action="/login" method="GET" class="space-y-4">
            <x-ui.input id="email" type="email" label="Email Address" placeholder="name@company.com" required>
                <x-slot:icon>
                    <x-heroicon-o-at-symbol class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </x-slot:icon>
            </x-ui.input>

            <x-ui.button type="submit" class="w-full">
                Send Reset Link
            </x-ui.button>
        </form>

        <x-slot:footer>
            <a href="/login" class="text-xs font-bold text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300 inline-flex items-center gap-1">
                <x-heroicon-o-arrow-left class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                Back to Sign In
            </a>
        </x-slot:footer>
    </x-ui.card>
</x-layouts.guest>
