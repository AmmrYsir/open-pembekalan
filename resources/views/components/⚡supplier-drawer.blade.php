<?php

use App\Models\Supplier;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public string $mode = 'create'; // 'create' | 'view' | 'edit'
    public bool $showPanel = false;
    public ?int $activeId = null;

    // Form fields
    public string $company_name = '';
    public string $ssm_number = '';
    public string $mobile_no = '';
    public string $telephone_no = '';
    public string $mof_registration_no = '';
    public string $paid_up_capital = '';
    public string $application_status = 'APPROVED';

    #[On('open-supplier-drawer')]
    public function open(string $mode = 'create', ?int $id = null): void
    {
        $this->resetValidation();
        $this->reset(['company_name', 'ssm_number', 'mobile_no', 'telephone_no', 'mof_registration_no', 'paid_up_capital', 'application_status']);
        $this->activeId = $id;
        $this->mode = $mode;

        if ($id && in_array($mode, ['view', 'edit'])) {
            $supplier = Supplier::findOrFail($id);
            $this->company_name = $supplier->company_name ?? '';
            $this->ssm_number = $supplier->ssm_number ?? '';
            $this->mobile_no = $supplier->mobile_no ?? '';
            $this->telephone_no = $supplier->telephone_no ?? '';
            $this->mof_registration_no = $supplier->mof_registration_no ?? '';
            $this->paid_up_capital = (string) ($supplier->paid_up_capital ?? '');
            $this->application_status = $supplier->application_status ?? 'APPROVED';
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
            'company_name' => 'required|string|max:255',
            'ssm_number' => 'nullable|string|max:50',
            'mobile_no' => 'nullable|string|max:50',
            'telephone_no' => 'nullable|string|max:50',
            'mof_registration_no' => 'nullable|string|max:100',
            'paid_up_capital' => 'nullable|numeric',
            'application_status' => 'required|string',
        ]);

        if ($this->mode === 'edit' && $this->activeId) {
            $supplier = Supplier::findOrFail($this->activeId);
            $supplier->update($validated);
            session()->flash('success', 'Supplier details updated successfully.');
        } else {
            Supplier::create($validated);
            session()->flash('success', 'New supplier created successfully.');
        }

        $this->dispatch('supplier-saved');
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
                
                {{-- Header --}}
                <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">
                            {{ $mode === 'create' ? 'Add New Supplier' : ($mode === 'edit' ? 'Edit Supplier' : 'Supplier Details') }}
                        </h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Manage vendor registration information.</p>
                    </div>
                    <button wire:click="closePanel" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                        <x-heroicon-o-x-mark class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </button>
                </div>

                {{-- Content --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-4">
                    @if($mode === 'view')
                        <div class="space-y-4 text-sm">
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Company Name</label>
                                <p class="font-semibold text-zinc-900 dark:text-zinc-100 mt-0.5">{{ $company_name }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">SSM Number</label>
                                <p class="text-zinc-800 dark:text-zinc-200 mt-0.5">{{ $ssm_number ?: '-' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Mobile / Telephone</label>
                                <p class="text-zinc-800 dark:text-zinc-200 mt-0.5">{{ $mobile_no ?: ($telephone_no ?: '-') }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">MOF Registration No.</label>
                                <p class="text-zinc-800 dark:text-zinc-200 mt-0.5">{{ $mof_registration_no ?: '-' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Paid-Up Capital (MYR)</label>
                                <p class="text-zinc-800 dark:text-zinc-200 mt-0.5">{{ $paid_up_capital ? number_format((float)$paid_up_capital, 2) : '-' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Status</label>
                                <div class="mt-1">
                                    <x-ui.badge variant="success">{{ $application_status }}</x-ui.badge>
                                </div>
                            </div>
                        </div>
                    @else
                        <form wire:submit.prevent="save" class="space-y-4">
                            <div>
                                <x-ui.label for="company_name">Company Name *</x-ui.label>
                                <x-ui.input id="company_name" wire:model="company_name" placeholder="e.g. Pembekalan Maju Sdn Bhd" />
                                @error('company_name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <x-ui.label for="ssm_number">SSM Registration Number</x-ui.label>
                                <x-ui.input id="ssm_number" wire:model="ssm_number" placeholder="e.g. 202401019876" />
                                @error('ssm_number') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <x-ui.label for="mobile_no">Mobile No.</x-ui.label>
                                    <x-ui.input id="mobile_no" wire:model="mobile_no" placeholder="012-3456789" />
                                </div>
                                <div>
                                    <x-ui.label for="telephone_no">Office Telephone</x-ui.label>
                                    <x-ui.input id="telephone_no" wire:model="telephone_no" placeholder="03-88889999" />
                                </div>
                            </div>

                            <div>
                                <x-ui.label for="mof_registration_no">MOF Registration No.</x-ui.label>
                                <x-ui.input id="mof_registration_no" wire:model="mof_registration_no" placeholder="MOF-357-123456" />
                            </div>

                            <div>
                                <x-ui.label for="paid_up_capital">Paid-Up Capital (MYR)</x-ui.label>
                                <x-ui.input id="paid_up_capital" type="number" step="0.01" wire:model="paid_up_capital" placeholder="50000.00" />
                            </div>

                            <div>
                                <x-ui.label for="application_status">Application Status</x-ui.label>
                                <select id="application_status" wire:model="application_status" class="w-full px-3 py-2 text-sm bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/80 rounded-xl text-zinc-900 dark:text-zinc-100">
                                    <option value="APPROVED">APPROVED</option>
                                    <option value="PENDING">PENDING</option>
                                    <option value="REJECTED">REJECTED</option>
                                </select>
                            </div>
                        </form>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="p-6 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 flex justify-end gap-3">
                    @if($mode === 'view')
                        <x-ui.button variant="outline" size="sm" wire:click="closePanel">Close</x-ui.button>
                        <x-ui.button variant="primary" size="sm" wire:click="switchToEdit">Edit Supplier</x-ui.button>
                    @else
                        <x-ui.button variant="outline" size="sm" wire:click="closePanel">Cancel</x-ui.button>
                        <x-ui.button variant="primary" size="sm" wire:click="save">Save Supplier</x-ui.button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
