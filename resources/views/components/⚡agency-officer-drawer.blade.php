<?php

use App\Models\Agency;
use App\Models\AgencyOfficer;
use App\Models\Subagency;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public string $mode = 'create';
    public bool $showPanel = false;
    public ?int $activeId = null;

    // User Account fields
    public string $name = '';
    public string $email = '';
    public string $generatedPassword = '';
    public bool $isEmailVerified = false;

    // Agency Officer fields
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
        $this->reset(['name', 'email', 'generatedPassword', 'isEmailVerified', 'agency_id', 'subagency_id', 'title', 'nric', 'position', 'mobile_number']);
        $this->activeId = $id;
        $this->mode = $mode;

        if ($mode === 'create') {
            $this->generatedPassword = 'Pass#' . Str::random(8);
        }

        if ($id && in_array($mode, ['view', 'edit'])) {
            $officer = AgencyOfficer::with('user')->findOrFail($id);
            $this->agency_id = $officer->agency_id;
            $this->subagency_id = $officer->subagency_id;
            $this->title = $officer->title ?? '';
            $this->nric = $officer->nric ?? '';
            $this->position = $officer->position ?? '';
            $this->mobile_number = $officer->mobile_number ?? '';

            if ($officer->user) {
                $this->name = $officer->user->name ?? '';
                $this->email = $officer->user->email ?? '';
                $this->isEmailVerified = ! is_null($officer->user->email_verified_at);
            }
        }

        $this->showPanel = true;
    }

    public function generateNewPassword(): void
    {
        $this->generatedPassword = 'Pass#' . Str::random(8);
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
        if ($this->mode === 'create') {
            $validated = $this->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'agency_id' => 'required|exists:agencies,id',
                'subagency_id' => 'nullable|exists:subagencies,id',
                'title' => 'nullable|string|max:50',
                'nric' => 'nullable|string|max:50',
                'position' => 'nullable|string|max:100',
                'mobile_number' => 'nullable|string|max:50',
            ]);

            // Create User account with auto-generated password and email_verified_at = null (needs self verification)
            $passwordToSave = $this->generatedPassword ?: ('Pass#' . Str::random(8));
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($passwordToSave),
                'email_verified_at' => null, // Unverified initially for officer self-verification
            ]);

            AgencyOfficer::create([
                'user_id' => $user->id,
                'agency_id' => $validated['agency_id'],
                'subagency_id' => $validated['subagency_id'],
                'title' => $validated['title'],
                'nric' => $validated['nric'],
                'position' => $validated['position'],
                'mobile_number' => $validated['mobile_number'],
            ]);

            session()->flash('success', "Agency Officer created. Default Password: {$passwordToSave}");
        } elseif ($this->mode === 'edit' && $this->activeId) {
            $officer = AgencyOfficer::with('user')->findOrFail($this->activeId);

            $validated = $this->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $officer->user_id,
                'agency_id' => 'required|exists:agencies,id',
                'subagency_id' => 'nullable|exists:subagencies,id',
                'title' => 'nullable|string|max:50',
                'nric' => 'nullable|string|max:50',
                'position' => 'nullable|string|max:100',
                'mobile_number' => 'nullable|string|max:50',
            ]);

            if ($officer->user) {
                $userData = [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ];
                if ($this->generatedPassword) {
                    $userData['password'] = Hash::make($this->generatedPassword);
                }
                $officer->user->update($userData);
            }

            $officer->update([
                'agency_id' => $validated['agency_id'],
                'subagency_id' => $validated['subagency_id'],
                'title' => $validated['title'],
                'nric' => $validated['nric'],
                'position' => $validated['position'],
                'mobile_number' => $validated['mobile_number'],
            ]);

            session()->flash('success', 'Agency Officer details updated successfully.');
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
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Setup user credentials and focal agency assignment.</p>
                    </div>
                    <button wire:click="closePanel" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                        <x-heroicon-o-x-mark class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-4">
                    @if($mode === 'view')
                        <div class="space-y-4 text-sm">
                            <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700/60 space-y-2">
                                <span class="text-xs font-semibold uppercase tracking-wider text-zinc-400">Account Credentials</span>
                                <div>
                                    <label class="text-xs text-zinc-500 dark:text-zinc-400">Officer Full Name</label>
                                    <p class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $name }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-zinc-500 dark:text-zinc-400">Login Email</label>
                                    <p class="text-zinc-800 dark:text-zinc-200 font-mono text-xs">{{ $email }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-zinc-500 dark:text-zinc-400">Email Verification Status</label>
                                    <div class="mt-1">
                                        @if($isEmailVerified)
                                            <x-ui.badge variant="success">Verified</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="warning">Awaiting Verification</x-ui.badge>
                                        @endif
                                    </div>
                                </div>
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
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Assigned Agency</label>
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
                            <div class="p-4 rounded-xl bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800/40 space-y-3">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5">
                                    <x-heroicon-o-user class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                    Account & Login Information
                                </h4>

                                <div>
                                    <x-ui.label for="off_name">Full Name *</x-ui.label>
                                    <x-ui.input id="off_name" wire:model="name" placeholder="e.g. Ir. Dr. Hafiz Basri" />
                                    @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <x-ui.label for="off_email">Login Email Address *</x-ui.label>
                                    <x-ui.input id="off_email" type="email" wire:model="email" placeholder="officer@agency.gov.my" />
                                    @error('email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-1">Officer must verify this email address upon first login.</p>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <x-ui.label for="generatedPassword" class="mb-0">Auto-Generated Password</x-ui.label>
                                        <button type="button" wire:click="generateNewPassword" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Regenerate</button>
                                    </div>
                                    <div class="relative">
                                        <x-ui.input id="generatedPassword" wire:model="generatedPassword" readonly class="bg-white dark:bg-zinc-900 font-mono text-xs text-emerald-700 dark:text-emerald-400 font-bold" />
                                    </div>
                                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-1">Provide this temporary password directly to the officer.</p>
                                </div>
                            </div>

                            <div>
                                <x-ui.label for="agency_id">Assigned Agency *</x-ui.label>
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
                        <x-ui.button variant="primary" size="sm" wire:click="save">Save Officer Account</x-ui.button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
