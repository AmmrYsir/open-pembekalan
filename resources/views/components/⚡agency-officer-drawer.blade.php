<?php

use App\Models\Agency;
use App\Models\AgencyOfficer;
use App\Models\Subagency;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public string $mode = 'create';
    public bool $showPanel = false;
    public ?int $activeId = null;

    public ?int $user_id = null;
    public ?int $agency_id = null;
    public ?int $subagency_id = null;
    public string $title = '';
    public string $nric = '';
    public string $position = '';
    public string $mobile_number = '';

    #[On('open-agency-officer-drawer')]
    public function open(string $mode = 'create', ?int $id = null): void
    {
        $this->resetValidation();
        $this->reset(['user_id', 'agency_id', 'subagency_id', 'title', 'nric', 'position', 'mobile_number']);
        $this->activeId = $id;
        $this->mode = $mode;

        if ($id && in_array($mode, ['view', 'edit'])) {
            $officer = AgencyOfficer::findOrFail($id);
            $this->user_id = $officer->user_id;
            $this->agency_id = $officer->agency_id;
            $this->subagency_id = $officer->subagency_id;
            $this->title = $officer->title ?? '';
            $this->nric = $officer->nric ?? '';
            $this->position = $officer->position ?? '';
            $this->mobile_number = $officer->mobile_number ?? '';
        }

        $this->showPanel = true;
    }

    #[Computed]
    public function users(): \Illuminate\Database\Eloquent\Collection
    {
        return User::orderBy('name')->get(['id', 'name']);
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
            'user_id' => 'required|exists:users,id',
            'agency_id' => 'required|exists:agencies,id',
            'subagency_id' => 'nullable|exists:subagencies,id',
            'title' => 'nullable|string|max:50',
            'nric' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:100',
            'mobile_number' => 'nullable|string|max:50',
        ]);

        if ($this->mode === 'edit' && $this->activeId) {
            $officer = AgencyOfficer::findOrFail($this->activeId);
            $officer->update($validated);
            session()->flash('success', 'Agency Officer updated successfully.');
        } else {
            AgencyOfficer::create($validated);
            session()->flash('success', 'Agency Officer created successfully.');
        }

        $this->dispatch('agency-officer-saved');
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
                            {{ $mode === 'create' ? 'Add Agency Officer' : ($mode === 'edit' ? 'Edit Officer' : 'Officer Details') }}
                        </h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Agency contact person & procurement officer details.</p>
                    </div>
                    <button wire:click="closePanel" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                        <x-heroicon-o-x-mark class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-4">
                    @if($mode === 'view')
                        <div class="space-y-4 text-sm">
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">User Account</label>
                                <p class="font-semibold text-zinc-900 dark:text-zinc-100 mt-0.5">
                                    {{ $this->users->firstWhere('id', $user_id)?->name ?? '-' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Title & Position</label>
                                <p class="text-zinc-800 dark:text-zinc-200 mt-0.5">{{ $title }} {{ $position ? "($position)" : '' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">IC / NRIC</label>
                                <p class="text-zinc-800 dark:text-zinc-200 mt-0.5">{{ $nric ?: '-' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Agency</label>
                                <p class="text-zinc-800 dark:text-zinc-200 mt-0.5">
                                    {{ $this->agencies->firstWhere('id', $agency_id)?->name ?? '-' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Mobile Number</label>
                                <p class="text-zinc-800 dark:text-zinc-200 mt-0.5">{{ $mobile_number ?: '-' }}</p>
                            </div>
                        </div>
                    @else
                        <form wire:submit.prevent="save" class="space-y-4">
                            <div>
                                <x-ui.label for="user_id">User Account *</x-ui.label>
                                <select id="user_id" wire:model="user_id" class="w-full px-3 py-2 text-sm bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded-xl text-zinc-900 dark:text-zinc-100">
                                    <option value="">Select User</option>
                                    @foreach($this->users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                                @error('user_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-ui.label for="agency_id">Agency *</x-ui.label>
                                <select id="agency_id" wire:model.live="agency_id" class="w-full px-3 py-2 text-sm bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded-xl text-zinc-900 dark:text-zinc-100">
                                    <option value="">Select Agency</option>
                                    @foreach($this->agencies as $agency)
                                        <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                                    @endforeach
                                </select>
                                @error('agency_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-ui.label for="subagency_id">Subagency</x-ui.label>
                                <select id="subagency_id" wire:model="subagency_id" class="w-full px-3 py-2 text-sm bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded-xl text-zinc-900 dark:text-zinc-100">
                                    <option value="">Select Subagency (Optional)</option>
                                    @foreach($this->subagencies as $sub)
                                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <x-ui.label for="title">Title</x-ui.label>
                                    <x-ui.input id="title" wire:model="title" placeholder="e.g. Encik / Ir." />
                                </div>
                                <div>
                                    <x-ui.label for="position">Position</x-ui.label>
                                    <x-ui.input id="position" wire:model="position" placeholder="e.g. Senior Officer" />
                                </div>
                            </div>

                            <div>
                                <x-ui.label for="nric">NRIC / MyKad No.</x-ui.label>
                                <x-ui.input id="nric" wire:model="nric" placeholder="e.g. 880101-14-5555" />
                            </div>

                            <div>
                                <x-ui.label for="mobile_number">Mobile Number</x-ui.label>
                                <x-ui.input id="mobile_number" wire:model="mobile_number" placeholder="012-3456789" />
                            </div>
                        </form>
                    @endif
                </div>

                <div class="p-6 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 flex justify-end gap-3">
                    @if($mode === 'view')
                        <x-ui.button variant="outline" size="sm" wire:click="closePanel">Close</x-ui.button>
                        <x-ui.button variant="primary" size="sm" wire:click="switchToEdit">Edit Officer</x-ui.button>
                    @else
                        <x-ui.button variant="outline" size="sm" wire:click="closePanel">Cancel</x-ui.button>
                        <x-ui.button variant="primary" size="sm" wire:click="save">Save Officer</x-ui.button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
