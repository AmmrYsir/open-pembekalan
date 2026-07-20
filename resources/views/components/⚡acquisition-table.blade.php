<?php

use App\Enums\AcquisitionMethod;
use App\Enums\AcquisitionType;
use App\Models\Acquisition;
use App\Models\Agency;
use App\Models\Subagency;
use App\Models\VotType;
use Livewire\Attributes\Computed;
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

    // ── Panel state ─────────────────────────────────────────────────────────
    // mode: 'create' | 'view' | 'edit'
    public string $mode = 'create';
    public bool $showPanel = false;
    public bool $showDeleteConfirm = false;
    public ?int $activeId = null;
    public ?int $deletingId = null;

    // ── Form fields ─────────────────────────────────────────────────────────
    public string $type = '';
    public string $method = '';
    public string $project_number = '';
    public string $project_name = '';
    public string $status = '';
    public string $provision_type = '';
    public string $submission_type = '';
    public ?int $vot_type_id = null;
    public string $tender_number = '';
    public string $siling_price = '';
    public string $no_allocation_warrant = '';
    public ?int $agency_id = null;
    public ?int $subagency_id = null;
    public bool $is_required_kbp = false;
    public bool $mof_required = false;
    public bool $cidb_required = false;
    public string $committee_type = '';

    // ── Validation ──────────────────────────────────────────────────────────
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type'                  => ['required', 'string'],
            'method'                => ['required', 'string'],
            'project_number'        => ['required', 'string', 'max:100'],
            'project_name'          => ['required', 'string', 'max:255'],
            'status'                => ['required', 'string'],
            'provision_type'        => ['nullable', 'string', 'max:100'],
            'submission_type'       => ['nullable', 'string', 'max:100'],
            'vot_type_id'           => ['nullable', 'integer', 'exists:vot_types,id'],
            'tender_number'         => ['nullable', 'string', 'max:100'],
            'siling_price'          => ['nullable', 'numeric', 'min:0'],
            'no_allocation_warrant' => ['nullable', 'string', 'max:100'],
            'agency_id'             => ['nullable', 'integer', 'exists:agencies,id'],
            'subagency_id'          => ['nullable', 'integer', 'exists:subagencies,id'],
            'is_required_kbp'       => ['boolean'],
            'mof_required'          => ['boolean'],
            'cidb_required'         => ['boolean'],
            'committee_type'        => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function validationAttributes(): array
    {
        return [
            'type'                  => 'Acquisition Type',
            'method'                => 'Acquisition Method',
            'project_number'        => 'Project Number',
            'project_name'          => 'Project Name',
            'status'                => 'Status',
            'provision_type'        => 'Provision Type',
            'submission_type'       => 'Submission Type',
            'vot_type_id'           => 'VOT Type',
            'tender_number'         => 'Tender Number',
            'siling_price'          => 'Ceiling Price',
            'no_allocation_warrant' => 'Allocation Warrant No.',
            'agency_id'             => 'Agency',
            'subagency_id'          => 'Sub-Agency',
            'committee_type'        => 'Committee Type',
        ];
    }

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
    public function agencies(): \Illuminate\Database\Eloquent\Collection
    {
        return Agency::orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function subagencies(): \Illuminate\Database\Eloquent\Collection
    {
        return Subagency::when($this->agency_id, fn ($q) => $q->where('agency_id', $this->agency_id))
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    #[Computed]
    public function votTypes(): \Illuminate\Database\Eloquent\Collection
    {
        return VotType::orderBy('name')->get(['id', 'name', 'code']);
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

    // ── Panel open helpers ───────────────────────────────────────────────────
    public function openCreate(): void
    {
        $this->resetForm();
        $this->activeId   = null;
        $this->mode       = 'create';
        $this->showPanel  = true;
    }

    public function openView(int $id): void
    {
        $this->loadAcquisition($id);
        $this->activeId  = $id;
        $this->mode      = 'view';
        $this->showPanel = true;
    }

    public function openEdit(int $id): void
    {
        $this->loadAcquisition($id);
        $this->activeId  = $id;
        $this->mode      = 'edit';
        $this->showPanel = true;
    }

    /**
     * Switch from view mode → edit mode for the same record (inline toggle).
     */
    public function switchToEdit(): void
    {
        $this->mode = 'edit';
        $this->resetValidation();
    }

    /**
     * Switch from edit mode → view mode (discard unsaved changes, reload from DB).
     */
    public function switchToView(): void
    {
        if ($this->activeId) {
            $this->loadAcquisition($this->activeId);
        }

        $this->mode = 'view';
        $this->resetValidation();
    }

    // ── CRUD ────────────────────────────────────────────────────────────────
    public function save(): void
    {
        $this->validate();

        $data = [
            'type'                  => $this->type,
            'method'                => $this->method,
            'project_number'        => $this->project_number,
            'project_name'          => $this->project_name,
            'status'                => $this->status,
            'provision_type'        => $this->provision_type ?: null,
            'submission_type'       => $this->submission_type ?: null,
            'vot_type_id'           => $this->vot_type_id,
            'tender_number'         => $this->tender_number ?: null,
            'siling_price'          => $this->siling_price !== '' ? (float) $this->siling_price : null,
            'no_allocation_warrant' => $this->no_allocation_warrant ?: null,
            'agency_id'             => $this->agency_id,
            'subagency_id'          => $this->subagency_id,
            'is_required_kbp'       => $this->is_required_kbp,
            'mof_required'          => $this->mof_required,
            'cidb_required'         => $this->cidb_required,
            'committee_type'        => $this->committee_type ?: null,
        ];

        if ($this->mode === 'edit' && $this->activeId) {
            Acquisition::findOrFail($this->activeId)->update($data);
            session()->flash('success', 'Acquisition updated successfully.');
            // After save, go back to view mode
            $this->loadAcquisition($this->activeId);
            $this->mode = 'view';
        } else {
            $record         = Acquisition::create($data);
            $this->activeId = $record->id;
            session()->flash('success', 'Acquisition created successfully.');
            $this->loadAcquisition($record->id);
            $this->mode = 'view';
        }

        $this->resetPage();
    }

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

            if ($this->showPanel && $this->activeId === $this->deletingId) {
                $this->showPanel = false;
                $this->resetForm();
            }
        }

        $this->showDeleteConfirm = false;
        $this->deletingId        = null;
        $this->resetPage();
    }

    public function closePanel(): void
    {
        $this->showPanel = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deletingId        = null;
    }

    // ── Workflow actions (example stubs) ────────────────────────────────────
    public function sendForConfirmation(): void
    {
        // TODO: implement real workflow
        session()->flash('success', 'Acquisition sent for confirmation.');
        if ($this->activeId) {
            $this->loadAcquisition($this->activeId);
        }

        $this->mode = 'view';
    }

    public function verify(): void
    {
        // TODO: implement real workflow
        session()->flash('success', 'Acquisition verified.');
        if ($this->activeId) {
            $this->loadAcquisition($this->activeId);
        }

        $this->mode = 'view';
    }

    // ── Private helpers ──────────────────────────────────────────────────────
    private function loadAcquisition(int $id): void
    {
        $a = Acquisition::with(['agency', 'subagency', 'votType'])->findOrFail($id);

        $this->type                  = $a->type instanceof AcquisitionType ? $a->type->value : (string) ($a->type ?? '');
        $this->method                = $a->method instanceof AcquisitionMethod ? $a->method->value : (string) ($a->method ?? '');
        $this->project_number        = $a->project_number ?? '';
        $this->project_name          = $a->project_name ?? '';
        $this->status                = $a->status ?? '';
        $this->provision_type        = $a->provision_type ?? '';
        $this->submission_type       = $a->submission_type ?? '';
        $this->vot_type_id           = $a->vot_type_id;
        $this->tender_number         = $a->tender_number ?? '';
        $this->siling_price          = $a->siling_price !== null ? (string) $a->siling_price : '';
        $this->no_allocation_warrant = $a->no_allocation_warrant ?? '';
        $this->agency_id             = $a->agency_id;
        $this->subagency_id          = $a->subagency_id;
        $this->is_required_kbp       = (bool) $a->is_required_kbp;
        $this->mof_required          = (bool) $a->mof_required;
        $this->cidb_required         = (bool) $a->cidb_required;
        $this->committee_type        = $a->committee_type ?? '';
    }

    private function resetForm(): void
    {
        $this->type                  = '';
        $this->method                = '';
        $this->project_number        = '';
        $this->project_name          = '';
        $this->status                = '';
        $this->provision_type        = '';
        $this->submission_type       = '';
        $this->vot_type_id           = null;
        $this->tender_number         = '';
        $this->siling_price          = '';
        $this->no_allocation_warrant = '';
        $this->agency_id             = null;
        $this->subagency_id          = null;
        $this->is_required_kbp       = false;
        $this->mof_required          = false;
        $this->cidb_required         = false;
        $this->committee_type        = '';
        $this->activeId              = null;
    }
};
?>

{{-- ── Root wrapper ────────────────────────────────────────────────────────── --}}
<div class="space-y-6">

    {{-- ── Flash message ──────────────────────────────────────────────────── --}}
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

    {{-- ── Stat summary cards ──────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <x-ui.card>
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
        </x-ui.card>

        <x-ui.card>
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
        </x-ui.card>

        <x-ui.card>
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
        </x-ui.card>
    </div>

    {{-- ── Main table card ─────────────────────────────────────────────────── --}}
    <x-ui.card>
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
                            <option value="{{ $t->value }}">{{ $t->label() }}</option>
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
                <x-ui.button variant="primary" size="sm" wire:click="openCreate" class="ml-auto shrink-0">
                    <x-heroicon-o-plus class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    New Acquisition
                </x-ui.button>
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
                                    <div class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5">Tender: {{ $acquisition->tender_number }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-col gap-1.5">
                                    @if($acquisition->type)
                                        <x-ui.badge variant="primary">
                                            {{ $acquisition->type instanceof \App\Enums\AcquisitionType ? $acquisition->type->label() : $acquisition->type }}
                                        </x-ui.badge>
                                    @endif
                                    @if($acquisition->method)
                                        <x-ui.badge variant="secondary">
                                            {{ $acquisition->method instanceof \App\Enums\AcquisitionMethod ? $acquisition->method->value : $acquisition->method }}
                                        </x-ui.badge>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap font-mono text-sm text-zinc-700 dark:text-zinc-300">
                                @if($acquisition->siling_price !== null)
                                    RM {{ number_format((float) $acquisition->siling_price, 2) }}
                                @else
                                    <span class="text-zinc-400 dark:text-zinc-600">—</span>
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
                                    <span class="text-zinc-400 dark:text-zinc-600 text-sm">—</span>
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
                                <x-ui.badge :variant="$statusVariant" pill>{{ $statusLabel }}</x-ui.badge>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1">
                                    {{-- View --}}
                                    <button
                                        wire:click="openView({{ $acquisition->id }})"
                                        title="View"
                                        class="p-1.5 rounded-lg cursor-pointer text-zinc-400 hover:text-sky-600 hover:bg-sky-50 dark:hover:bg-sky-950/30 dark:hover:text-sky-400 transition-all"
                                    >
                                        <x-heroicon-o-eye class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                    </button>
                                    {{-- Edit --}}
                                    <button
                                        wire:click="openEdit({{ $acquisition->id }})"
                                        title="Edit"
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
                                    <x-ui.button variant="primary" size="sm" wire:click="openCreate">
                                        <x-heroicon-o-plus class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                        New Acquisition
                                    </x-ui.button>
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
    </x-ui.card>

    {{-- ══════════════════════════════════════════════════════════════════════
         Slide-over Panel (create / view / edit)
    ══════════════════════════════════════════════════════════════════════ --}}
    <div
        x-data
        x-show="$wire.showPanel"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-hidden"
        style="display: none;"
        role="dialog"
        aria-modal="true"
    >
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-zinc-950/50 backdrop-blur-sm" wire:click="closePanel"></div>

        {{-- Panel --}}
        <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
            <div
                x-show="$wire.showPanel"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="pointer-events-auto w-screen max-w-2xl"
            >
                <div class="flex h-full flex-col bg-white dark:bg-zinc-900 shadow-2xl border-l border-zinc-200/80 dark:border-zinc-800/80">

                    {{-- ── Panel Header ──────────────────────────────────────── --}}
                    <div class="flex items-center justify-between px-6 py-5 border-b border-zinc-100 dark:border-zinc-800/50">
                        <div class="flex items-center gap-3 min-w-0">
                            {{-- Mode icon --}}
                            @if($mode === 'view')
                                <span class="shrink-0 w-9 h-9 rounded-xl bg-sky-50 dark:bg-sky-950/40 flex items-center justify-center text-sky-600 dark:text-sky-400">
                                    <x-heroicon-o-eye class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                </span>
                            @elseif($mode === 'edit')
                                <span class="shrink-0 w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                    <x-heroicon-o-pencil class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                </span>
                            @else
                                <span class="shrink-0 w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                    <x-heroicon-o-plus class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                </span>
                            @endif
                            <div class="min-w-0">
                                <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 truncate">
                                    @if($mode === 'view')
                                        {{ $project_name ?: 'Acquisition Details' }}
                                    @elseif($mode === 'edit')
                                        Edit — {{ $project_name ?: 'Acquisition' }}
                                    @else
                                        New Acquisition
                                    @endif
                                </h2>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                    @if($mode === 'view')
                                        Read-only view. Click <span class="font-medium text-amber-600 dark:text-amber-400">Edit</span> to make changes.
                                    @elseif($mode === 'edit')
                                        Editing acquisition record.
                                    @else
                                        Fill in the details to create a new acquisition.
                                    @endif
                                </p>
                            </div>
                        </div>
                        <button
                            wire:click="closePanel"
                            class="shrink-0 p-2 rounded-xl cursor-pointer text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all"
                        >
                            <x-heroicon-o-x-mark class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        </button>
                    </div>

                    {{-- ── Scrollable Body ────────────────────────────────────── --}}
                    <div class="flex-1 overflow-y-auto px-6 py-6">

                        @if($mode === 'view')
                            {{-- ════ VIEW MODE — read-only detail layout ════ --}}
                            <div class="space-y-7">

                                {{-- Section: Project Information --}}
                                <div>
                                    <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                                        <span class="w-5 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                        Project Information
                                        <span class="flex-1 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                    </h3>
                                    <dl class="grid grid-cols-2 gap-x-6 gap-y-4">
                                        <div>
                                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Project Number</dt>
                                            <dd class="mt-1 text-sm font-mono text-zinc-900 dark:text-zinc-100">{{ $project_number ?: '—' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Tender Number</dt>
                                            <dd class="mt-1 text-sm font-mono text-zinc-900 dark:text-zinc-100">{{ $tender_number ?: '—' }}</dd>
                                        </div>
                                        <div class="col-span-2">
                                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Project Name</dt>
                                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100 font-medium">{{ $project_name ?: '—' }}</dd>
                                        </div>
                                    </dl>
                                </div>

                                {{-- Section: Classification --}}
                                <div>
                                    <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                                        <span class="w-5 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                        Classification
                                        <span class="flex-1 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                    </h3>
                                    <dl class="grid grid-cols-2 gap-x-6 gap-y-4">
                                        <div>
                                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Acquisition Type</dt>
                                            <dd class="mt-1">
                                                @if($type)
                                                    @php $typeEnum = \App\Enums\AcquisitionType::tryFrom($type); @endphp
                                                    <x-ui.badge variant="primary">{{ $typeEnum ? $typeEnum->label() : $type }}</x-ui.badge>
                                                @else
                                                    <span class="text-sm text-zinc-400 dark:text-zinc-600">—</span>
                                                @endif
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Acquisition Method</dt>
                                            <dd class="mt-1">
                                                @if($method)
                                                    <x-ui.badge variant="secondary">{{ $method }}</x-ui.badge>
                                                @else
                                                    <span class="text-sm text-zinc-400 dark:text-zinc-600">—</span>
                                                @endif
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Status</dt>
                                            <dd class="mt-1">
                                                @if($status)
                                                    @php
                                                        $sv = match($status) {
                                                            'DILULUSKAN'  => 'success',
                                                            'DIKEMUKAKAN' => 'info',
                                                            'DRAF'        => 'warning',
                                                            'DITOLAK'     => 'danger',
                                                            default       => 'secondary',
                                                        };
                                                        $sl = match($status) {
                                                            'DILULUSKAN'  => 'Diluluskan',
                                                            'DIKEMUKAKAN' => 'Dikemukakan',
                                                            'DRAF'        => 'Draf',
                                                            'DITOLAK'     => 'Ditolak',
                                                            'DIBATALKAN'  => 'Dibatalkan',
                                                            default       => $status,
                                                        };
                                                    @endphp
                                                    <x-ui.badge :variant="$sv" pill>{{ $sl }}</x-ui.badge>
                                                @else
                                                    <span class="text-sm text-zinc-400 dark:text-zinc-600">—</span>
                                                @endif
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">VOT Type</dt>
                                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                                @if($vot_type_id)
                                                    @php $vot = $this->votTypes->firstWhere('id', $vot_type_id); @endphp
                                                    {{ $vot ? "{$vot->code} — {$vot->name}" : '—' }}
                                                @else
                                                    <span class="text-zinc-400 dark:text-zinc-600">—</span>
                                                @endif
                                            </dd>
                                        </div>
                                    </dl>
                                </div>

                                {{-- Section: Financial --}}
                                <div>
                                    <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                                        <span class="w-5 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                        Financial
                                        <span class="flex-1 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                    </h3>
                                    <dl class="grid grid-cols-2 gap-x-6 gap-y-4">
                                        <div>
                                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Ceiling Price</dt>
                                            <dd class="mt-1 text-sm font-mono font-semibold text-zinc-900 dark:text-zinc-100">
                                                {{ $siling_price !== '' ? 'RM '.number_format((float) $siling_price, 2) : '—' }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Allocation Warrant No.</dt>
                                            <dd class="mt-1 text-sm font-mono text-zinc-900 dark:text-zinc-100">{{ $no_allocation_warrant ?: '—' }}</dd>
                                        </div>
                                    </dl>
                                </div>

                                {{-- Section: Agency --}}
                                <div>
                                    <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                                        <span class="w-5 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                        Agency
                                        <span class="flex-1 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                    </h3>
                                    <dl class="grid grid-cols-2 gap-x-6 gap-y-4">
                                        <div>
                                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Agency</dt>
                                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                                @if($agency_id)
                                                    @php $ag = $this->agencies->firstWhere('id', $agency_id); @endphp
                                                    {{ $ag?->name ?? '—' }}
                                                @else
                                                    <span class="text-zinc-400 dark:text-zinc-600">—</span>
                                                @endif
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Sub-Agency</dt>
                                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                                @if($subagency_id)
                                                    @php $sub = $this->subagencies->firstWhere('id', $subagency_id); @endphp
                                                    {{ $sub?->name ?? '—' }}
                                                @else
                                                    <span class="text-zinc-400 dark:text-zinc-600">—</span>
                                                @endif
                                            </dd>
                                        </div>
                                    </dl>
                                </div>

                                {{-- Section: Additional --}}
                                <div>
                                    <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                                        <span class="w-5 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                        Additional Details
                                        <span class="flex-1 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                    </h3>
                                    <dl class="grid grid-cols-2 gap-x-6 gap-y-4">
                                        <div>
                                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Provision Type</dt>
                                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $provision_type ?: '—' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Submission Type</dt>
                                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $submission_type ?: '—' }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Committee Type</dt>
                                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $committee_type ?: '—' }}</dd>
                                        </div>
                                    </dl>

                                    {{-- Boolean flags --}}
                                    <div class="mt-4 grid grid-cols-3 gap-3">
                                        @foreach([
                                            [$is_required_kbp, 'KBP Required', 'Kontraktor Bumiputera'],
                                            [$mof_required,    'MOF Required', 'Ministry of Finance'],
                                            [$cidb_required,   'CIDB Required', 'Const. Industry Dev. Board'],
                                        ] as [$val, $label, $desc])
                                            <div class="p-3 rounded-xl border {{ $val ? 'border-emerald-200 dark:border-emerald-800/50 bg-emerald-50/60 dark:bg-emerald-950/20' : 'border-zinc-200 dark:border-zinc-700/80 bg-zinc-50/60 dark:bg-zinc-800/20' }}">
                                                <div class="flex items-center gap-2 mb-0.5">
                                                    @if($val)
                                                        <x-heroicon-o-check class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" />
                                                    @else
                                                        <x-heroicon-o-x-mark class="w-3.5 h-3.5 text-zinc-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                                    @endif
                                                    <span class="text-xs font-semibold {{ $val ? 'text-emerald-700 dark:text-emerald-400' : 'text-zinc-500 dark:text-zinc-400' }}">{{ $label }}</span>
                                                </div>
                                                <p class="text-xs text-zinc-500 dark:text-zinc-500 pl-5">{{ $desc }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                            {{-- END VIEW MODE --}}

                        @else
                            {{-- ════ CREATE / EDIT MODE — form ════ --}}
                            <form wire:submit="save" id="acquisition-form" class="space-y-7">

                                {{-- Section: Project Info --}}
                                <div>
                                    <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                                        <span class="w-5 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                                        Project Information
                                        <span class="flex-1 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                                    </h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <x-ui.input id="project_number" label="Project Number" placeholder="e.g. PRJ-2026-001" wire:model="project_number" :required="true" :error="$errors->first('project_number')" />
                                        <x-ui.input id="tender_number" label="Tender Number" placeholder="e.g. TND-2026-001" wire:model="tender_number" :error="$errors->first('tender_number')" />
                                        <div class="sm:col-span-2">
                                            <x-ui.input id="project_name" label="Project Name" placeholder="Full name of the acquisition project" wire:model="project_name" :required="true" :error="$errors->first('project_name')" />
                                        </div>
                                    </div>
                                </div>

                                {{-- Section: Classification --}}
                                <div>
                                    <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                                        <span class="w-5 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                                        Classification
                                        <span class="flex-1 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                                    </h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="space-y-1.5">
                                            <x-ui.label for="type" :required="true">Acquisition Type</x-ui.label>
                                            <select id="type" wire:model="type" class="block w-full rounded-xl border {{ $errors->has('type') ? 'border-rose-500' : 'border-zinc-200 dark:border-zinc-700/80' }} bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 py-2.5 px-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-emerald-500">
                                                <option value="">Select type...</option>
                                                @foreach($this->acquisitionTypes as $t)
                                                    <option value="{{ $t->value }}">{{ $t->label() }}</option>
                                                @endforeach
                                            </select>
                                            @error('type') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                                        </div>

                                        <div class="space-y-1.5">
                                            <x-ui.label for="method" :required="true">Acquisition Method</x-ui.label>
                                            <select id="method" wire:model="method" class="block w-full rounded-xl border {{ $errors->has('method') ? 'border-rose-500' : 'border-zinc-200 dark:border-zinc-700/80' }} bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 py-2.5 px-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-emerald-500">
                                                <option value="">Select method...</option>
                                                @foreach($this->acquisitionMethods as $m)
                                                    <option value="{{ $m->value }}">{{ $m->value }}</option>
                                                @endforeach
                                            </select>
                                            @error('method') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                                        </div>

                                        <div class="space-y-1.5">
                                            <x-ui.label for="status" :required="true">Status</x-ui.label>
                                            <select id="status" wire:model="status" class="block w-full rounded-xl border {{ $errors->has('status') ? 'border-rose-500' : 'border-zinc-200 dark:border-zinc-700/80' }} bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 py-2.5 px-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-emerald-500">
                                                <option value="">Select status...</option>
                                                <option value="DRAF">Draf</option>
                                                <option value="DIKEMUKAKAN">Dikemukakan</option>
                                                <option value="DILULUSKAN">Diluluskan</option>
                                                <option value="DITOLAK">Ditolak</option>
                                                <option value="DIBATALKAN">Dibatalkan</option>
                                            </select>
                                            @error('status') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                                        </div>

                                        <div class="space-y-1.5">
                                            <x-ui.label for="vot_type_id">VOT Type</x-ui.label>
                                            <select id="vot_type_id" wire:model="vot_type_id" class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 py-2.5 px-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-emerald-500">
                                                <option value="">None</option>
                                                @foreach($this->votTypes as $vot)
                                                    <option value="{{ $vot->id }}">{{ $vot->code }} — {{ $vot->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- Section: Financial --}}
                                <div>
                                    <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                                        <span class="w-5 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                                        Financial
                                        <span class="flex-1 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                                    </h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="space-y-1.5">
                                            <x-ui.label for="siling_price">Ceiling Price (RM)</x-ui.label>
                                            <div class="relative rounded-xl shadow-xs">
                                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 text-sm font-medium">RM</div>
                                                <input id="siling_price" type="number" step="0.01" min="0" wire:model="siling_price" placeholder="0.00" class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 py-2.5 pl-10 pr-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-emerald-500">
                                            </div>
                                            @error('siling_price') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                                        </div>
                                        <x-ui.input id="no_allocation_warrant" label="Allocation Warrant No." placeholder="e.g. WP-2026-0012" wire:model="no_allocation_warrant" :error="$errors->first('no_allocation_warrant')" />
                                    </div>
                                </div>

                                {{-- Section: Agency --}}
                                <div>
                                    <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                                        <span class="w-5 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                                        Agency
                                        <span class="flex-1 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                                    </h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="space-y-1.5">
                                            <x-ui.label for="agency_id">Agency</x-ui.label>
                                            <select id="agency_id" wire:model.live="agency_id" class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 py-2.5 px-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-emerald-500">
                                                <option value="">No agency</option>
                                                @foreach($this->agencies as $agency)
                                                    <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="space-y-1.5">
                                            <x-ui.label for="subagency_id">Sub-Agency</x-ui.label>
                                            <select id="subagency_id" wire:model="subagency_id" @if(!$agency_id) disabled @endif class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 py-2.5 px-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                                <option value="">No sub-agency</option>
                                                @foreach($this->subagencies as $sub)
                                                    <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- Section: Additional --}}
                                <div>
                                    <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                                        <span class="w-5 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                                        Additional Details
                                        <span class="flex-1 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                                    </h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <x-ui.input id="provision_type" label="Provision Type" placeholder="e.g. Pusat" wire:model="provision_type" :error="$errors->first('provision_type')" />
                                        <x-ui.input id="submission_type" label="Submission Type" placeholder="e.g. Baharu" wire:model="submission_type" :error="$errors->first('submission_type')" />
                                        <x-ui.input id="committee_type" label="Committee Type" placeholder="e.g. JK Teknikal" wire:model="committee_type" :error="$errors->first('committee_type')" />
                                    </div>

                                    {{-- Toggle switches --}}
                                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        @foreach([
                                            ['is_required_kbp', 'KBP Required', 'Kontraktor Bumiputera'],
                                            ['mof_required',    'MOF Required', 'Ministry of Finance'],
                                            ['cidb_required',   'CIDB Required', 'Const. Industry Dev. Board'],
                                        ] as [$field, $label, $desc])
                                            <label class="flex items-start gap-3 p-3 rounded-xl border border-zinc-200 dark:border-zinc-700/80 cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors">
                                                <div class="relative mt-0.5 shrink-0">
                                                    <input type="checkbox" wire:model="{{ $field }}" class="peer sr-only" id="{{ $field }}">
                                                    <div class="w-9 h-5 rounded-full bg-zinc-200 dark:bg-zinc-700 peer-checked:bg-emerald-500 transition-colors"></div>
                                                    <div class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white shadow-sm transition-transform peer-checked:translate-x-4"></div>
                                                </div>
                                                <div>
                                                    <div class="text-xs font-semibold text-zinc-700 dark:text-zinc-200">{{ $label }}</div>
                                                    <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $desc }}</div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                            </form>
                            {{-- END FORM MODE --}}
                        @endif

                    </div>{{-- end scrollable body --}}

                    {{-- ── Panel Footer ───────────────────────────────────────── --}}
                    <div class="px-6 py-4 border-t border-zinc-100 dark:border-zinc-800/50 bg-zinc-50/60 dark:bg-zinc-900/60">

                        @if($mode === 'view')
                            {{-- VIEW footer: [Send for Confirmation] [Verify]  ···  [Edit] [Close] --}}
                            <div class="flex items-center justify-between gap-3">

                                {{-- Left — workflow state actions --}}
                                <div class="flex items-center gap-2">
                                    {{-- Send for Confirmation --}}
                                    <x-ui.button variant="secondary" size="sm" wire:click="sendForConfirmation" wire:loading.attr="disabled" wire:target="sendForConfirmation">
                                        <x-heroicon-o-paper-airplane class="w-3.5 h-3.5 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                        <span wire:loading.remove wire:target="sendForConfirmation">Send for Confirmation</span>
                                        <span wire:loading wire:target="sendForConfirmation">Sending...</span>
                                    </x-ui.button>

                                    {{-- Verify --}}
                                    <x-ui.button variant="secondary" size="sm" wire:click="verify" wire:loading.attr="disabled" wire:target="verify">
                                        <x-heroicon-o-shield-check class="w-3.5 h-3.5 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                        <span wire:loading.remove wire:target="verify">Verify</span>
                                        <span wire:loading wire:target="verify">Verifying...</span>
                                    </x-ui.button>
                                </div>

                                {{-- Right — Edit + Close --}}
                                <div class="flex items-center gap-2">
                                    <x-ui.button variant="outline" size="sm" wire:click="switchToEdit">
                                        <x-heroicon-o-pencil class="w-3.5 h-3.5 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                        Edit
                                    </x-ui.button>
                                    <x-ui.button variant="secondary" size="sm" wire:click="closePanel">Close</x-ui.button>
                                </div>
                            </div>

                        @elseif($mode === 'edit')
                            {{-- EDIT footer: right side only — [Back to View] [Save Changes] --}}
                            <div class="flex items-center justify-end gap-2">
                                <x-ui.button variant="outline" size="sm" wire:click="switchToView">
                                    <x-heroicon-o-chevron-left class="w-4 h-4 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                    Back to View
                                </x-ui.button>

                                <x-ui.button variant="primary" size="sm" wire:click="save" wire:loading.attr="disabled" wire:target="save">
                                    <span wire:loading.remove wire:target="save">
                                        <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                        Save Changes
                                    </span>
                                    <span wire:loading wire:target="save" class="flex items-center gap-1.5">
                                        <x-icon-spinner class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" />
                                        Saving...
                                    </span>
                                </x-ui.button>
                            </div>

                        @else
                            {{-- CREATE footer: Cancel (left) | Create (right) --}}
                            <div class="flex items-center justify-end gap-3">
                                <x-ui.button variant="outline" size="sm" wire:click="closePanel">Cancel</x-ui.button>
                                <x-ui.button variant="primary" size="sm" wire:click="save" wire:loading.attr="disabled" wire:target="save">
                                    <span wire:loading.remove wire:target="save">
                                        <x-heroicon-o-plus class="w-4 h-4 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                        Create Acquisition
                                    </span>
                                    <span wire:loading wire:target="save" class="flex items-center gap-1.5">
                                        <x-icon-spinner class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" />
                                        Creating...
                                    </span>
                                </x-ui.button>
                            </div>
                        @endif

                    </div>{{-- end footer --}}
                </div>
            </div>
        </div>
    </div>

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
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">This action cannot be undone. The acquisition record will be permanently removed.</p>
                </div>
            </div>
            <div class="mt-5 flex gap-3 justify-end">
                <x-ui.button variant="outline" size="sm" wire:click="cancelDelete">Cancel</x-ui.button>
                <x-ui.button variant="danger" size="sm" wire:click="delete" wire:loading.attr="disabled" wire:target="delete">
                    <span wire:loading.remove wire:target="delete">Delete</span>
                    <span wire:loading wire:target="delete">Deleting...</span>
                </x-ui.button>
            </div>
        </div>
    </div>

</div>