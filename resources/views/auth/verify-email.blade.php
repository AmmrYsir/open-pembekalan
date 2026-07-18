<x-layouts.guest>
    <x-slot:title>Verify Email - openPembekalan</x-slot:title>

    <x-ui.card>
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 mb-4 shadow-sm">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 19v-8.93a2 2 0 01.89-1.664l8-5.333a2 2 0 012.22 0l8 5.333A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-2.25-1.5a2 2 0 00-2.22 0l-2.25 1.5" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Verify Your Email</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1.5">
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed you?
            </p>
        </div>

        <!-- Success Toast Mock -->
        <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-800/30 text-xs text-emerald-700 dark:text-emerald-400 mb-5 flex items-start gap-2.5">
            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>A new verification link has been sent to the email address you provided during registration.</span>
        </div>

        <form action="/dashboard" method="GET" class="space-y-3">
            <x-ui.button type="submit" class="w-full">
                Verify Manually (Mock Proceed)
            </x-ui.button>
        </form>

        <form action="/login" method="GET" class="mt-3">
            <x-ui.button type="submit" variant="secondary" class="w-full">
                Resend Verification Email
            </x-ui.button>
        </form>

        <x-slot:footer>
            <span class="text-xs text-zinc-500 dark:text-zinc-400">Changed your mind?</span>
            <a href="/login" class="text-xs font-bold text-rose-600 hover:text-rose-500 dark:text-rose-400 dark:hover:text-rose-300">Sign Out</a>
        </x-slot:footer>
    </x-ui.card>
</x-layouts.guest>
