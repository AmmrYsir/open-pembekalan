<?php

use App\Models\Supplier;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public string $mode = 'view';
    public bool $showPanel = false;
    public ?int $activeId = null;

    // View details
    public string $company_name = '';
    public string $ssm_number = '';
    public string $mobile_no = '';
    public string $telephone_no = '';
    public string $mof_registration_no = '';
    public string $paid_up_capital = '';
    public string $application_status = 'APPROVED';

    #[On('open-supplier-drawer')]
    public function open(string $mode = 'view', ?int $id = null): void
    {
        $this->activeId = $id;
        $this->mode = 'view';

        if ($id) {
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
                        <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">Supplier Profile Details</h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">View-only supplier registration information.</p>
                    </div>
                    <button wire:click="closePanel" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                        <x-heroicon-o-x-mark class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </button>
                </div>

                {{-- Content --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-4">
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
                </div>

                {{-- Footer --}}
                <div class="p-6 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 flex justify-end gap-3">
                    <x-ui.button variant="outline" size="sm" wire:click="closePanel">Close</x-ui.button>
                </div>
            </div>
        </div>
    </div>
</div>
