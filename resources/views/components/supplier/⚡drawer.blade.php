<?php

use App\Models\Supplier;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public string $mode = 'view';
    public bool $showPanel = false;
    public ?int $activeId = null;

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
                        <span class="shrink-0 w-9 h-9 rounded-xl bg-sky-50 dark:bg-sky-950/40 flex items-center justify-center text-sky-600 dark:text-sky-400">
                            <x-heroicon-o-eye class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        </span>
                        <div class="min-w-0">
                            <h2 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 truncate">
                                {{ $company_name ?: 'Supplier Details' }}
                            </h2>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                Read-only vendor profile details.
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
                    <div class="space-y-7">
                        <div>
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                                <span class="w-5 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                                Company & Registration Details
                                <span class="flex-1 h-px bg-zinc-200 dark:bg-zinc-700"></span>
                            </h3>
                            <dl class="grid grid-cols-2 gap-x-6 gap-y-4">
                                <div class="col-span-2">
                                    <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Company Name</dt>
                                    <dd class="mt-1 text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $company_name ?: '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">SSM Number</dt>
                                    <dd class="mt-1 text-sm font-mono text-zinc-800 dark:text-zinc-200">{{ $ssm_number ?: '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">MOF Reg. No.</dt>
                                    <dd class="mt-1 text-sm font-mono text-zinc-800 dark:text-zinc-200">{{ $mof_registration_no ?: '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Contact Number</dt>
                                    <dd class="mt-1 text-sm text-zinc-800 dark:text-zinc-200 font-mono">{{ $mobile_no ?: ($telephone_no ?: '—') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Paid-Up Capital</dt>
                                    <dd class="mt-1 text-sm font-mono text-zinc-900 dark:text-zinc-100">
                                        {{ $paid_up_capital ? 'RM '.number_format((float)$paid_up_capital, 2) : '—' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Status</dt>
                                    <dd class="mt-1">
                                        <x-badge variant="success" pill>{{ $application_status }}</x-badge>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-zinc-100 dark:border-zinc-800/50 bg-zinc-50/60 dark:bg-zinc-900/60 flex justify-end">
                    <x-button variant="secondary" size="sm" wire:click="closePanel">Close</x-button>
                </div>
            </div>
        </div>
    </div>
</div>
