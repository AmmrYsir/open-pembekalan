<?php

use App\Models\State;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public string $mode = 'create';
    public bool $showPanel = false;
    public ?int $activeId = null;

    public string $code = '';
    public string $shortname = '';
    public string $fullname = '';
    public string $capital = '';

    #[On('open-state-drawer')]
    public function open(string $mode = 'create', ?int $id = null): void
    {
        $this->resetValidation();
        $this->reset(['code', 'shortname', 'fullname', 'capital']);
        $this->activeId = $id;
        $this->mode = $mode;

        if ($id && in_array($mode, ['view', 'edit'])) {
            $st = State::findOrFail($id);
            $this->code = $st->code ?? '';
            $this->shortname = $st->shortname ?? '';
            $this->fullname = $st->fullname ?? '';
            $this->capital = $st->capital ?? '';
        }

        $this->showPanel = true;
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
            'code' => 'required|string|max:10',
            'shortname' => 'required|string|max:50',
            'fullname' => 'required|string|max:100',
            'capital' => 'nullable|string|max:100',
        ]);

        if ($this->mode === 'edit' && $this->activeId) {
            $st = State::findOrFail($this->activeId);
            $st->update($validated);
            session()->flash('success', 'State updated successfully.');
        } else {
            State::create($validated);
            session()->flash('success', 'State created successfully.');
        }

        $this->dispatch('state-saved');
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
                            {{ $mode === 'create' ? 'Add New State' : ($mode === 'edit' ? 'Edit State' : 'State Details') }}
                        </h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">State master reference data.</p>
                    </div>
                    <button wire:click="closePanel" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                        <x-heroicon-o-x-mark class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-4">
                    @if($mode === 'view')
                        <div class="space-y-4 text-sm">
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">State Code</label>
                                <p class="font-mono text-xs font-semibold text-zinc-900 dark:text-zinc-100 mt-0.5">{{ $code }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Short Name</label>
                                <p class="text-zinc-800 dark:text-zinc-200 mt-0.5">{{ $shortname }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Full Name</label>
                                <p class="font-semibold text-zinc-900 dark:text-zinc-100 mt-0.5">{{ $fullname }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">State Capital</label>
                                <p class="text-zinc-800 dark:text-zinc-200 mt-0.5">{{ $capital ?: '-' }}</p>
                            </div>
                        </div>
                    @else
                        <form wire:submit.prevent="save" class="space-y-4">
                            <div>
                                <x-ui.label for="state_code">State Code *</x-ui.label>
                                <x-ui.input id="state_code" wire:model="code" placeholder="e.g. SGR" />
                                @error('code') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-ui.label for="state_shortname">Short Name *</x-ui.label>
                                <x-ui.input id="state_shortname" wire:model="shortname" placeholder="e.g. Selangor" />
                                @error('shortname') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-ui.label for="state_fullname">Full Name *</x-ui.label>
                                <x-ui.input id="state_fullname" wire:model="fullname" placeholder="e.g. Selangor Darul Ehsan" />
                                @error('fullname') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-ui.label for="state_capital">Capital</x-ui.label>
                                <x-ui.input id="state_capital" wire:model="capital" placeholder="e.g. Shah Alam" />
                            </div>
                        </form>
                    @endif
                </div>

                <div class="p-6 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 flex justify-end gap-3">
                    @if($mode === 'view')
                        <x-ui.button variant="outline" size="sm" wire:click="closePanel">Close</x-ui.button>
                        <x-ui.button variant="primary" size="sm" wire:click="switchToEdit">Edit State</x-ui.button>
                    @else
                        <x-ui.button variant="outline" size="sm" wire:click="closePanel">Cancel</x-ui.button>
                        <x-ui.button variant="primary" size="sm" wire:click="save">Save State</x-ui.button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
