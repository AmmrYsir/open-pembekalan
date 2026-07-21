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

            $passwordToSave = $this->generatedPassword ?: ('Pass#' . Str::random(8));
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($passwordToSave),
                'email_verified_at' => null,
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
            class="pointer-events-auto w-screen max-w-xl"
        >
            <div class="flex h-full flex-col bg-white dark:bg-zinc-900 shadow-2xl border-l border-zinc-200/80 dark:border-zinc-800/80">

                {{-- ── Panel Header ──────────────────────────────────────── --}}
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
                                    {{ $name ?: 'Officer Details' }}
                                @elseif($mode === 'edit')
                                    Edit — {{ $name ?: 'Officer' }}
                                @else
                                    New Agency Officer Account
                                @endif
                            </h2>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                @if($mode === 'view')
                                    Read-only view. Click <span class="font-medium text-amber-600 dark:text-amber-400">Edit</span> to update account.
                                @elseif($mode === 'edit')
                                    Updating agency officer details & account credentials.
                                @else
                                    Create user account and assign agency officer position.
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

                {{-- ── Scrollable Body ────────────────────────────────────── --}}
                <div class="flex-1 overflow-y-auto px-6 py-6">

                    @if($mode === 'view')
                        {{-- VIEW MODE --}}
                        <div class="space-y-7">
                            {{-- Section: Account & Credentials --}}
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                                    <span class="w-5 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                    Account & Login Credentials
                                    <span class="flex-1 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                </h3>
                                <dl class="grid grid-cols-2 gap-x-6 gap-y-4">
                                    <div class="col-span-2">
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Full Name</dt>
                                        <dd class="mt-1 text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $name ?: '—' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Login Email</dt>
                                        <dd class="mt-1 text-sm font-mono text-zinc-800 dark:text-zinc-200">{{ $email ?: '—' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Verification Status</dt>
                                        <dd class="mt-1">
                                            @if($isEmailVerified)
                                                <x-badge variant="success" pill>Verified</x-badge>
                                            @else
                                                <x-badge variant="warning" pill>Awaiting Verification</x-badge>
                                            @endif
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            {{-- Section: Officer Profile & Assignment --}}
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                                    <span class="w-5 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                    Officer Profile & Assignment
                                    <span class="flex-1 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                </h3>
                                <dl class="grid grid-cols-2 gap-x-6 gap-y-4">
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Title & Position</dt>
                                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100 font-medium">{{ $title }} {{ $position ? "($position)" : '—' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">IC / NRIC</dt>
                                        <dd class="mt-1 text-sm font-mono text-zinc-800 dark:text-zinc-200">{{ $nric ?: '—' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Assigned Agency</dt>
                                        <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                            {{ $this->agencies->firstWhere('id', $agency_id)?->name ?? '—' }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Mobile Number</dt>
                                        <dd class="mt-1 text-sm font-mono text-zinc-800 dark:text-zinc-200">{{ $mobile_number ?: '—' }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                    @else
                        {{-- CREATE / EDIT FORM MODE --}}
                        <form wire:submit.prevent="save" class="space-y-7">
                            {{-- Section: Account Information --}}
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                                    <span class="w-5 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                    Account & Login Credentials
                                    <span class="flex-1 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                </h3>

                                <div class="space-y-4">
                                    <div>
                                        <x-label for="off_name">Full Name *</x-label>
                                        <x-input id="off_name" wire:model="name" placeholder="e.g. Ir. Dr. Hafiz Basri" />
                                        @error('name') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <x-label for="off_email">Login Email Address *</x-label>
                                        <x-input id="off_email" type="email" wire:model="email" placeholder="officer@agency.gov.my" />
                                        @error('email') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                                        <p class="text-xs text-zinc-400 mt-1">Officer must verify this email address upon initial login.</p>
                                    </div>

                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <x-label for="generatedPassword" class="mb-0">Auto-Generated Password</x-label>
                                            <button type="button" wire:click="generateNewPassword" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Regenerate</button>
                                        </div>
                                        <x-input id="generatedPassword" wire:model="generatedPassword" readonly class="font-mono text-xs text-emerald-700 dark:text-emerald-400 font-bold bg-zinc-50 dark:bg-zinc-800/60" />
                                        <p class="text-xs text-zinc-400 mt-1">Provide this temporary password directly to the officer.</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Section: Agency Assignment --}}
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                                    <span class="w-5 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                    Agency Assignment & Details
                                    <span class="flex-1 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                </h3>

                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <x-label for="agency_id">Assigned Agency *</x-label>
                                            <select id="agency_id" wire:model.live="agency_id" class="w-full px-3 py-2 text-sm bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                                                <option value="">Select Agency</option>
                                                @foreach($this->agencies as $agency)
                                                    <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('agency_id') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                                        </div>

                                        <div>
                                            <x-label for="subagency_id">Subagency</x-label>
                                            <select id="subagency_id" wire:model="subagency_id" class="w-full px-3 py-2 text-sm bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded-xl text-zinc-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                                                <option value="">Select Subagency (Optional)</option>
                                                @foreach($this->subagencies as $sub)
                                                    <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <x-label for="title">Title</x-label>
                                            <x-input id="title" wire:model="title" placeholder="e.g. Encik / Ir." />
                                        </div>
                                        <div>
                                            <x-label for="position">Position</x-label>
                                            <x-input id="position" wire:model="position" placeholder="e.g. Senior Officer" />
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <x-label for="nric">NRIC / MyKad No.</x-label>
                                            <x-input id="nric" wire:model="nric" placeholder="e.g. 880101-14-5555" />
                                        </div>
                                        <div>
                                            <x-label for="mobile_number">Mobile Number</x-label>
                                            <x-input id="mobile_number" wire:model="mobile_number" placeholder="012-3456789" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endif

                </div>

                {{-- ── Panel Footer ───────────────────────────────────────── --}}
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
                                Create Officer Account
                            </x-button>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
