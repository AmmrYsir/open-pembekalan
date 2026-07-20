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

	public function mount(?User $user): void
	{
		$this->user = $user ?? auth()->user();

		if (! $this->user) {
			abort(403, 'Unauthorized');
		}
	}

    public string $successMessage = '';

    public string $errorMessage = '';

	public function updatePassword(): void
	{
		$this->successMessage = '';
		$this->errorMessage = '';

		$this->validate();

		$user = $this->user ?? auth()->user();

		if (! $user || ! Hash::check($this->current_password, $user->password)) {
			$this->addError('current_password', 'The provided current password does not match our records.');
			$this->errorMessage = 'Current password is incorrect.';
			session()->flash('error', 'Current password is incorrect.');
			return;
		}

		$user->update([
			'password' => Hash::make($this->new_password),
		]);

		$this->reset(['current_password', 'new_password', 'new_password_confirmation']);

		$msg = 'Password updated successfully.';
		$this->successMessage = $msg;
		session()->flash('message', $msg);
	}
};
?>

<div class="md:col-span-2">
<x-ui.card>
	<form wire:submit="updatePassword" class="space-y-4">
		@if ($successMessage || session()->has('message'))
			<x-ui.alert wire:key="pwd-success-{{ microtime() }}" variant="success" dismissible>
				{{ $successMessage ?: session('message') }}
			</x-ui.alert>
		@endif

		@if ($errorMessage || session()->has('error'))
			<x-ui.alert wire:key="pwd-error-{{ microtime() }}" variant="error" dismissible>
				{{ $errorMessage ?: session('error') }}
			</x-ui.alert>
		@endif

		<x-ui.input wire:model="current_password" id="current_password" type="password" label="Current Password" placeholder="••••••••" required :error="$errors->first('current_password')" />
		<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
			<x-ui.input wire:model="new_password" id="new_password" type="password" label="New Password" placeholder="••••••••" required :error="$errors->first('new_password')" />
			<x-ui.input wire:model="new_password_confirmation" id="new_password_confirmation" type="password" label="Confirm New Password" placeholder="••••••••" required :error="$errors->first('new_password_confirmation')" />
		</div>
		<x-slot:footer>
			<x-ui.button class="cursor-pointer" type="submit" variant="primary">Update Password</x-ui.button>
		</x-slot:footer>
	</form>
</x-ui.card>
</div>