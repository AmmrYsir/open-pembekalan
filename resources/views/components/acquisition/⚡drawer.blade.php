<?php

use App\Models\Acquisition;
use App\Models\Agency;
use App\Models\Subagency;
use App\Models\VotType;
use App\Models\AgencyOfficer;
use App\Models\User;
use App\Models\Sequence;
use App\Enums\AcquisitionType;
use App\Enums\AcquisitionMethod;
use App\Enums\AcquisitionCommitteeType;
use App\Livewire\Forms\AcquisitionForm;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

new class extends Component
{
    public AcquisitionForm $form;

    // panel state
    public string $mode = 'create'; // 'create' | 'view' | 'edit'
    public bool $showPanel = false;
    public ?int $activeId = null;

    #[On('open-acquisition-drawer')]
    public function open(string $mode = 'create', ?int $id = null): void
    {
        $this->form->resetForm();
        $this->activeId = $id;
        $this->mode = $mode;

        if ($id && in_array($mode, ['view', 'edit'])) {
            $acquisition = Acquisition::findOrFail($id);
            $this->form->fillFromModel($acquisition);
        }

        $this->showPanel = true;
    }

    public function updatedFormAgencyId(): void
    {
        $this->form->subagency_id = null;
        $this->form->user_id = null;
    }

    public function updatedFormSubagencyId(): void
    {
        $this->form->user_id = null;
    }

    #[Computed]
    public function currentAcquisition(): ?Acquisition
    {
        return $this->activeId ? Acquisition::find($this->activeId) : null;
    }

    #[Computed]
    public function agencies(): \Illuminate\Database\Eloquent\Collection
    {
        return Agency::orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function subagencies(): \Illuminate\Database\Eloquent\Collection
    {
        return Subagency::when($this->form->agency_id, fn ($q) => $q->where('agency_id', $this->form->agency_id))
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    #[Computed]
    public function officers(): \Illuminate\Database\Eloquent\Collection
    {
        if (!$this->form->agency_id) {
            return new \Illuminate\Database\Eloquent\Collection();
        }

        return User::whereIn('id',
            AgencyOfficer::where('agency_id', $this->form->agency_id)
                ->when($this->form->subagency_id, fn ($q) => $q->where('subagency_id', $this->form->subagency_id))
                ->pluck('user_id')
        )->orderBy('name')->get(['id', 'name']);
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
    public function committeeTypes(): array
    {
        return AcquisitionCommitteeType::cases();
    }

    public function save(): void
    {
        if ($this->mode === 'edit' && $this->activeId) {
            $acquisition = Acquisition::findOrFail($this->activeId);
            $this->form->update($acquisition);
            session()->flash('success', 'Acquisition updated successfully.');
            
            // Reload into view mode
            $this->form->fillFromModel($acquisition->fresh());
            $this->mode = 'view';
        } else {
            $record = $this->form->store();
            $this->activeId = $record->id;
            Sequence::where('slug', 'project-number')->increment('value');
            session()->flash('success', 'Acquisition created successfully.');
            
            $this->form->fillFromModel($record);
            $this->mode = 'view';
        }

        $this->dispatch('acquisition-saved');
    }

    public function transitionTo(string $targetStateClass): void
    {
        if (!$this->activeId) {
            return;
        }

        $acquisition = Acquisition::findOrFail($this->activeId);
        $acquisition->status->transitionTo($targetStateClass);

        session()->flash('success', 'Status updated to ' . $acquisition->status->label());

        $this->form->fillFromModel($acquisition->fresh());
        $this->dispatch('acquisition-saved');
    }

    public function switchToEdit(): void
    {
        $this->mode = 'edit';
        $this->resetValidation();
    }

    public function switchToView(): void
    {
        if ($this->activeId) {
            $acquisition = Acquisition::findOrFail($this->activeId);
            $this->form->fillFromModel($acquisition);
        }
        $this->mode = 'view';
        $this->resetValidation();
    }

    public function closePanel(): void
    {
        $this->showPanel = false;
        $this->form->resetForm();
        $this->resetValidation();
    }
};
?>

{{-- ── Slide-over Panel (create / view / edit) ── --}}
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
                                    {{ $form->project_name ?: 'Acquisition Details' }}
                                @elseif($mode === 'edit')
                                    Edit — {{ $form->project_name ?: 'Acquisition' }}
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
                    <div class="flex items-center gap-1.5 shrink-0">
                        @if($activeId)
                            <a
                                href="{{ route('acquisition.show', $activeId) }}"
                                title="Open Full Page with Tabs"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/40 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition-colors"
                            >
                                <x-heroicon-o-arrow-top-right-on-square class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                Full Page
                            </a>
                        @endif
                        <button
                            wire:click="closePanel"
                            class="p-2 rounded-xl cursor-pointer text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all"
                        >
                            <x-heroicon-o-x-mark class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        </button>
                    </div>
                </div>

                {{-- ── Scrollable Body ────────────────────────────────────── --}}
                <div class="flex-1 overflow-y-auto px-6 py-6">

                    @if($mode === 'view')
                        {{-- ════ VIEW MODE — read-only detail layout ════ --}}
                        <div class="space-y-7">

                            {{-- Status Banner --}}
                            @if($this->currentAcquisition && $this->currentAcquisition->status)
                                <div class="flex items-center justify-between p-3.5 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/80 dark:border-zinc-800">
                                    <div class="flex items-center gap-2.5">
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Pipeline Status:</span>
                                        <x-badge variant="{{ $this->currentAcquisition->status->color() }}">
                                            {{ $this->currentAcquisition->status->label() }}
                                        </x-badge>
                                    </div>
                                    <span class="text-xs font-mono text-zinc-500 dark:text-zinc-400">
                                        {{ $this->currentAcquisition->status->getValue() }}
                                    </span>
                                </div>
                            @endif

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
                                        <dd class="mt-1 text-sm font-mono text-zinc-900 dark:text-zinc-100">{{ $form->project_number ?: '—' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Tender Number</dt>
                                        <dd class="mt-1 text-sm font-mono text-zinc-900 dark:text-zinc-100">{{ $form->tender_number ?: '—' }}</dd>
                                    </div>
                                    <div class="col-span-2">
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Project Name</dt>
                                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100 font-medium">{{ $form->project_name ?: '—' }}</dd>
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
                                            @if($form->type)
                                                @php $typeEnum = \App\Enums\AcquisitionType::tryFrom($form->type); @endphp
                                                <x-badge variant="primary">{{ $typeEnum ? $typeEnum->value : $form->type }}</x-badge>
                                            @else
                                                <span class="text-sm text-zinc-400 dark:text-zinc-650">—</span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Acquisition Method</dt>
                                        <dd class="mt-1">
                                            @if($form->method)
                                                <x-badge variant="secondary">{{ $form->method }}</x-badge>
                                            @else
                                                <span class="text-sm text-zinc-400 dark:text-zinc-650">—</span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">VOT Type</dt>
                                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100 font-mono">
                                            @if($form->vot_type_id)
                                                @php $vot = $this->votTypes->firstWhere('id', $form->vot_type_id); @endphp
                                                {{ $vot ? "{$vot->code} — {$vot->name}" : '—' }}
                                            @else
                                                <span class="text-zinc-400 dark:text-zinc-650">—</span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Committee Type</dt>
                                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100 font-sans">
                                            @if($form->committee_type)
                                                @php $commEnum = \App\Enums\AcquisitionCommitteeType::tryFrom($form->committee_type); @endphp
                                                {{ $commEnum ? $commEnum->label() : $form->committee_type }}
                                            @else
                                                <span class="text-zinc-400 dark:text-zinc-650">—</span>
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
                                            {{ $form->siling_price !== '' ? 'RM '.number_format((float) $form->siling_price, 2) : '—' }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Allocation Warrant No.</dt>
                                        <dd class="mt-1 text-sm font-mono text-zinc-900 dark:text-zinc-100">{{ $form->no_allocation_warrant ?: '—' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            {{-- Section: Agency & Officer --}}
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                                    <span class="w-5 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                    Agency & Officer
                                    <span class="flex-1 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                </h3>
                                <dl class="grid grid-cols-2 gap-x-6 gap-y-4">
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Agency</dt>
                                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100 font-medium">
                                            @if($form->agency_id)
                                                @php $ag = $this->agencies->firstWhere('id', $form->agency_id); @endphp
                                                {{ $ag?->name ?? '—' }}
                                            @else
                                                <span class="text-zinc-400 dark:text-zinc-600">—</span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Sub-Agency</dt>
                                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                            @if($form->subagency_id)
                                                @php $sub = $this->subagencies->firstWhere('id', $form->subagency_id); @endphp
                                                {{ $sub?->name ?? '—' }}
                                            @else
                                                <span class="text-zinc-400 dark:text-zinc-600">—</span>
                                            @endif
                                        </dd>
                                    </div>
                                    <div class="col-span-2">
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Officer</dt>
                                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                            @if($form->user_id)
                                                @php $usr = \App\Models\User::find($form->user_id); @endphp
                                                {{ $usr?->name ?? '—' }}
                                            @else
                                                <span class="text-zinc-400 dark:text-zinc-650">—</span>
                                            @endif
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            {{-- Section: Requirements --}}
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                                    <span class="w-5 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                    Requirements
                                    <span class="flex-1 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                </h3>

                                {{-- Boolean flags --}}
                                <div class="mt-4 grid grid-cols-3 gap-3">
                                    @foreach([
                                        [$form->is_required_kbp, 'KBP Required', 'Kontraktor Bumiputera'],
                                        [$form->mof_required,    'MOF Required', 'Ministry of Finance'],
                                        [$form->cidb_required,   'CIDB Required', 'Const. Industry Dev. Board'],
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
                                            <p class="text-xs text-zinc-550 dark:text-zinc-500 pl-5">{{ $desc }}</p>
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
                                    <x-input id="project_number" disabled label="Project Number" placeholder="e.g. PRJ-2026-001" wire:model="form.project_number" :required="true" :error="$errors->first('form.project_number')" />
                                    <x-input id="tender_number" label="Tender Number" placeholder="e.g. TND-2026-001" wire:model="form.tender_number" :error="$errors->first('form.tender_number')" />
                                    <div class="sm:col-span-2">
                                        <x-input id="project_name" label="Project Name" placeholder="Full name of the acquisition project" wire:model="form.project_name" :required="true" :error="$errors->first('form.project_name')" />
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
                                        <x-label for="type" :required="true">Acquisition Type</x-label>
                                        <select id="type" wire:model="form.type" class="block w-full rounded-xl border {{ $errors->has('form.type') ? 'border-rose-500' : 'border-zinc-200 dark:border-zinc-700/80' }} bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 py-2.5 px-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-emerald-500">
                                            <option value="">Select type...</option>
                                            @foreach($this->acquisitionTypes as $t)
                                                <option value="{{ $t->value }}">{{ $t->value }}</option>
                                            @endforeach
                                        </select>
                                        @error('form.type') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="space-y-1.5">
                                        <x-label for="method" :required="true">Acquisition Method</x-label>
                                        <select id="method" wire:model="form.method" class="block w-full rounded-xl border {{ $errors->has('form.method') ? 'border-rose-500' : 'border-zinc-200 dark:border-zinc-700/80' }} bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 py-2.5 px-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-emerald-500">
                                            <option value="">Select method...</option>
                                            @foreach($this->acquisitionMethods as $m)
                                                <option value="{{ $m->value }}">{{ $m->value }}</option>
                                            @endforeach
                                        </select>
                                        @error('form.method') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="space-y-1.5">
                                        <x-label for="vot_type_id">VOT Type</x-label>
                                        <select id="vot_type_id" wire:model="form.vot_type_id" class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 py-2.5 px-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-emerald-500">
                                            <option value="">None</option>
                                            @foreach($this->votTypes as $vot)
                                                <option value="{{ $vot->id }}">{{ $vot->code }} — {{ $vot->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="space-y-1.5">
                                        <x-label for="committee_type">Committee Type</x-label>
                                        <select id="committee_type" wire:model="form.committee_type" class="block w-full rounded-xl border {{ $errors->has('form.committee_type') ? 'border-rose-500' : 'border-zinc-200 dark:border-zinc-700/80' }} bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 py-2.5 px-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-emerald-500">
                                            <option value="">Select committee type...</option>
                                            @foreach($this->committeeTypes as $comm)
                                                <option value="{{ $comm->value }}">{{ $comm->value }}</option>
                                            @endforeach
                                        </select>
                                        @error('form.committee_type') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
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
                                        <x-label for="siling_price">Ceiling Price (RM)</x-label>
                                        <div class="relative rounded-xl shadow-xs">
                                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 text-sm font-medium">RM</div>
                                            <input id="siling_price" type="number" step="0.01" min="0" wire:model="form.siling_price" placeholder="0.00" class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 py-2.5 pl-10 pr-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-emerald-500">
                                        </div>
                                        @error('form.siling_price') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <x-input id="no_allocation_warrant" label="Allocation Warrant No." placeholder="e.g. WP-2026-0012" wire:model="form.no_allocation_warrant" :error="$errors->first('form.no_allocation_warrant')" />
                                </div>
                            </div>

                            {{-- Section: Agency --}}
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                                    <span class="w-5 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                                    Agency & Officer
                                    <span class="flex-1 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="space-y-1.5">
                                        <x-label for="agency_id">Agency</x-label>
                                        <select id="agency_id" wire:model.live="form.agency_id" class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 py-2.5 px-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-emerald-500">
                                            <option value="">No agency</option>
                                            @foreach($this->agencies as $agency)
                                                <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="space-y-1.5">
                                        <x-label for="subagency_id">Sub-Agency</x-label>
                                        <select id="subagency_id" wire:model.live="form.subagency_id" @if(!$form->agency_id) disabled @endif class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 py-2.5 px-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                            <option value="">No sub-agency</option>
                                            @foreach($this->subagencies as $sub)
                                                <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="space-y-1.5 col-span-1 sm:col-span-2">
                                        <x-label for="user_id">Officer</x-label>
                                        <select id="user_id" wire:model="form.user_id" @if(!$form->agency_id) disabled @endif class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 py-2.5 px-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:border-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                            <option value="">Select officer...</option>
                                            @foreach($this->officers as $officer)
                                                <option value="{{ $officer->id }}">{{ $officer->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('form.user_id') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Section: Requirements --}}
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                                    <span class="w-5 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                                    Requirements
                                    <span class="flex-1 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                                </h3>

                                {{-- Toggle switches --}}
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    @foreach([
                                        ['is_required_kbp', 'KBP Required', 'Kontraktor Bumiputera'],
                                        ['mof_required',    'MOF Required', 'Ministry of Finance'],
                                        ['cidb_required',   'CIDB Required', 'Const. Industry Dev. Board'],
                                    ] as [$field, $label, $desc])
                                        <label class="flex items-start gap-3 p-3 rounded-xl border border-zinc-200 dark:border-zinc-700/80 cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors">
                                            <div class="relative mt-0.5 shrink-0">
                                                <input type="checkbox" wire:model="form.{{ $field }}" class="peer sr-only" id="{{ $field }}">
                                                <div class="w-9 h-5 rounded-full bg-zinc-200 dark:bg-zinc-700 peer-checked:bg-emerald-500 transition-colors"></div>
                                                <div class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white shadow-sm transition-transform peer-checked:translate-x-4"></div>
                                            </div>
                                            <div>
                                                <div class="text-xs font-semibold text-zinc-700 dark:text-zinc-200">{{ $label }}</div>
                                                <div class="text-xs text-zinc-550 dark:text-zinc-400 mt-0.5">{{ $desc }}</div>
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
                        {{-- VIEW footer: [Real State Pipeline Transition Buttons]  ···  [Edit] [Close] --}}
                        <div class="flex items-center justify-between gap-3">

                            {{-- Left — real state workflow actions --}}
                            <div class="flex items-center gap-2">
                                @if($this->currentAcquisition && $this->currentAcquisition->status)
                                    @foreach($this->currentAcquisition->status->transitionableStateInstances() as $targetState)
                                        <x-button 
                                            variant="primary" 
                                            size="sm" 
                                            wire:click="transitionTo('{{ addslashes(get_class($targetState)) }}')" 
                                            wire:loading.attr="disabled"
                                            wire:target="transitionTo('{{ addslashes(get_class($targetState)) }}')"
                                        >
                                            <x-heroicon-o-arrow-right-circle class="w-3.5 h-3.5 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                            <span wire:loading.remove wire:target="transitionTo('{{ addslashes(get_class($targetState)) }}')">
                                                Advance to {{ $targetState->label() }}
                                            </span>
                                            <span wire:loading wire:target="transitionTo('{{ addslashes(get_class($targetState)) }}')">
                                                Updating...
                                            </span>
                                        </x-button>
                                    @endforeach
                                @endif
                            </div>

                            {{-- Right — Edit + Close --}}
                            <div class="flex items-center gap-2">
                                <x-button variant="outline" size="sm" wire:click="switchToEdit">
                                    <x-heroicon-o-pencil class="w-3.5 h-3.5 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                    Edit
                                </x-button>
                                <x-button variant="secondary" size="sm" wire:click="closePanel">Close</x-button>
                            </div>
                        </div>

                    @elseif($mode === 'edit')
                        {{-- EDIT footer: right side only — [Back to View] [Save Changes] --}}
                        <div class="flex items-center justify-end gap-2">
                            <x-button variant="outline" size="sm" wire:click="switchToView">
                                <x-heroicon-o-chevron-left class="w-4 h-4 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                Back to View
                            </x-button>

                            <x-button variant="primary" size="sm" wire:click="save" wire:loading.attr="disabled" wire:target="save">
                                <span wire:loading.remove wire:target="save">
                                    <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                    Save Changes
                                </span>
                                <span wire:loading wire:target="save" class="flex items-center gap-1.5">
                                    <x-icon-spinner class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" />
                                    Saving...
                                </span>
                            </x-button>
                        </div>

                    @else
                        {{-- CREATE footer: Cancel (left) | Create (right) --}}
                        <div class="flex items-center justify-end gap-3">
                            <x-button variant="outline" size="sm" wire:click="closePanel">Cancel</x-button>
                            <x-button variant="primary" size="sm" wire:click="save" wire:loading.attr="disabled" wire:target="save">
                                <span wire:loading.remove wire:target="save">
                                    <x-heroicon-o-plus class="w-4 h-4 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                    Create Acquisition
                                </span>
                                <span wire:loading wire:target="save" class="flex items-center gap-1.5">
                                    <x-icon-spinner class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" />
                                    Creating...
                                </span>
                            </x-button>
                        </div>
                    @endif

                </div>{{-- end footer --}}
            </div>
        </div>
    </div>
</div>
