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

    {{-- ── Flash Notification ── --}}
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
            class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/50 text-emerald-700 dark:text-emerald-400 text-sm font-medium flex items-center gap-2 shadow-xs"
        >
            <x-heroicon-o-check-circle class="w-5 h-5 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Header Card ── --}}
    <x-card class="!p-4 sm:!p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/40">
                        <x-heroicon-o-clipboard-document-list class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </span>
                    <h1 class="text-xl font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">
                        Procurement Acquisitions
                    </h1>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    Management and tracking of procurement acquisition projects, methods, and specifications.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <x-button variant="primary" size="sm" wire:click="$dispatch('open-acquisition-drawer', { mode: 'create' })" class="cursor-pointer shadow-xs">
                    <x-heroicon-o-plus class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    New Acquisition
                </x-button>
            </div>
        </div>
    </x-card>

    {{-- ── Stat Summary Cards ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        {{-- Card 1: Total --}}
        <div class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-zinc-500">
                <span>Total Acquisitions</span>
                <x-heroicon-o-clipboard-document-list class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
            </div>
            <div class="text-2xl font-bold font-mono text-zinc-900 dark:text-zinc-100">
                {{ $this->totalCount }} Records
            </div>
            <p class="text-xs text-zinc-400 font-mono">All Time History</p>
        </div>

        {{-- Card 2: Draft --}}
        <div class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-zinc-500">
                <span>Draft Proposals</span>
                <x-heroicon-o-pencil-square class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
            </div>
            <div class="text-2xl font-bold font-mono text-amber-600 dark:text-amber-400">
                {{ $this->draftCount }} Drafts
            </div>
            <p class="text-xs text-amber-600 dark:text-amber-400 font-mono">Pending Submission</p>
        </div>

        {{-- Card 3: Active --}}
        <div class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-zinc-500">
                <span>Active / Approved</span>
                <x-heroicon-o-check-circle class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
            </div>
            <div class="text-2xl font-bold font-mono text-blue-600 dark:text-blue-400">
                {{ $this->activeCount }} Active
            </div>
            <p class="text-xs text-blue-600 dark:text-blue-400 font-mono">Submitted & Approved</p>
        </div>
    </div>

    {{-- ── Filter Toolbar ── --}}
    <x-card class="!p-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 text-sm">
            {{-- Search Bar --}}
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by project name, project number, or tender number..."
                    class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 py-2.5 pl-10 pr-3.5 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500"
                >
            </div>

            {{-- Select Filters --}}
            <div class="flex items-center gap-3 flex-wrap">
                <select wire:model.live="filterType" class="rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-xs py-2 px-3 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    <option value="">All Types</option>
                    @foreach($this->acquisitionTypes as $t)
                        <option value="{{ $t->value }}">{{ $t->value }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterMethod" class="rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-xs py-2 px-3 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    <option value="">All Methods</option>
                    @foreach($this->acquisitionMethods as $m)
                        <option value="{{ $m->value }}">{{ $m->value }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterStatus" class="rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-xs py-2 px-3 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    <option value="">All Statuses</option>
                    <option value="DRAF">Draf</option>
                    <option value="DIKEMUKAKAN">Dikemukakan</option>
                    <option value="DILULUSKAN">Diluluskan</option>
                    <option value="DITOLAK">Ditolak</option>
                    <option value="DIBATALKAN">Dibatalkan</option>
                </select>
            </div>
        </div>
    </x-card>

    {{-- ── Main Table ── --}}
    <div class="overflow-x-auto rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs">
        <table class="min-w-full divide-y divide-zinc-100 dark:divide-zinc-800 text-sm">
            <thead class="bg-zinc-50/70 dark:bg-zinc-800/40">
                <tr>
                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">
                        <button wire:click="sort('project_number')" class="flex items-center gap-1 cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                            Project Title & Reference
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
                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Type / Method</th>
                    <th scope="col" class="px-4 py-3.5 text-center text-xs font-semibold text-zinc-500 uppercase tracking-wider">
                        <button wire:click="sort('siling_price')" class="flex items-center gap-1 mx-auto cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
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
                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Agency & Department</th>
                    <th scope="col" class="px-4 py-3.5 text-center text-xs font-semibold text-zinc-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold text-zinc-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse($this->acquisitions as $acquisition)
                    <tr wire:key="acq-{{ $acquisition->id }}" class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/20 transition-colors">
                        {{-- Ref Code & Title --}}
                        <td class="px-4 py-4 max-w-md">
                            <a href="{{ route('acquisition.show', $acquisition) }}" class="font-bold text-zinc-900 dark:text-zinc-100 text-sm line-clamp-2 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                                {{ $acquisition->project_name }}
                            </a>
                            <div class="flex items-center gap-2.5 text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ $acquisition->project_number }}
                                </span>
                                @if($acquisition->tender_number)
                                    <span class="text-zinc-400 dark:text-zinc-500 font-sans">• Tender: {{ $acquisition->tender_number }}</span>
                                @endif
                            </div>
                        </td>

                        {{-- Type / Method --}}
                        <td class="px-4 py-4 whitespace-nowrap space-y-1">
                            @if($acquisition->type)
                                <x-badge variant="primary">
                                    {{ $acquisition->type instanceof \App\Enums\AcquisitionType ? $acquisition->type->value : $acquisition->type }}
                                </x-badge>
                            @endif
                            @if($acquisition->method)
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 font-mono">
                                    {{ $acquisition->method instanceof \App\Enums\AcquisitionMethod ? $acquisition->method->value : $acquisition->method }}
                                </div>
                            @endif
                        </td>

                        {{-- Ceiling Price --}}
                        <td class="px-4 py-4 whitespace-nowrap text-center font-mono font-bold text-xs text-zinc-900 dark:text-zinc-100">
                            @if($acquisition->siling_price !== null)
                                RM {{ number_format((float) $acquisition->siling_price, 2) }}
                            @else
                                <span class="text-zinc-400 dark:text-zinc-650">—</span>
                            @endif
                        </td>

                        {{-- Agency & Subagency --}}
                        <td class="px-4 py-4">
                            @if($acquisition->agency)
                                <div class="flex items-center gap-1.5 text-xs font-semibold text-zinc-900 dark:text-zinc-100">
                                    <x-heroicon-o-building-office-2 class="w-3.5 h-3.5 text-zinc-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                    {{ $acquisition->agency->name }}
                                </div>
                            @endif
                            @if($acquisition->subagency)
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 pl-5">{{ $acquisition->subagency->name }}</div>
                            @endif
                            @if(!$acquisition->agency && !$acquisition->subagency)
                                <span class="text-zinc-400 dark:text-zinc-650 text-xs">—</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-4 whitespace-nowrap text-center">
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

                        {{-- Actions --}}
                        <td class="px-4 py-4 whitespace-nowrap text-right space-x-1">
                            {{-- View Link (Full page) --}}
                            <a
                                href="{{ route('acquisition.show', $acquisition) }}"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors border border-emerald-200 dark:border-emerald-800/40"
                                title="View Acquisition Details"
                            >
                                <x-heroicon-o-eye class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                View
                            </a>

                            {{-- Edit Button (Drawer) --}}
                            <button
                                wire:click="$dispatch('open-acquisition-drawer', { mode: 'edit', id: {{ $acquisition->id }} })"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors cursor-pointer"
                                title="Edit Acquisition"
                            >
                                <x-heroicon-o-pencil class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                Edit
                            </button>

                            {{-- Delete Button --}}
                            <button
                                wire:click="confirmDelete({{ $acquisition->id }})"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-semibold bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/50 transition-colors border border-rose-200 dark:border-rose-800/40 cursor-pointer"
                                title="Delete Acquisition"
                            >
                                <x-heroicon-o-trash class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <span class="w-12 h-12 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-400">
                                    <x-heroicon-o-clipboard-document-list class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" />
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

    {{-- ── Pagination ── --}}
    @if($this->acquisitions->hasPages())
        <div class="mt-4">
            {{ $this->acquisitions->links() }}
        </div>
    @endif

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