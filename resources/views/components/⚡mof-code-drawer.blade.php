<?php

use App\Models\MofCode;
use App\Models\MofSubcategory;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public string $mode = 'create';
    public bool $showPanel = false;
    public ?int $activeId = null;

    public ?int $mof_subcategory_id = null;
    public string $code = '';
    public string $name = '';

    #[On('open-mof-code-drawer')]
    public function open(string $mode = 'create', ?int $id = null): void
    {
        $this->resetValidation();
        $this->reset(['mof_subcategory_id', 'code', 'name']);
        $this->activeId = $id;
        $this->mode = $mode;

        if ($id && in_array($mode, ['view', 'edit'])) {
            $item = MofCode::findOrFail($id);
            $this->mof_subcategory_id = $item->mof_subcategory_id;
            $this->code = $item->code ?? '';
            $this->name = $item->name ?? '';
        }

        $this->showPanel = true;
    }

    #[Computed]
    public function subcategories(): \Illuminate\Database\Eloquent\Collection
    {
        return MofSubcategory::orderBy('name')->get(['id', 'name']);
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
            'mof_subcategory_id' => 'required|exists:mof_subcategories,id',
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
        ]);

        if ($this->mode === 'edit' && $this->activeId) {
            $item = MofCode::findOrFail($this->activeId);
            $item->update($validated);
            session()->flash('success', 'MOF Code updated successfully.');
        } else {
            MofCode::create($validated);
            session()->flash('success', 'MOF Code created successfully.');
        }

        $this->dispatch('mof-code-saved');
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
                            {{ $mode === 'create' ? 'Add MOF Code' : ($mode === 'edit' ? 'Edit MOF Code' : 'MOF Code Details') }}
                        </h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Ministry of Finance specialized field code configuration.</p>
                    </div>
                    <button wire:click="closePanel" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                        <x-heroicon-o-x-mark class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-4">
                    @if($mode === 'view')
                        <div class="space-y-4 text-sm">
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Parent Subcategory</label>
                                <p class="font-semibold text-zinc-900 dark:text-zinc-100 mt-0.5">
                                    {{ $this->subcategories->firstWhere('id', $mof_subcategory_id)?->name ?? '-' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">MOF Code</label>
                                <p class="font-mono text-xs text-emerald-700 dark:text-emerald-400 font-bold mt-0.5">{{ $code }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Field Name / Description</label>
                                <p class="text-zinc-800 dark:text-zinc-200 mt-0.5">{{ $name }}</p>
                            </div>
                        </div>
                    @else
                        <form wire:submit.prevent="save" class="space-y-4">
                            <div>
                                <x-ui.label for="mof_subcategory_id">MOF Subcategory *</x-ui.label>
                                <select id="mof_subcategory_id" wire:model="mof_subcategory_id" class="w-full px-3 py-2 text-sm bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded-xl text-zinc-900 dark:text-zinc-100">
                                    <option value="">Select Subcategory</option>
                                    @foreach($this->subcategories as $sub)
                                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                    @endforeach
                                </select>
                                @error('mof_subcategory_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-ui.label for="code_val">MOF Code *</x-ui.label>
                                <x-ui.input id="code_val" wire:model="code" placeholder="e.g. 010101" />
                                @error('code') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-ui.label for="code_name">Field Name / Description *</x-ui.label>
                                <x-ui.input id="code_name" wire:model="name" placeholder="e.g. Perabot, Perabot Makmal dan Kelengkapan Berasaskan Kayu/Rotan/Fabrik/Logam/Plastik" />
                                @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </form>
                    @endif
                </div>

                <div class="p-6 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 flex justify-end gap-3">
                    @if($mode === 'view')
                        <x-ui.button variant="outline" size="sm" wire:click="closePanel">Close</x-ui.button>
                        <x-ui.button variant="primary" size="sm" wire:click="switchToEdit">Edit MOF Code</x-ui.button>
                    @else
                        <x-ui.button variant="outline" size="sm" wire:click="closePanel">Cancel</x-ui.button>
                        <x-ui.button variant="primary" size="sm" wire:click="save">Save MOF Code</x-ui.button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
