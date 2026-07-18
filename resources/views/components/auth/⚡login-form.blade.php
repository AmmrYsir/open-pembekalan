<?php

use Livewire\Component;

use App\Models\AuthService;

new class extends Component
{
    #[Validate('required|email')]
    public $email = '';
    
    #[Validate('required')]
    public $password = '';

    public function login()
    {
        (new AuthService())->login($this->email, $this->password);
    }
};
?>

<form wire:submit.prevent="login" class="space-y-4">
    <x-ui.input id="email" type="email" label="Email Address" placeholder="name@company.com" required>
        <x-slot:icon>
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
            </svg>
        </x-slot:icon>
    </x-ui.input>

    <x-ui.input id="password" type="password" label="Password" placeholder="••••••••" required>
        <x-slot:icon>
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </x-slot:icon>
    </x-ui.input>

    <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 cursor-pointer select-none">
            <input type="checkbox" class="w-4 h-4 rounded border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0 focus:outline-none">
            <span class="text-xs text-zinc-600 dark:text-zinc-400">Remember me</span>
        </label>
        <a href="/forgot-password" class="text-xs font-semibold text-emerald-600 hover:text-emerald-500 dark:text-emerald-400 dark:hover:text-emerald-300">Forgot password?</a>
    </div>

    <x-ui.button type="submit" class="w-full">
        Sign In
    </x-ui.button>
</form>