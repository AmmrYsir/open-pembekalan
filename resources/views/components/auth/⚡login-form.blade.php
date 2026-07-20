<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Services\AuthService;

new class extends Component
{
    #[Validate('required|email|string|max:124')]
    public $email = '';
    
    #[Validate('required|string|max:124')]
    public $password = '';

    public function login(AuthService $authService)
    {
        $this->validate();

        return $authService->login($this->email, $this->password);
    }
};
?>

<form wire:submit.prevent="login" class="space-y-4">
    @if ($errors->has('credentials_error'))
        <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700/40 text-rose-800 dark:text-rose-400 rounded-xl p-3 text-sm">
            <ul>
                <li>{{ $errors->first('credentials_error') }}</li>
            </ul>
        </div>
    @endif

    <x-ui.input wire:model="email" id="email" type="email" label="Email Address" placeholder="name@company.com" required error="{{ $errors->first('email') }}">
        <x-slot:icon>
            <x-heroicon-o-at-symbol class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
        </x-slot:icon>
    </x-ui.input>

    <x-ui.input wire:model="password" id="password" type="password" label="Password" placeholder="••••••••" required error="{{ $errors->first('password') }}">
        <x-slot:icon>
            <x-heroicon-o-lock-closed class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
        </x-slot:icon>
    </x-ui.input>

    <div class="flex items-center justify-between">
        <x-ui.checkbox id="remember_me" name="remember" label="Remember me" />
        <a href="/forgot-password" class="text-xs font-semibold text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300">Forgot password?</a>
    </div>

    <x-ui.button type="submit" class="w-full">
        Sign In
    </x-ui.button>
</form>