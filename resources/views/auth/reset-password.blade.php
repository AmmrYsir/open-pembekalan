<x-layouts.guest>
    <x-slot:title>Reset Password - openPembekalan</x-slot:title>

    <x-card>
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 mb-4 shadow-sm">
                <x-heroicon-o-lock-closed class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Set New Password</h2>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1.5">Please choose a new password for your account</p>
        </div>

        <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <x-input id="email" name="email" type="email" label="Email Address" value="{{ old('email', $email) }}" required>
                    <x-slot:icon>
                        <x-heroicon-o-at-symbol class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </x-slot:icon>
                </x-input>
                @error('email')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-input id="password" name="password" type="password" label="New Password" placeholder="••••••••" required>
                    <x-slot:icon>
                        <x-heroicon-o-lock-closed class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </x-slot:icon>
                </x-input>
                @error('password')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-input id="password_confirmation" name="password_confirmation" type="password" label="Confirm New Password" placeholder="••••••••" required>
                    <x-slot:icon>
                        <x-heroicon-o-lock-closed class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </x-slot:icon>
                </x-input>
            </div>

            <x-button type="submit" class="w-full">
                Reset Password
            </x-button>
        </form>

        <x-slot:footer>
            <a href="{{ route('login') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300 inline-flex items-center gap-1">
                <x-heroicon-o-arrow-left class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                Back to Sign In
            </a>
        </x-slot:footer>
    </x-card>
</x-layouts.guest>
