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

    public ?int $agency_id = null;

    public ?int $subagency_id = null;

    public function rules(): array
    {
        $userId = $this->user?->id ?? auth()->id();

        return [
            'full_name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'alpha_dash', 'max:255', Rule::unique('users', 'username')->ignore($userId)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'agency_id' => ['nullable', 'integer', 'exists:agencies,id'],
            'subagency_id' => ['nullable', 'integer', 'exists:subagencies,id'],
        ];
    }

    public function mount(?User $user): void
    {
        $this->user = $user ?? auth()->user();

        if ($this->user) {
            $this->full_name = $this->user->name;
            $this->username = $this->user->username ?? '';
            $this->email = $this->user->email;

            $officer = $this->user->agencyOfficer;
            $this->agency_id = $officer?->agency_id;
            $this->subagency_id = $officer?->subagency_id;

            $currentUser = auth()->user();
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

            $officer = $targetUser->agencyOfficer;
            $currentAgencyId = $officer?->agency_id;
            $currentSubagencyId = $officer?->subagency_id;

            $nameChanged = $currentName !== $validated['full_name'];
            $usernameChanged = $currentUsername !== ($cleanedUsername ?? '');
            $emailChanged = $currentEmail !== $validated['email'];
            $agencyChanged = $currentAgencyId !== $validated['agency_id'] || $currentSubagencyId !== $validated['subagency_id'];

            if (! $nameChanged && ! $usernameChanged && ! $emailChanged && ! $agencyChanged) {
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
		<x-card>
			<div class="space-y-4">
				@if ($infoMessage || session()->has('message'))
					<x-alert wire:key="info-alert-{{ microtime() }}" :variant="$infoVariant" dismissible>
						{{ $infoMessage ?: session('message') }}
					</x-alert>
				@endif

				@if (session()->has('error'))
					<x-alert wire:key="info-error-{{ microtime() }}" variant="error" dismissible>
						{{ session('error') }}
					</x-alert>
				@endif

				<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
					<x-input wire:model="full_name" id="full_name" type="text" label="Full Name" required :error="$errors->first('full_name')" />
					
					<x-input wire:model="username" id="username" type="text" label="Username" placeholder="username" :error="$errors->first('username')">
						<x-slot:icon>
							<span class="font-semibold text-zinc-500 dark:text-zinc-400 select-none">@</span>
						</x-slot:icon>
					</x-input>
				</div>

				<x-input wire:model="email" id="email" type="email" label="Email Address" required :error="$errors->first('email')">
					<x-slot:suffix>
						@if($user?->hasVerifiedEmail())
							<x-badge variant="success" pill class="gap-1 text-[11px] py-0.5 px-2">
								<svg class="w-3 h-3 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
								</svg>
								Verified
							</x-badge>
						@else
							<x-badge variant="warning" pill class="gap-1 text-[11px] py-0.5 px-2">
								<svg class="w-3 h-3 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
								</svg>
								Unverified
							</x-badge>
						@endif
					</x-slot:suffix>
				</x-input>

				<!-- Organization & Department Info -->
                @unlessRole('supplier')
				<div class="p-3.5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/40 space-y-2">
					<div class="flex items-center justify-between">
						<span class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 flex items-center gap-1.5">
							<svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-1-4h.01M9 16h.01M9 12h.01M9 8h.01M15 16h.01M15 12h.01M15 8h.01"/></svg>
							Organization & Department
						</span>
					</div>

					<p class="text-xs text-zinc-600 dark:text-zinc-400 font-medium">
						{{ $user->agencyOfficer?->agency?->name ?? 'Acquisitions & Supplier Operations' }}
						@if($user->agencyOfficer?->subagency?->name)
							&bull; {{ $user->agencyOfficer->subagency->name }}
						@endif
					</p>
					<p class="text-[11px] text-zinc-400 dark:text-zinc-500 italic">
						To request a change to your assigned Agency or Department, please contact a System Administrator.
					</p>
				</div>
                @endunlessRole
			</div>

			<x-slot:footer>
				<x-button class="cursor-pointer" type="submit" loadingTarget="updateInformation">Save Changes</x-button>
			</x-slot:footer>
		</x-card>
	</form>
</div>