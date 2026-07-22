<?php

use App\Models\Acquisition;
use App\Models\Agency;
use App\Models\Subagency;
use App\Models\VotType;
use App\Enums\AcquisitionType;
use App\Enums\AcquisitionMethod;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    // ── Table / Search ──────────────────────────────────────────────────────
    public string $search = '';
    public string $filterType = '';
    public string $filterMethod = '';
    public string $filterStatus = '';
    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';

    // ── Local Delete state ──────────────────────────────────────────────────
    public bool $showDeleteConfirm = false;
    public ?int $deletingId = null;

    // ── Lifecycle ───────────────────────────────────────────────────────────
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function updatingFilterMethod(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    #[On('acquisition-saved')]
    public function refreshTable(): void
    {
        // Simply refreshes computed properties and table view
    }

    // ── Computed ────────────────────────────────────────────────────────────
    #[Computed]
    public function acquisitions(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Acquisition::query()
            ->with(['agency', 'subagency', 'votType'])
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('project_name', 'like', "%{$this->search}%")
                    ->orWhere('project_number', 'like', "%{$this->search}%")
                    ->orWhere('tender_number', 'like', "%{$this->search}%");
            }))
            ->when($this->filterType, fn ($q) => $q->where('type', $this->filterType))
            ->when($this->filterMethod, fn ($q) => $q->where('method', $this->filterMethod))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(10);
    }

    #[Computed]
    public function acquisitionTypes(): array
    {
        return AcquisitionType::cases();
    }

    #[Computed]
    public function acquisitionMethods(): array
    {
        return AcquisitionMethod::cases();
    }

    #[Computed]
    public function totalCount(): int
    {
        return Acquisition::count();
    }

    #[Computed]
    public function draftCount(): int
    {
        return Acquisition::where('status', 'DRAF')->count();
    }

    #[Computed]
    public function activeCount(): int
    {
        return Acquisition::whereIn('status', ['DIKEMUKAKAN', 'DILULUSKAN'])->count();
    }

    // ── Sorting ─────────────────────────────────────────────────────────────
    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $column;
            $this->sortDir = 'asc';
        }

        $this->resetPage();
    }

    // ── CRUD ────────────────────────────────────────────────────────────────
    public function confirmDelete(int $id): void
    {
        $this->deletingId        = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            Acquisition::findOrFail($this->deletingId)->delete();
            session()->flash('success', 'Acquisition deleted successfully.');
            $this->dispatch('acquisition-deleted');
        }

        $this->showDeleteConfirm = false;
        $this->deletingId        = null;
        $this->resetPage();
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deletingId        = null;
    }
};
?>

{{-- ── Root wrapper ── --}}
<div class="space-y-6">

    {{-- ── Flash message ── --}}
    @if(session('success'))
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 4000)"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/50 text-emerald-700 dark:text-emerald-400 text-sm font-medium shadow-xs"
        >
            <x-heroicon-o-check class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" />
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Stat summary cards ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <x-card>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Total Acquisitions</span>
                <span class="text-emerald-600 dark:text-emerald-400 p-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/30">
                    <x-heroicon-o-clipboard class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $this->totalCount }}</h3>
                <p class="text-xs text-zinc-500 mt-1">All time records</p>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Draft</span>
                <span class="text-amber-600 dark:text-amber-400 p-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/30">
                    <x-heroicon-o-pencil class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $this->draftCount }}</h3>
                <p class="text-xs text-zinc-500 mt-1">Pending submission</p>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Active</span>
                <span class="text-blue-600 dark:text-blue-400 p-1.5 rounded-xl bg-blue-50 dark:bg-blue-950/30">
                    <x-heroicon-o-check-circle class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </span>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $this->activeCount }}</h3>
                <p class="text-xs text-zinc-500 mt-1">Submitted or approved</p>
            </div>
        </x-card>
    </div>

    {{-- ── Main table card ── --}}
    <x-card>
        <x-slot:header>
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full mb-5">
                {{-- Search --}}
                <div class="relative flex-1 max-w-xs">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </div>
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="search"
                        placeholder="Search projects..."
                        class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500 py-2 pl-10 pr-3.5 text-sm transition-all focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:outline-none"
                    >
                </div>

                {{-- Filters --}}
                <div class="flex items-center gap-2 flex-wrap">
                    <select wire:model.live="filterType" class="rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 py-2 px-3 text-xs font-medium focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400">
                        <option value="">All Types</option>
                        @foreach($this->acquisitionTypes as $t)
                            <option value="{{ $t->value }}">{{ $t->value }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="filterMethod" class="rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 py-2 px-3 text-xs font-medium focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400">
                        <option value="">All Methods</option>
                        @foreach($this->acquisitionMethods as $m)
                            <option value="{{ $m->value }}">{{ $m->value }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="filterStatus" class="rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 py-2 px-3 text-xs font-medium focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400">
                        <option value="">All Statuses</option>
                        <option value="DRAF">Draf</option>
                        <option value="DIKEMUKAKAN">Dikemukakan</option>
                        <option value="DILULUSKAN">Diluluskan</option>
                        <option value="DITOLAK">Ditolak</option>
                        <option value="DIBATALKAN">Dibatalkan</option>
                    </select>
                </div>

                {{-- Add Button --}}
                <x-button variant="primary" size="sm" wire:click="$dispatch('open-acquisition-drawer', { mode: 'create' })" class="ml-auto shrink-0 cursor-pointer">
                    <x-heroicon-o-plus class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    New Acquisition
                </x-button>
            </div>
        </x-slot:header>

        {{-- Table --}}
        <div class="overflow-x-auto rounded-2xl border border-zinc-100 dark:border-zinc-800/80 shadow-xs">
            <table class="min-w-full divide-y divide-zinc-100 dark:divide-zinc-800/50">
                <thead class="bg-zinc-50/70 dark:bg-zinc-800/40">
                    <tr>
                        <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            <button wire:click="sort('project_number')" class="flex items-center gap-1 cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                                Project
                                @if($sortBy === 'project_number')
                                    @if($sortDir === 'asc')
                                        <x-heroicon-o-arrow-up class="w-3 h-3 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" />
                                    @else
                                        <x-heroicon-o-arrow-down class="w-3 h-3 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" />
                                    @endif
                                @else
                                    <x-heroicon-o-arrows-up-down class="w-3 h-3 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Type / Method</th>
                        <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            <button wire:click="sort('siling_price')" class="flex items-center gap-1 cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                                Ceiling Price
                                @if($sortBy === 'siling_price')
                                    @if($sortDir === 'asc')
                                        <x-heroicon-o-arrow-up class="w-3 h-3 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" />
                                    @else
                                        <x-heroicon-o-arrow-down class="w-3 h-3 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" />
                                    @endif
                                @else
                                    <x-heroicon-o-arrows-up-down class="w-3 h-3 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Agency</th>
                        <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-5 py-3.5 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-100 dark:divide-zinc-800/50">
                    @forelse($this->acquisitions as $acquisition)
                        <tr wire:key="acq-{{ $acquisition->id }}" class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/20 transition-colors">
                            <td class="px-5 py-4">
                                <div class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm">{{ $acquisition->project_name }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 font-mono">{{ $acquisition->project_number }}</div>
                                @if($acquisition->tender_number)
                                    <div class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5 font-sans">Tender: {{ $acquisition->tender_number }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-col gap-1.5">
                                    @if($acquisition->type)
                                        <x-badge variant="primary">
                                            {{ $acquisition->type instanceof \App\Enums\AcquisitionType ? $acquisition->type->value : $acquisition->type }}
                                        </x-badge>
                                    @endif
                                    @if($acquisition->method)
                                        <x-badge variant="secondary">
                                            {{ $acquisition->method instanceof \App\Enums\AcquisitionMethod ? $acquisition->method->value : $acquisition->method }}
                                        </x-badge>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap font-mono text-sm text-zinc-700 dark:text-zinc-300">
                                @if($acquisition->siling_price !== null)
                                    RM {{ number_format((float) $acquisition->siling_price, 2) }}
                                @else
                                    <span class="text-zinc-400 dark:text-zinc-650">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($acquisition->agency)
                                    <div class="text-sm text-zinc-700 dark:text-zinc-300">{{ $acquisition->agency->name }}</div>
                                @endif
                                @if($acquisition->subagency)
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $acquisition->subagency->name }}</div>
                                @endif
                                @if(!$acquisition->agency)
                                    <span class="text-zinc-400 dark:text-zinc-650 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @php
                                    $statusVariant = match($acquisition->status) {
                                        'DILULUSKAN'  => 'success',
                                        'DIKEMUKAKAN' => 'info',
                                        'DRAF'        => 'warning',
                                        'DITOLAK'     => 'danger',
                                        'DIBATALKAN'  => 'secondary',
                                        default       => 'secondary',
                                    };
                                    $statusLabel = match($acquisition->status) {
                                        'DILULUSKAN'  => 'Diluluskan',
                                        'DIKEMUKAKAN' => 'Dikemukakan',
                                        'DRAF'        => 'Draf',
                                        'DITOLAK'     => 'Ditolak',
                                        'DIBATALKAN'  => 'Dibatalkan',
                                        default       => $acquisition->status ?? '—',
                                    };
                                  @endphp
                                <x-badge :variant="$statusVariant" pill>{{ $statusLabel }}</x-badge>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                    {{-- Full Page View / Edit --}}
                                    <a
                                        href="{{ route('acquisition.show', $acquisition) }}"
                                        title="Open Full Page (Tabs)"
                                    >
                                        <button class="p-1.5 rounded-lg cursor-pointer text-zinc-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 dark:hover:text-indigo-400 transition-all">
                                            <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                        </button>
                                    </a>
                                    {{-- Quick View (Drawer) --}}
                                    <button
                                        wire:click="$dispatch('open-acquisition-drawer', { mode: 'view', id: {{ $acquisition->id }} })"
                                        title="Quick View (Drawer)"
                                        class="p-1.5 rounded-lg cursor-pointer text-zinc-400 hover:text-sky-600 hover:bg-sky-50 dark:hover:bg-sky-950/30 dark:hover:text-sky-400 transition-all"
                                    >
                                        <x-heroicon-o-eye class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                    </button>
                                    {{-- Quick Edit (Drawer) --}}
                                    <button
                                        wire:click="$dispatch('open-acquisition-drawer', { mode: 'edit', id: {{ $acquisition->id }} })"
                                        title="Quick Edit (Drawer)"
                                        class="p-1.5 rounded-lg cursor-pointer text-zinc-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 dark:hover:text-emerald-400 transition-all"
                                    >
                                        <x-heroicon-o-pencil class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                    </button>
                                    {{-- Delete --}}
                                    <button
                                        wire:click="confirmDelete({{ $acquisition->id }})"
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
                            <td colspan="6" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <span class="w-14 h-14 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                        <x-heroicon-o-clipboard class="w-7 h-7 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" />
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">No acquisitions found</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Try adjusting your search or filters, or create a new acquisition.</p>
                                    </div>
                                    <x-button variant="primary" size="sm" wire:click="$dispatch('open-acquisition-drawer', { mode: 'create' })" class="cursor-pointer">
                                        <x-heroicon-o-plus class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                        New Acquisition
                                    </x-button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($this->acquisitions->hasPages())
            <div class="mt-5 pt-4 border-t border-zinc-100 dark:border-zinc-800/50">
                {{ $this->acquisitions->links() }}
            </div>
        @endif
    </x-card>

    {{-- ══════════════════════════════════════════════════════════════════════
         Delete Confirmation Modal
    ══════════════════════════════════════════════════════════════════════ --}}
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
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Delete Acquisition</h3>
                    <p class="text-xs text-zinc-550 dark:text-zinc-400 mt-1">This action cannot be undone. The acquisition record will be permanently removed.</p>
                </div>
            </div>
            <div class="mt-5 flex gap-3 justify-end">
                <x-button variant="outline" size="sm" wire:click="cancelDelete">Cancel</x-button>
                <x-button variant="danger" size="sm" wire:click="delete" wire:loading.attr="disabled" wire:target="delete">
                    <span wire:loading.remove wire:target="delete">Delete</span>
                    <span wire:loading wire:target="delete">Deleting...</span>
                </x-button>
            </div>
        </div>
    </div>

</div>