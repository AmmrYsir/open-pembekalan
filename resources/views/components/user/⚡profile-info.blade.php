<?php

use Livewire\Component;

use App\Models\User;

new class extends Component
{
	private ?User $user = null;

	#[Validate('required|string|max:255')]
	public string $full_name = '';

	#[Validate('required|email|max:255')]
	public string $email = '';

	public function mount(?User $user): void
	{
		$this->user = $user ?? auth()->user();

		if ($this->user) {
			$this->full_name = $this->user->name;
			$this->email = $this->user->email;
		}
		else {
			abort(403, 'Unauthorized');
		}
	}

	public function updateInformation(): void
	{
		// Validate and update user information
		$this->validate();

		auth()->user()?->update([
			'name' => $this->full_name,
			'email' => $this->email,
		]);

		session()->flash('message', 'Profile updated successfully.');
	}
};
?>

<div class="md:col-span-2">
	<x-ui.card>
		<form wire:submit.prevent="updateInformation" class="space-y-4">
			<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
				<x-ui.input wire:model="full_name" id="full_name" type="text" label="Full Name" required />
				<x-ui.input wire:model="email" id="email" type="email" label="Email Address" required />
			</div>
			<x-slot:footer>
				<x-ui.button class="cursor-pointer" type="submit">Save Changes</x-ui.button>
			</x-slot:footer>
		</form>
	</x-ui.card>
	</div>
</div>