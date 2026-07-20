<?php

use Livewire\Component;

use Illuminate\Support\Facades\Hash;

use App\Models\User;

new class extends Component
{
    private ?User $user = null;

	public string $current_password = '';

	public string $new_password = '';

	public string $new_password_confirmation = '';

	public function rules(): array
    {
        $userId = $this->user?->id ?? auth()->id();

        return [
            'current_password' => ['required', 'string', 'max:255'],
            'new_password' => ['required', 'string', 'min:8'],
            'new_password_confirmation' => ['required', 'string', 'min:8', 'same:new_password'],
        ];
    }

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
	<form wire:submit="updatePassword">
		<x-ui.card>
			<div class="space-y-4">
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
			</div>

			<x-slot:footer>
				<x-ui.button class="cursor-pointer" type="submit" variant="primary" loadingTarget="updatePassword">Update Password</x-ui.button>
			</x-slot:footer>
		</x-ui.card>
	</form>
</div>