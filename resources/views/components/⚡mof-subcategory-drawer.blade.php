<?php

use App\Models\MofCategory;
use App\Models\MofSubcategory;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public string $mode = 'create';
    public bool $showPanel = false;
    public ?int $activeId = null;

    public ?int $mof_category_id = null;
    public string $code = '';
    public string $name = '';

    #[On('open-mof-subcategory-drawer')]
    public function open(string $mode = 'create', ?int $id = null): void
    {
        $this->resetValidation();
        $this->reset(['mof_category_id', 'code', 'name']);
        $this->activeId = $id;
        $this->mode = $mode;

        if ($id && in_array($mode, ['view', 'edit'])) {
            $sub = MofSubcategory::findOrFail($id);
            $this->mof_category_id = $sub->mof_category_id;
            $this->code = $sub->code ?? '';
            $this->name = $sub->name ?? '';
        }

        $this->showPanel = true;
    }

    #[Computed]
    public function categories(): \Illuminate\Database\Eloquent\Collection
    {
        return MofCategory::orderBy('name')->get(['id', 'name']);
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
            'mof_category_id' => 'required|exists:mof_categories,id',
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
        ]);

        if ($this->mode === 'edit' && $this->activeId) {
            $sub = MofSubcategory::findOrFail($this->activeId);
            $sub->update($validated);
            session()->flash('success', 'MOF Subcategory updated successfully.');
        } else {
            MofSubcategory::create($validated);
            session()->flash('success', 'MOF Subcategory created successfully.');
        }

        $this->dispatch('mof-subcategory-saved');
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
                                    {{ $name ?: 'MOF Subcategory Details' }}
                                @elseif($mode === 'edit')
                                    Edit — {{ $name ?: 'MOF Subcategory' }}
                                @else
                                    New MOF Subcategory
                                @endif
                            </h2>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                @if($mode === 'view')
                                    Read-only view. Click <span class="font-medium text-amber-600 dark:text-amber-400">Edit</span> to make changes.
                                @elseif($mode === 'edit')
                                    Editing MOF subcategory record.
                                @else
                                    Add subcategory under main MOF category.
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
                                    Subcategory Details
                                    <span class="flex-1 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                </h3>
                                <dl class="grid grid-cols-2 gap-x-6 gap-y-4">
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Subcategory Code</dt>
                                        <dd class="mt-1 text-sm font-mono text-zinc-900 dark:text-zinc-100 font-semibold">{{ $code ?: '—' }}</dd>
                                    </div>
                                    <div class="col-span-2">
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Parent MOF Category</dt>
                                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100 font-semibold">
                                            {{ $this->categories->firstWhere('id', $mof_category_id)?->name ?? '—' }}
                                        </dd>
                                    </div>
                                    <div class="col-span-2">
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Subcategory Name</dt>
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
                                    Subcategory Information
                                    <span class="flex-1 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                </h3>
                                <div class="space-y-4">
                                    <div>
                                        <x-label for="mof_category_id">Parent MOF Category *</x-label>
                                        <select id="mof_category_id" wire:model="mof_category_id" class="w-full px-3 py-2 text-sm bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                                            <option value="">Select Parent Category</option>
                                            @foreach($this->categories as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('mof_category_id') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <x-label for="sub_code">Subcategory Code *</x-label>
                                        <x-input id="sub_code" wire:model="code" placeholder="e.g. 0101" />
                                        @error('code') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <x-label for="sub_name">Subcategory Name *</x-label>
                                        <x-input id="sub_name" wire:model="name" placeholder="e.g. Perabot Rumah dan Pejabat" />
                                        @error('name') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
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
                                Create Subcategory
                            </x-button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
