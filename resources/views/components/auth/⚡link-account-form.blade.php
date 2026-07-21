<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

new class extends Component
{
    #[Validate('required|email|string|max:124')]
    public string $email = '';

    #[Validate('required|string|max:124')]
    public string $password = '';

    public function linkAccount()
    {
        $this->validate();

        $currentUser = Auth::user();

        if (! $currentUser) {
            return redirect()->route('login');
        }

        $targetUser = User::where('email', $this->email)->first();

        if (! $targetUser || ! Hash::check($this->password, $targetUser->password)) {
            $this->addError('credentials_error', 'These credentials do not match our records.');
            return;
        }

        // Security Check 1: Prevent linking currently logged-in account
        if ($targetUser->id === $currentUser->id) {
            $this->addError('credentials_error', 'You are already logged in to this account.');
            return;
        }

        // Security Check 2: Prevent linking an already linked account
        if ($currentUser->canSwitchTo($targetUser)) {
            $this->addError('credentials_error', 'This account is already linked to your account switcher.');
            return;
        }

        // Link account bi-directionally and set sliding verification
        $currentUser->linkAccount($targetUser);
        $currentUser->markSwitchVerified($targetUser);

        session()->flash('success', "Account {$targetUser->name} ({$targetUser->email}) successfully linked to your account switcher.");

        return redirect()->route('dashboard');
    }
};
?>

<form wire:submit.prevent="linkAccount" class="space-y-4">
    @if ($errors->has('credentials_error'))
        <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700/40 text-rose-800 dark:text-rose-400 rounded-xl p-3 text-sm flex items-start gap-2">
            <x-heroicon-o-exclamation-triangle class="w-5 h-5 shrink-0 mt-0.5 text-rose-500" />
            <div>
                <p class="font-semibold text-xs text-rose-900 dark:text-rose-200">Unable to Link Account</p>
                <p class="text-xs mt-0.5">{{ $errors->first('credentials_error') }}</p>
            </div>
        </div>
    @endif

    <x-input wire:model="email" id="link_email" type="email" label="Account Email Address" placeholder="name@company.com" required error="{{ $errors->first('email') }}">
        <x-slot:icon>
            <x-heroicon-o-at-symbol class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
        </x-slot:icon>
    </x-input>

    <x-input wire:model="password" id="link_password" type="password" label="Account Password" placeholder="••••••••" required error="{{ $errors->first('password') }}">
        <x-slot:icon>
            <x-heroicon-o-lock-closed class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
        </x-slot:icon>
    </x-input>

    <div class="pt-2 flex items-center justify-between gap-3">
        <a href="/dashboard" class="px-4 py-2.5 text-xs font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl transition-colors">
            Cancel
        </a>
        <x-button type="submit" class="flex-1">
            <x-heroicon-o-link class="w-4 h-4 mr-1.5" />
            Link Account
        </x-button>
    </div>
</form>
