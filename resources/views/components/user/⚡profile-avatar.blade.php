<?php

use Livewire\Component;

use App\Models\User;

new class extends Component
{
	private ?User $user = null;

	public function mount(): void
	{
		$this->user = auth()->user();
	}
};
?>

<div class="relative shrink-0 group">
	<img class="h-24 w-24 rounded-2xl object-cover ring-4 ring-emerald-500/10" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Avatar">
	<button class="absolute -bottom-1 -right-1 p-1.5 rounded-lg bg-white dark:bg-zinc-800 text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 border border-zinc-200 dark:border-zinc-700 shadow-sm cursor-pointer">
		<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
			<path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
			<path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
		</svg>
	</button>
</div>