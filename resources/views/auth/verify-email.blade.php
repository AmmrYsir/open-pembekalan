<x-layouts.guest>
    <x-slot:title>Verify Email - openPembekalan</x-slot:title>

    <x-card>
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 mb-4 shadow-sm">
                <x-heroicon-o-envelope class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Verify Your Email</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1.5">
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed you?
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-800/30 text-xs text-emerald-700 dark:text-emerald-400 mb-5 flex items-start gap-2.5">
                <x-heroicon-o-check-circle class="w-4 h-4 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                <span>A new verification link has been sent to the email address you provided during registration.</span>
            </div>
        @endif

        <form action="{{ route('verification.send') }}" method="POST" class="space-y-3">
            @csrf
            <x-button type="submit" class="w-full">
                Resend Verification Email
            </x-button>
        </form>

        <x-slot:footer>
            <span class="text-xs text-zinc-500 dark:text-zinc-400">Changed your mind?</span>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-xs font-bold text-rose-600 hover:text-rose-500 dark:text-rose-400 dark:hover:text-rose-300">
                    Sign Out
                </button>
            </form>
        </x-slot:footer>
    </x-card>
</x-layouts.guest>

