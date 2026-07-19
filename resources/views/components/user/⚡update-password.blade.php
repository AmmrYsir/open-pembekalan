<?php

use Livewire\Component;

use Illuminate\Support\Facades\Hash;

use App\Models\User;

new class extends Component
{
    private ?User $user = null;

	#[Validate('required|string|min:8')]
	public string $current_password = '';

	#[Validate('required|string|min:8|confirmed')]
	public string $new_password = '';

	#[Validate('required|string|min:8|confirmed')]
	public string $new_password_confirmation = '';

	public function mount(): void
	{
		$this->user = auth()->user();
	}

	public function updatePassword(): void
	{
		$this->validate();

		$user = auth()->user();

		if (! $user || ! Hash::check($this->current_password, $user->password)) {
			session()->flash('error', 'Current password is incorrect.');
			return;
		}

		$user->update([
			'password' => Hash::make($this->new_password),
		]);

		session()->flash('message', 'Password updated successfully.');
	}
};
?>

<div class="md:col-span-2">
<x-ui.card>
	<form wire:submit.prevent="updatePassword" class="space-y-4">
		<x-ui.input wire:model="current_password" id="current_password" type="password" label="Current Password" placeholder="••••••••" required />
		<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
			<x-ui.input wire:model="new_password" id="new_password" type="password" label="New Password" placeholder="••••••••" required />
			<x-ui.input wire:model="new_password_confirmation" id="new_password_confirmation" type="password" label="Confirm New Password" placeholder="••••••••" required />
		</div>
		<x-slot:footer>
			<x-ui.button class="cursor-pointer" type="submit" variant="primary">Update Password</x-ui.button>
		</x-slot:footer>
	</form>
</x-ui.card>
</div>