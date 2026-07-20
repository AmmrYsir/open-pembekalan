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

    public string $infoMessage = '';

    public string $infoVariant = 'success';

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
        $this->infoMessage = '';
        $this->infoVariant = 'success';

        $cleanedUsername = $this->username !== '' ? ltrim(trim($this->username), '@') : null;
        $this->username = $cleanedUsername ?? '';

        $validated = $this->validate();

        $targetUser = $this->user ?? auth()->user();

        if ($targetUser) {
            $currentName = $targetUser->name;
            $currentUsername = $targetUser->username ?? '';
            $currentEmail = $targetUser->email;

            $nameChanged = $currentName !== $validated['full_name'];
            $usernameChanged = $currentUsername !== ($cleanedUsername ?? '');
            $emailChanged = $currentEmail !== $validated['email'];

            if (! $nameChanged && ! $usernameChanged && ! $emailChanged) {
                $msg = 'No changes were detected in your profile information.';
                $this->infoMessage = $msg;
                $this->infoVariant = 'info';
                session()->flash('message', $msg);

                return;
            }

            $targetUser->name = $validated['full_name'];
            $targetUser->username = $cleanedUsername;
            $targetUser->email = $validated['email'];

            if ($emailChanged) {
                $targetUser->email_verified_at = null;
            }

            $targetUser->save();

            $this->user = $targetUser->fresh();

            $msg = $emailChanged
                ? 'Profile updated successfully. Please verify your new email address.'
                : 'Profile updated successfully.';

            $this->infoMessage = $msg;
            $this->infoVariant = 'success';
            session()->flash('message', $msg);
        }
    }
};
?>

<div class="md:col-span-2">
	<form wire:submit="updateInformation">
		<x-ui.card>
			<div class="space-y-4">
				@if ($infoMessage || session()->has('message'))
					<x-ui.alert wire:key="info-alert-{{ microtime() }}" :variant="$infoVariant" dismissible>
						{{ $infoMessage ?: session('message') }}
					</x-ui.alert>
				@endif

				@if (session()->has('error'))
					<x-ui.alert wire:key="info-error-{{ microtime() }}" variant="error" dismissible>
						{{ session('error') }}
					</x-ui.alert>
				@endif

				<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
					<x-ui.input wire:model="full_name" id="full_name" type="text" label="Full Name" required :error="$errors->first('full_name')" />
					
					<x-ui.input wire:model="username" id="username" type="text" label="Username" placeholder="username" :error="$errors->first('username')">
						<x-slot:icon>
							<span class="font-semibold text-zinc-500 dark:text-zinc-400 select-none">@</span>
						</x-slot:icon>
					</x-ui.input>
				</div>

				<x-ui.input wire:model="email" id="email" type="email" label="Email Address" required :error="$errors->first('email')">
					<x-slot:suffix>
						@if($user?->hasVerifiedEmail())
							<x-ui.badge variant="success" pill class="gap-1 text-[11px] py-0.5 px-2">
								<svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
								</svg>
								Verified
							</x-ui.badge>
						@else
							<x-ui.badge variant="warning" pill class="gap-1 text-[11px] py-0.5 px-2">
								<svg class="w-3 h-3 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
								</svg>
								Unverified
							</x-ui.badge>
						@endif
					</x-slot:suffix>
				</x-ui.input>
			</div>

			<x-slot:footer>
				<x-ui.button class="cursor-pointer" type="submit" loadingTarget="updateInformation">Save Changes</x-ui.button>
			</x-slot:footer>
		</x-ui.card>
	</form>
</div>