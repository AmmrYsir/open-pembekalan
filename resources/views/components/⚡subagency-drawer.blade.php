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
    }

    public function switchToEdit(): void
    {
        $this->mode = 'edit';
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

<div>
    <div x-data="{ open: @entangle('showPanel') }" x-show="open" x-cloak class="relative z-50">
        <div x-show="open" x-transition:enter="ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in-out duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="open = false" class="fixed inset-0 bg-zinc-950/40 backdrop-blur-xs transition-opacity"></div>

        <div class="fixed inset-y-0 right-0 z-50 flex max-w-full pl-10">
            <div x-show="open" x-transition:enter="transform transition ease-in-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                 class="w-screen max-w-md bg-white dark:bg-zinc-900 shadow-2xl border-l border-zinc-200 dark:border-zinc-800 flex flex-col justify-between">
                
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">
                            {{ $mode === 'create' ? 'Add New Subagency' : ($mode === 'edit' ? 'Edit Subagency' : 'Subagency Details') }}
                        </h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Department or division level configuration.</p>
                    </div>
                    <button wire:click="closePanel" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                        <x-heroicon-o-x-mark class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-4">
                    @if($mode === 'view')
                        <div class="space-y-4 text-sm">
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Parent Agency</label>
                                <p class="font-semibold text-zinc-900 dark:text-zinc-100 mt-0.5">
                                    {{ $this->agencies->firstWhere('id', $agency_id)?->name ?? '-' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Subagency Code</label>
                                <p class="text-zinc-800 dark:text-zinc-200 mt-0.5">{{ $code }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Subagency Name</label>
                                <p class="text-zinc-800 dark:text-zinc-200 mt-0.5">{{ $name }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Status</label>
                                <div class="mt-1">
                                    <x-ui.badge variant="{{ $is_active ? 'success' : 'secondary' }}">
                                        {{ $is_active ? 'Active' : 'Inactive' }}
                                    </x-ui.badge>
                                </div>
                            </div>
                        </div>
                    @else
                        <form wire:submit.prevent="save" class="space-y-4">
                            <div>
                                <x-ui.label for="agency_id">Parent Agency *</x-ui.label>
                                <select id="agency_id" wire:model="agency_id" class="w-full px-3 py-2 text-sm bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded-xl text-zinc-900 dark:text-zinc-100">
                                    <option value="">Select Parent Agency</option>
                                    @foreach($this->agencies as $agency)
                                        <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                                    @endforeach
                                </select>
                                @error('agency_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-ui.label for="subagency_code">Subagency Code *</x-ui.label>
                                <x-ui.input id="subagency_code" wire:model="code" placeholder="e.g. SUB-201" />
                                @error('code') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-ui.label for="subagency_name">Subagency Name *</x-ui.label>
                                <x-ui.input id="subagency_name" wire:model="name" placeholder="e.g. Telecommunications Division" />
                                @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex items-center gap-3 pt-2">
                                <input id="sub_active" type="checkbox" wire:model="is_active" class="rounded border-zinc-300 text-emerald-600 focus:ring-emerald-500" />
                                <x-ui.label for="sub_active" class="cursor-pointer mb-0">Active Status</x-ui.label>
                            </div>
                        </form>
                    @endif
                </div>

                <div class="p-6 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 flex justify-end gap-3">
                    @if($mode === 'view')
                        <x-ui.button variant="outline" size="sm" wire:click="closePanel">Close</x-ui.button>
                        <x-ui.button variant="primary" size="sm" wire:click="switchToEdit">Edit Subagency</x-ui.button>
                    @else
                        <x-ui.button variant="outline" size="sm" wire:click="closePanel">Cancel</x-ui.button>
                        <x-ui.button variant="primary" size="sm" wire:click="save">Save Subagency</x-ui.button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
