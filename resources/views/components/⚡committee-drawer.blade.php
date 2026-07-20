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
                                    {{ $name ?: 'Committee Details' }}
                                @elseif($mode === 'edit')
                                    Edit — {{ $name ?: 'Committee' }}
                                @else
                                    New Committee Configuration
                                @endif
                            </h2>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                @if($mode === 'view')
                                    Read-only view. Click <span class="font-medium text-amber-600 dark:text-amber-400">Edit</span> to make changes.
                                @elseif($mode === 'edit')
                                    Editing evaluation committee roles and setup.
                                @else
                                    Configure procurement evaluation committee setup.
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
                                    Committee Information
                                    <span class="flex-1 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                </h3>
                                <dl class="grid grid-cols-2 gap-x-6 gap-y-4">
                                    <div class="col-span-2">
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Committee Name</dt>
                                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100 font-semibold">{{ $name ?: '—' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Slug</dt>
                                        <dd class="mt-1 text-sm font-mono text-zinc-800 dark:text-zinc-200">{{ $slug ?: '—' }}</dd>
                                    </div>
                                    <div class="col-span-2">
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Configured Positions</dt>
                                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100 font-medium">{{ $positionsInput ?: 'None configured' }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    @else
                        <form wire:submit.prevent="save" class="space-y-7">
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                                    <span class="w-5 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                    Committee Details
                                    <span class="flex-1 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                </h3>
                                <div class="space-y-4">
                                    <div>
                                        <x-ui.label for="comm_name">Committee Name *</x-ui.label>
                                        <x-ui.input id="comm_name" wire:model.live="name" placeholder="e.g. Jawatankuasa Penilaian Teknis" />
                                        @error('name') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <x-ui.label for="comm_slug">Slug *</x-ui.label>
                                        <x-ui.input id="comm_slug" wire:model="slug" placeholder="e.g. jawatankuasa-penilaian-teknis" />
                                        @error('slug') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <x-ui.label for="comm_positions">Committee Roles / Positions (Comma-separated)</x-ui.label>
                                        <x-ui.input id="comm_positions" wire:model="positionsInput" placeholder="Pengerusi, Ahli, Urusetia" />
                                        <p class="text-xs text-zinc-400 mt-1">Separate multiple roles with commas.</p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>

                <div class="px-6 py-4 border-t border-zinc-100 dark:border-zinc-800/50 bg-zinc-50/60 dark:bg-zinc-900/60">
                    @if($mode === 'view')
                        <div class="flex items-center justify-end gap-2">
                            <x-ui.button variant="outline" size="sm" wire:click="switchToEdit">
                                <x-heroicon-o-pencil class="w-3.5 h-3.5 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                Edit
                            </x-ui.button>
                            <x-ui.button variant="secondary" size="sm" wire:click="closePanel">Close</x-ui.button>
                        </div>
                    @elseif($mode === 'edit')
                        <div class="flex items-center justify-end gap-2">
                            <x-ui.button variant="outline" size="sm" wire:click="switchToView">
                                <x-heroicon-o-chevron-left class="w-4 h-4 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                Back to View
                            </x-ui.button>
                            <x-ui.button variant="primary" size="sm" wire:click="save">Save Changes</x-ui.button>
                        </div>
                    @else
                        <div class="flex items-center justify-end gap-3">
                            <x-ui.button variant="outline" size="sm" wire:click="closePanel">Cancel</x-ui.button>
                            <x-ui.button variant="primary" size="sm" wire:click="save">
                                <x-heroicon-o-plus class="w-4 h-4 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                Create Committee
                            </x-ui.button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
