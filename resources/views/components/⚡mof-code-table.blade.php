<?php

use App\Models\MofCode;
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

    #[On('mof-code-saved')]
    public function refreshTable(): void
    {
    }

    #[Computed]
    public function mofCodes(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return MofCode::query()
            ->with(['subcategory', 'category'])
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('code', 'like', "%{$this->search}%");
            }))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(10);
    }

    #[Computed]
    public function totalCount(): int
    {
        return MofCode::count();
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
            MofCode::findOrFail($this->deletingId)->delete();
            session()->flash('success', 'MOF Code deleted successfully.');
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
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Total MOF Field Codes</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mt-1">{{ $this->totalCount }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <x-heroicon-o-tag class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </div>
            </div>
        </x-card>
    </div>

    <x-card>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 mb-6">
            <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search MOF code or title..."
                       class="w-full pl-10 pr-4 py-2 text-sm bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded-xl text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all" />
            </div>

            <x-button variant="primary" size="sm" @click="$dispatch('open-mof-code-drawer', { mode: 'create' })">
                <x-heroicon-o-plus class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                Add MOF Code
            </x-button>
        </div>

        <x-table :headers="['MOF Code', 'Title / Field Name', 'Subcategory', 'Category', 'Actions']">
            @forelse($this->mofCodes as $code)
                <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/20 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap font-mono text-xs font-bold text-emerald-700 dark:text-emerald-400">
                        {{ $code->code }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap font-semibold text-zinc-900 dark:text-zinc-100 text-sm">
                        {{ $code->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-650 dark:text-zinc-300">
                        {{ $code->subcategory?->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-650 dark:text-zinc-300">
                        {{ $code->category?->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-1">
                            <button
                                wire:click="$dispatch('open-mof-code-drawer', { mode: 'view', id: {{ $code->id }} })"
                                title="View"
                                class="p-1.5 rounded-lg cursor-pointer text-zinc-400 hover:text-sky-600 hover:bg-sky-50 dark:hover:bg-sky-950/30 dark:hover:text-sky-400 transition-all"
                            >
                                <x-heroicon-o-eye class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            </button>
                            <button
                                wire:click="$dispatch('open-mof-code-drawer', { mode: 'edit', id: {{ $code->id }} })"
                                title="Edit"
                                class="p-1.5 rounded-lg cursor-pointer text-zinc-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 dark:hover:text-emerald-400 transition-all"
                            >
                                <x-heroicon-o-pencil class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            </button>
                            <button
                                wire:click="confirmDelete({{ $code->id }})"
                                title="Delete"
                                class="p-1.5 rounded-lg cursor-pointer text-zinc-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 dark:hover:text-rose-400 transition-all"
                            >
                                <x-heroicon-o-trash class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <span class="w-14 h-14 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                <x-heroicon-o-clipboard class="w-7 h-7 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" />
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">No MOF codes found</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Try adjusting your search query.</p>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-table>

        <div class="mt-4">
            {{ $this->mofCodes->links() }}
        </div>
    </x-card>

    <div
        x-data
        x-show="$wire.showDeleteConfirm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;"
        role="dialog"
        aria-modal="true"
    >
        <div class="fixed inset-0 bg-zinc-950/60 backdrop-blur-sm" wire:click="cancelDelete"></div>
        <div
            x-show="$wire.showDeleteConfirm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative z-10 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200/80 dark:border-zinc-800/80 p-6 w-full max-w-sm"
        >
            <div class="flex items-start gap-4">
                <span class="shrink-0 w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/40 flex items-center justify-center">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </span>
                <div>
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Delete MOF Code</h3>
                    <p class="text-xs text-zinc-550 dark:text-zinc-400 mt-1">Are you sure you want to delete this MOF code?</p>
                </div>
            </div>
            <div class="mt-5 flex gap-3 justify-end">
                <x-button variant="outline" size="sm" wire:click="cancelDelete">Cancel</x-button>
                <x-button variant="primary" size="sm" class="bg-rose-600 hover:bg-rose-700 text-white border-transparent" wire:click="delete">Delete</x-button>
            </div>
        </div>
    </div>
</div>
