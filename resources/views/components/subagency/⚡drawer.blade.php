<?php

use App\Models\Agency;
use App\Models\Subagency;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public string $mode = 'create';
    public bool $showPanel = false;
    public ?int $activeId = null;

    public ?int $agency_id = null;
    public string $code = '';
    public string $name = '';
    public bool $is_active = true;

    #[On('open-subagency-drawer')]
    public function open(string $mode = 'create', ?int $id = null): void
    {
        $this->resetValidation();
        $this->reset(['agency_id', 'code', 'name', 'is_active']);
        $this->activeId = $id;
        $this->mode = $mode;

        if ($id && in_array($mode, ['view', 'edit'])) {
            $subagency = Subagency::findOrFail($id);
            $this->agency_id = $subagency->agency_id;
            $this->code = $subagency->code ?? '';
            $this->name = $subagency->name ?? '';
            $this->is_active = (bool) $subagency->is_active;
        }

        $this->showPanel = true;
    }

    #[Computed]
    public function agencies(): \Illuminate\Database\Eloquent\Collection
    {
        return Agency::orderBy('name')->get(['id', 'name']);
    }

    public function closePanel(): void
    {
        $this->showPanel = false;
        $this->resetValidation();
    }

    public function switchToEdit(): void
    {
        $this->mode = 'edit';
    }

    public function switchToView(): void
    {
        $this->mode = 'view';
    }

    public function save(): void
    {
        $validated = $this->validate([
            'agency_id' => 'required|exists:agencies,id',
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($this->mode === 'edit' && $this->activeId) {
            $subagency = Subagency::findOrFail($this->activeId);
            $subagency->update($validated);
            session()->flash('success', 'Subagency updated successfully.');
        } else {
            Subagency::create($validated);
            session()->flash('success', 'Subagency created successfully.');
        }

        $this->dispatch('subagency-saved');
        $this->showPanel = false;
    }
};
?>

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
    <div class="fixed inset-0 bg-zinc-950/50 backdrop-blur-sm" wire:click="closePanel"></div>

    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
        <div
            x-show="$wire.showPanel"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="pointer-events-auto w-screen max-w-xl"
        >
            <div class="flex h-full flex-col bg-white dark:bg-zinc-900 shadow-2xl border-l border-zinc-200/80 dark:border-zinc-800/80">

                <div class="flex items-center justify-between px-6 py-5 border-b border-zinc-100 dark:border-zinc-800/50">
                    <div class="flex items-center gap-3 min-w-0">
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
                                    {{ $name ?: 'Subagency Details' }}
                                @elseif($mode === 'edit')
                                    Edit — {{ $name ?: 'Subagency' }}
                                @else
                                    New Subagency Record
                                @endif
                            </h2>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                @if($mode === 'view')
                                    Read-only view. Click <span class="font-medium text-amber-600 dark:text-amber-400">Edit</span> to make changes.
                                @elseif($mode === 'edit')
                                    Editing subagency division details.
                                @else
                                    Create a new subagency unit or department.
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

                <div class="flex-1 overflow-y-auto px-6 py-6">
                    @if($mode === 'view')
                        <div class="space-y-7">
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                                    <span class="w-5 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                    Subagency Details
                                    <span class="flex-1 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                </h3>
                                <dl class="grid grid-cols-2 gap-x-6 gap-y-4">
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Subagency Code</dt>
                                        <dd class="mt-1 text-sm font-mono text-zinc-900 dark:text-zinc-100 font-semibold">{{ $code ?: '—' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Status</dt>
                                        <dd class="mt-1">
                                            <x-badge variant="{{ $is_active ? 'success' : 'secondary' }}" pill>
                                                {{ $is_active ? 'Active' : 'Inactive' }}
                                            </x-badge>
                                        </dd>
                                    </div>
                                    <div class="col-span-2">
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Parent Agency</dt>
                                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100 font-semibold">
                                            {{ $this->agencies->firstWhere('id', $agency_id)?->name ?? '—' }}
                                        </dd>
                                    </div>
                                    <div class="col-span-2">
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Subagency Name</dt>
                                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100 font-semibold">{{ $name ?: '—' }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    @else
                        <form wire:submit.prevent="save" class="space-y-7">
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                                    <span class="w-5 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                    Subagency Information
                                    <span class="flex-1 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                </h3>
                                <div class="space-y-4">
                                    <div>
                                        <x-label for="agency_id">Parent Agency *</x-label>
                                        <select id="agency_id" wire:model="agency_id" class="w-full px-3 py-2 text-sm bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                                            <option value="">Select Parent Agency</option>
                                            @foreach($this->agencies as $agency)
                                                <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('agency_id') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <x-label for="subagency_code">Subagency Code *</x-label>
                                        <x-input id="subagency_code" wire:model="code" placeholder="e.g. SUB-201" />
                                        @error('code') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <x-label for="subagency_name">Subagency Name *</x-label>
                                        <x-input id="subagency_name" wire:model="name" placeholder="e.g. Telecommunications Division" />
                                        @error('name') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="flex items-center gap-3 pt-2">
                                        <input id="sub_active" type="checkbox" wire:model="is_active" class="rounded border-zinc-300 text-emerald-600 focus:ring-emerald-500" />
                                        <x-label for="sub_active" class="cursor-pointer mb-0">Active Status</x-label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>

                <div class="px-6 py-4 border-t border-zinc-100 dark:border-zinc-800/50 bg-zinc-50/60 dark:bg-zinc-900/60">
                    @if($mode === 'view')
                        <div class="flex items-center justify-end gap-2">
                            <x-button variant="outline" size="sm" wire:click="switchToEdit">
                                <x-heroicon-o-pencil class="w-3.5 h-3.5 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                Edit
                            </x-button>
                            <x-button variant="secondary" size="sm" wire:click="closePanel">Close</x-button>
                        </div>
                    @elseif($mode === 'edit')
                        <div class="flex items-center justify-end gap-2">
                            <x-button variant="outline" size="sm" wire:click="switchToView">
                                <x-heroicon-o-chevron-left class="w-4 h-4 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                Back to View
                            </x-button>
                            <x-button variant="primary" size="sm" wire:click="save">Save Changes</x-button>
                        </div>
                    @else
                        <div class="flex items-center justify-end gap-3">
                            <x-button variant="outline" size="sm" wire:click="closePanel">Cancel</x-button>
                            <x-button variant="primary" size="sm" wire:click="save">
                                <x-heroicon-o-plus class="w-4 h-4 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                Create Subagency
                            </x-button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
