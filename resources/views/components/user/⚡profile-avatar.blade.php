<?php

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
	use WithFileUploads;

	/**
	 * @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null
	 */
	public $avatar = null;

	public ?int $userId = null;

	public string $successMessage = '';

	public string $errorMessage = '';

	public function mount(?User $user): void
	{
		$resolvedUser = $user ?? auth()->user();

		if (! $resolvedUser) {
			abort(403, 'Unauthorized');
		}

		$this->userId = $resolvedUser->id;
	}

	#[Computed]
	public function resolvedUser(): ?User
	{
		return ($this->userId ? User::find($this->userId) : null) ?? auth()->user();
	}

	#[Computed]
	public function hasAvatar(): bool
	{
		return (bool) $this->resolvedUser?->avatar_url;
	}

	#[Computed]
	public function avatarSrc(): string
	{
		return $this->hasAvatar
			? Storage::url($this->resolvedUser->avatar_url)
			: '';
	}

	#[Computed]
	public function initials(): string
	{
		return $this->resolvedUser?->initials() ?? '';
	}

	#[Computed]
	public function avatarColor(): string
	{
		return $this->resolvedUser?->avatar_color ?? '#6b7280';
	}

	/**
	 * Fires automatically once Livewire finishes uploading the temp file.
	 * Validates, permanently stores, and updates the user record in one step.
	 */
	public function updatedAvatar(): void
	{
		$this->successMessage = '';
		$this->errorMessage = '';

		$this->validate([
			'avatar' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
		]);

		$user = $this->resolvedUser;

		if (! $user) {
			return;
		}

		// Remove old file to prevent orphans
		if ($user->avatar_url) {
			Storage::disk('public')->delete($user->avatar_url);
		}

		// Derive extension from MIME type — never from user-supplied filename
		$extension = match ($this->avatar->getMimeType()) {
			'image/png' => 'png',
			'image/webp' => 'webp',
			default => 'jpg',
		};

		$path = $this->avatar->storeAs('avatars', $user->uuid.'.'.$extension, 'public');
		$user->update(['avatar_url' => $path]);

		$this->avatar = null;

		$msg = 'Avatar updated successfully.';
		$this->successMessage = $msg;
		session()->flash('message', $msg);
		$this->dispatch('avatar-updated');
	}

	public function removeAvatar(): void
	{
		$this->successMessage = '';
		$this->errorMessage = '';

		$user = $this->resolvedUser;

		if (! $user) {
			return;
		}

		if ($user->avatar_url) {
			Storage::disk('public')->delete($user->avatar_url);
			$user->update(['avatar_url' => null]);
		}

		$msg = 'Avatar removed.';
		$this->successMessage = $msg;
		session()->flash('message', $msg);
		$this->dispatch('avatar-updated');
	}
};
?>

<div class="relative shrink-0 group">

	{{-- ── Avatar display ────────────────────────────────────────────────── --}}
	<div class="relative h-24 w-24">

		@if($this->hasAvatar)
			<img
				class="h-24 w-24 rounded-2xl object-cover ring-4 ring-emerald-500/10"
				src="{{ $this->avatarSrc }}"
				alt="Profile avatar"
			>
		@else
			<div
				class="flex h-24 w-24 items-center justify-center rounded-2xl ring-4 ring-emerald-500/10"
				style="background-color: {{ $this->avatarColor }};"
			>
				<span class="text-3xl font-bold leading-none text-white select-none">
					{{ $this->initials }}
				</span>
			</div>
		@endif

		{{-- Spinner overlay — wire:loading.flex forces display:flex so centering works --}}
		<div
			wire:loading.flex
			wire:target="avatar,removeAvatar"
			class="absolute inset-0 hidden items-center justify-center rounded-2xl bg-black/50 backdrop-blur-sm"
		>
			<x-icon-spinner class="h-6 w-6 animate-spin text-white" fill="none" viewBox="0 0 24 24" />
		</div>

		{{-- Remove button — appears on group-hover when avatar is set --}}
		@if($this->hasAvatar)
			<button
				wire:click="removeAvatar"
				wire:confirm="Remove your profile picture?"
				wire:loading.attr="disabled"
				wire:target="avatar,removeAvatar"
				class="absolute -right-1.5 -top-1.5 z-10 cursor-pointer rounded-full bg-rose-500 p-1 text-white opacity-0 shadow-sm transition-all duration-200 hover:bg-rose-600 group-hover:opacity-100 disabled:cursor-not-allowed"
				title="Remove avatar"
			>
				<x-heroicon-o-x-mark class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" />
			</button>
		@endif
	</div>

	{{-- ── Camera / upload button ─────────────────────────────────────────── --}}
	{{-- Using <label> so clicking it opens the file picker natively --}}
	<label
		wire:loading.attr="disabled"
		wire:loading.class="opacity-50 cursor-not-allowed"
		wire:target="avatar,removeAvatar"
		class="absolute -bottom-1 -right-1 cursor-pointer rounded-lg border border-zinc-200 bg-white p-1.5 text-zinc-500 shadow-sm transition-colors duration-150 hover:text-zinc-700 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:text-zinc-300"
		title="Change avatar"
	>
		<x-heroicon-o-camera class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />

		{{-- Hidden file input wired to the avatar property --}}
		{{-- x-on:change resets the value so re-selecting a file always triggers the change event --}}
		<input
			type="file"
			wire:model="avatar"
			accept="image/jpeg,image/jpg,image/png,image/webp"
			class="sr-only"
			x-on:change="$nextTick(() => { $el.value = '' })"
		>
	</label>

	{{-- ── Session & Validation Alerts ───────────────────────────────────── --}}
	@if ($successMessage || session()->has('message'))
		<div class="absolute left-0 top-full z-20 mt-2 w-56">
			<x-alert wire:key="avatar-success-{{ microtime() }}" variant="success" dismissible>
				{{ $successMessage ?: session('message') }}
			</x-alert>
		</div>
	@endif

	@if ($errorMessage || session()->has('error'))
		<div class="absolute left-0 top-full z-20 mt-2 w-56">
			<x-alert wire:key="avatar-error-{{ microtime() }}" variant="error" dismissible>
				{{ $errorMessage ?: session('error') }}
			</x-alert>
		</div>
	@endif

	@error('avatar')
		<div class="absolute left-0 top-full z-20 mt-2 w-56">
			<x-alert wire:key="avatar-validation-{{ microtime() }}" variant="error" dismissible>
				{{ $message }}
			</x-alert>
		</div>
	@enderror

</div>