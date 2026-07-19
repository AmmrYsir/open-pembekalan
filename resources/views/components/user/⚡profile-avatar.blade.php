<?php

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
	use WithFileUploads;

	/**
	 * @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null
	 */
	public $avatar = null;

	/**
	 * Fires automatically once Livewire finishes uploading the temp file.
	 * Validates, permanently stores, and updates the user record in one step.
	 */
	public function updatedAvatar(): void
	{
		$this->validate([
			'avatar' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
		]);

		/** @var User|null $user */
		$user = auth()->user();

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

		session()->flash('message', 'Avatar updated successfully.');
		$this->dispatch('avatar-updated');
	}

	public function removeAvatar(): void
	{
		/** @var User|null $user */
		$user = auth()->user();

		if (! $user) {
			return;
		}

		if ($user->avatar_url) {
			Storage::disk('public')->delete($user->avatar_url);
			$user->update(['avatar_url' => null]);
		}

		session()->flash('message', 'Avatar removed.');
		$this->dispatch('avatar-updated');
	}
};
?>

<div class="relative shrink-0 group">

	@php
		/** @var \App\Models\User|null $user */
		$user = auth()->user();

		if ($user?->avatar_url) {
			$avatarSrc = Storage::url($user->avatar_url);
		} elseif ($user) {
			$initials  = htmlspecialchars($user->initials(), ENT_XML1);
			$color     = htmlspecialchars($user->avatar_color, ENT_XML1);
			$svg       = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96">'
				.'<rect width="96" height="96" fill="'.$color.'"/>'
				.'<text x="50%" y="50%" dy="0.35em" text-anchor="middle" dominant-baseline="central"'
				.' fill="white" font-size="34" font-weight="700"'
				.' font-family="ui-sans-serif,system-ui,sans-serif">'.$initials.'</text>'
				.'</svg>';
			$avatarSrc = 'data:image/svg+xml;base64,'.base64_encode($svg);
		} else {
			$avatarSrc = '';
		}
	@endphp

	{{-- ── Avatar display ────────────────────────────────────────────────── --}}
	<div class="relative">
		<img
			class="h-24 w-24 rounded-2xl object-cover ring-4 ring-emerald-500/10"
			src="{{ $avatarSrc }}"
			alt="Profile avatar"
		>

		{{-- Spinner overlay — shown during temp upload AND during the save action --}}
		<div
			wire:loading
			wire:target="avatar,removeAvatar"
			class="absolute inset-0 flex items-center justify-center rounded-2xl bg-black/50 backdrop-blur-sm"
		>
			<svg class="h-6 w-6 animate-spin text-white" fill="none" viewBox="0 0 24 24">
				<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
				<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
			</svg>
		</div>

		{{-- Remove button — appears on group-hover when avatar is set --}}
		@if($user?->avatar_url)
			<button
				wire:click="removeAvatar"
				wire:confirm="Remove your profile picture?"
				wire:loading.attr="disabled"
				wire:target="avatar,removeAvatar"
				class="absolute -right-1.5 -top-1.5 z-10 cursor-pointer rounded-full bg-rose-500 p-1 text-white opacity-0 shadow-sm transition-all duration-200 hover:bg-rose-600 group-hover:opacity-100 disabled:cursor-not-allowed"
				title="Remove avatar"
			>
				<svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
					<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
				</svg>
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
		<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
			<path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
			<path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
		</svg>

		{{-- Hidden file input wired to the avatar property --}}
		<input
			type="file"
			wire:model="avatar"
			accept="image/jpeg,image/jpg,image/png,image/webp"
			class="sr-only"
		>
	</label>

	{{-- ── Validation error tooltip ──────────────────────────────────────── --}}
	@error('avatar')
		<div class="absolute left-0 top-full z-20 mt-2 w-52 rounded-xl border border-rose-100 bg-white p-2.5 shadow-lg dark:border-rose-900/30 dark:bg-zinc-900">
			<p class="flex items-start gap-1.5 text-xs text-rose-600 dark:text-rose-400">
				<svg class="mt-0.5 h-3.5 w-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
					<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
				</svg>
				{{ $message }}
			</p>
		</div>
	@enderror

</div>