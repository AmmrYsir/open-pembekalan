<?php

use App\Models\Committee;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';

    public bool $showDeleteConfirm = false;
    public ?int $deletingId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[On('committee-saved')]
    public function refreshTable(): void
    {
    }

    #[Computed]
    public function committees(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Committee::query()
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('slug', 'like', "%{$this->search}%");
            }))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(10);
    }

    #[Computed]
    public function totalCount(): int
    {
        return Committee::count();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }

        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            Committee::findOrFail($this->deletingId)->delete();
            session()->flash('success', 'Committee deleted successfully.');
        }

        $this->showDeleteConfirm = false;
        $this->deletingId = null;
        $this->resetPage();
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deletingId = null;
    }
};
?>

<div class="space-y-6">
    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
             class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/50 text-emerald-700 dark:text-emerald-400 text-sm font-medium shadow-xs">
            <x-heroicon-o-check class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" />
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <x-ui.card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Total Committees</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-1">{{ $this->totalCount }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <x-heroicon-o-academic-cap class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </div>
            </div>
        </x-ui.card>
    </div>

    <x-ui.card>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 mb-6">
            <div class="relative flex-1 max-w-md">
                <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search committee name or slug..."
                       class="w-full pl-10 pr-4 py-2 text-sm bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded-xl text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all" />
            </div>

            <x-ui.button variant="primary" size="sm" @click="$dispatch('open-committee-drawer', { mode: 'create' })">
                <x-heroicon-o-plus class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                Add New Committee
            </x-ui.button>
        </div>

        <x-ui.table :headers="['Committee Name', 'Slug', 'Configured Positions', 'Actions']">
            @forelse($this->committees as $committee)
                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ $committee->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-zinc-600 dark:text-zinc-400 text-xs font-mono">
                        {{ $committee->slug }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if(is_array($committee->position) && count($committee->position) > 0)
                            <div class="flex flex-wrap gap-1">
                                @foreach(array_slice($committee->position, 0, 3) as $pos)
                                    <x-ui.badge variant="info">{{ $pos }}</x-ui.badge>
                                @endforeach
                                @if(count($committee->position) > 3)
                                    <x-ui.badge variant="secondary">+{{ count($committee->position) - 3 }} more</x-ui.badge>
                                @endif
                            </div>
                        @else
                            <span class="text-xs text-zinc-400">None configured</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <x-ui.button variant="outline" size="sm" @click="$dispatch('open-committee-drawer', { mode: 'view', id: {{ $committee->id }} })">
                                View
                            </x-ui.button>
                            <x-ui.button variant="outline" size="sm" @click="$dispatch('open-committee-drawer', { mode: 'edit', id: {{ $committee->id }} })">
                                Edit
                            </x-ui.button>
                            <x-ui.button variant="outline" size="sm" class="text-rose-600 dark:text-rose-400 hover:border-rose-300" wire:click="confirmDelete({{ $committee->id }})">
                                Delete
                            </x-ui.button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-zinc-500 dark:text-zinc-400 text-sm">
                        No committees found.
                    </td>
                </tr>
            @endforelse
        </x-ui.table>

        <div class="mt-4">
            {{ $this->committees->links() }}
        </div>
    </x-ui.card>

    @if($showDeleteConfirm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-950/40 backdrop-blur-xs">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 max-w-sm w-full space-y-4 shadow-xl">
                <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 text-lg">Confirm Delete</h3>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Are you sure you want to delete this committee?</p>
                <div class="flex justify-end gap-3 pt-2">
                    <x-ui.button variant="outline" size="sm" wire:click="cancelDelete">Cancel</x-ui.button>
                    <x-ui.button variant="primary" size="sm" class="bg-rose-600 hover:bg-rose-700 text-white border-transparent" wire:click="delete">Delete Committee</x-ui.button>
                </div>
            </div>
        </div>
    @endif
</div>
