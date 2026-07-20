<?php

use App\Models\Committee;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public string $mode = 'create';
    public bool $showPanel = false;
    public ?int $activeId = null;

    public string $name = '';
    public string $slug = '';
    public string $positionsInput = '';

    #[On('open-committee-drawer')]
    public function open(string $mode = 'create', ?int $id = null): void
    {
        $this->resetValidation();
        $this->reset(['name', 'slug', 'positionsInput']);
        $this->activeId = $id;
        $this->mode = $mode;

        if ($id && in_array($mode, ['view', 'edit'])) {
            $committee = Committee::findOrFail($id);
            $this->name = $committee->name ?? '';
            $this->slug = $committee->slug ?? '';
            $this->positionsInput = is_array($committee->position) ? implode(', ', $committee->position) : '';
        }

        $this->showPanel = true;
    }

    public function updatedName(): void
    {
        if ($this->mode === 'create') {
            $this->slug = Str::slug($this->name);
        }
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
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'positionsInput' => 'nullable|string',
        ]);

        $positionsArray = array_filter(array_map('trim', explode(',', $this->positionsInput)));

        $data = [
            'name' => $validated['name'],
            'slug' => Str::slug($validated['slug']),
            'position' => array_values($positionsArray),
        ];

        if ($this->mode === 'edit' && $this->activeId) {
            $committee = Committee::findOrFail($this->activeId);
            $committee->update($data);
            session()->flash('success', 'Committee updated successfully.');
        } else {
            Committee::create($data);
            session()->flash('success', 'Committee created successfully.');
        }

        $this->dispatch('committee-saved');
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
                            {{ $mode === 'create' ? 'Add New Committee' : ($mode === 'edit' ? 'Edit Committee' : 'Committee Details') }}
                        </h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Procurement evaluation committee setup.</p>
                    </div>
                    <button wire:click="closePanel" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                        <x-heroicon-o-x-mark class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-4">
                    @if($mode === 'view')
                        <div class="space-y-4 text-sm">
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Committee Name</label>
                                <p class="font-semibold text-zinc-900 dark:text-zinc-100 mt-0.5">{{ $name }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Slug</label>
                                <p class="font-mono text-xs text-zinc-700 dark:text-zinc-300 mt-0.5">{{ $slug }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Configured Positions</label>
                                <p class="text-zinc-800 dark:text-zinc-200 mt-0.5">{{ $positionsInput ?: 'None' }}</p>
                            </div>
                        </div>
                    @else
                        <form wire:submit.prevent="save" class="space-y-4">
                            <div>
                                <x-ui.label for="comm_name">Committee Name *</x-ui.label>
                                <x-ui.input id="comm_name" wire:model.live="name" placeholder="e.g. Jawatankuasa Penilaian Teknis" />
                                @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-ui.label for="comm_slug">Slug *</x-ui.label>
                                <x-ui.input id="comm_slug" wire:model="slug" placeholder="e.g. jawatankuasa-penilaian-teknis" />
                                @error('slug') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-ui.label for="comm_positions">Committee Roles / Positions (Comma-separated)</x-ui.label>
                                <x-ui.input id="comm_positions" wire:model="positionsInput" placeholder="Pengerusi, Ahli, Urusetia" />
                                <p class="text-xs text-zinc-400 mt-1">Separate multiple roles with commas.</p>
                            </div>
                        </form>
                    @endif
                </div>

                <div class="p-6 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 flex justify-end gap-3">
                    @if($mode === 'view')
                        <x-ui.button variant="outline" size="sm" wire:click="closePanel">Close</x-ui.button>
                        <x-ui.button variant="primary" size="sm" wire:click="switchToEdit">Edit Committee</x-ui.button>
                    @else
                        <x-ui.button variant="outline" size="sm" wire:click="closePanel">Cancel</x-ui.button>
                        <x-ui.button variant="primary" size="sm" wire:click="save">Save Committee</x-ui.button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
