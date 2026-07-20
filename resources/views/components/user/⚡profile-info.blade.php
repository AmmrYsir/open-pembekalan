<?php

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component
{
    public ?User $user = null;

    public string $full_name = '';

    public string $username = '';

    public string $email = '';

    public function rules(): array
    {
        $userId = $this->user?->id ?? auth()->id();

        return [
            'full_name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'alpha_dash', 'max:255', Rule::unique('users', 'username')->ignore($userId)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
        ];
    }

    public function mount(?User $user): void
    {
        $this->user = $user ?? auth()->user();

        if ($this->user) {
            $this->full_name = $this->user->name;
            $this->username = $this->user->username ?? '';
            $this->email = $this->user->email;
        } else {
            abort(403, 'Unauthorized');
        }
    }

    public function updateInformation(): void
    {
        $cleanedUsername = $this->username !== '' ? ltrim(trim($this->username), '@') : null;
        $this->username = $cleanedUsername ?? '';

        $validated = $this->validate();

        $targetUser = $this->user ?? auth()->user();

        $targetUser?->update([
            'name' => $validated['full_name'],
            'username' => $cleanedUsername,
            'email' => $validated['email'],
        ]);

        session()->flash('message', 'Profile updated successfully.');
    }
};
?>

<div class="md:col-span-2">
	<x-ui.card>
		<form wire:submit.prevent="updateInformation" class="space-y-4">
			@if (session()->has('message'))
				<div class="p-3 text-xs font-medium text-emerald-700 bg-emerald-50 dark:bg-emerald-950/40 dark:text-emerald-300 rounded-lg border border-emerald-200 dark:border-emerald-800">
					{{ session('message') }}
				</div>
			@endif

			<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
				<x-ui.input wire:model="full_name" id="full_name" type="text" label="Full Name" required :error="$errors->first('full_name')" />
				
				<x-ui.input wire:model="username" id="username" type="text" label="Username" placeholder="username" :error="$errors->first('username')">
					<x-slot:icon>
						<span class="font-semibold text-zinc-500 dark:text-zinc-400 select-none">@</span>
					</x-slot:icon>
				</x-ui.input>
			</div>

			<x-ui.input wire:model="email" id="email" type="email" label="Email Address" required :error="$errors->first('email')" />

			<x-slot:footer>
				<x-ui.button class="cursor-pointer" type="submit">Save Changes</x-ui.button>
			</x-slot:footer>
		</form>
	</x-ui.card>
</div>