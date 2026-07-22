<?php

use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component
{
    public bool $isOpen = false;
    public ?string $adId = null;

    public string $title = '';
    public string $referenceCode = '';
    public string $category = 'Services';
    public float $documentFee = 50.0;
    public string $publishAt = '';
    public string $closingAt = '';
    public string $briefingType = 'mandatory'; // 'mandatory' | 'optional' | 'none'
    public string $briefingAt = '';
    public string $briefingVenue = '';
    public string $status = 'published';

    #[On('open-advertisement-drawer')]
    public function openDrawer(?string $id = null): void
    {
        $this->adId = $id;

        if ($id) {
            $this->title = 'TENDER FOR SECURITY GUARD SERVICES (WITHOUT FIREARMS) FOR A PERIOD OF TWO (2) YEARS AT SELADANG CAGE, PERAK STATE SPORTS COUNCIL';
            $this->referenceCode = 'ADV-2026-00' . $id;
            $this->category = 'Services';
            $this->documentFee = 50.0;
            $this->publishAt = '2026-08-01T09:00';
            $this->closingAt = '2026-08-21T12:00';
            $this->briefingType = 'mandatory';
            $this->briefingAt = '2026-08-05T10:00';
            $this->briefingVenue = 'Main Conference Room, Level 4 / Google Meet';
            $this->status = 'published';
        } else {
            $this->reset(['title', 'referenceCode', 'adId']);
            $this->referenceCode = 'ADV-2026-00' . rand(10, 99);
            $this->category = 'Supply';
            $this->documentFee = 50.0;
            $this->publishAt = date('Y-m-d\TH:i', strtotime('+1 day 09:00'));
            $this->closingAt = date('Y-m-d\TH:i', strtotime('+14 days 12:00'));
            $this->briefingType = 'optional';
            $this->briefingAt = date('Y-m-d\TH:i', strtotime('+3 days 10:00'));
            $this->briefingVenue = 'Main Procurement Hall';
            $this->status = 'draft';
        }

        $this->isOpen = true;
    }

    public function closeDrawer(): void
    {
        $this->isOpen = false;
    }

    public function save(): void
    {
        if (trim($this->title) === '') {
            $this->addError('title', 'Tender notice title is required.');
            return;
        }

        $this->isOpen = false;
        $this->dispatch('advertisement-saved');
        session()->flash('success', $this->adId ? 'Advertisement notice successfully updated.' : 'New procurement advertisement notice successfully published.');
    }
};
?>

<div
    x-data="{ show: @entangle('isOpen') }"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 overflow-hidden"
    role="dialog"
    aria-modal="true"
>
    <div class="absolute inset-0 overflow-hidden">
        {{-- Backdrop --}}
        <div
            x-show="show"
            x-transition:enter="ease-in-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in-out duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            wire:click="closeDrawer"
            class="absolute inset-0 bg-zinc-950/60 backdrop-blur-xs transition-opacity"
        ></div>

        <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
            <div
                x-show="show"
                x-transition:enter="transform transition ease-in-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-300"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="pointer-events-auto w-screen max-w-md"
            >
                <div class="flex h-full flex-col bg-white dark:bg-zinc-900 shadow-2xl border-l border-zinc-200 dark:border-zinc-800">
                    {{-- Header --}}
                    <div class="p-6 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between bg-zinc-50/60 dark:bg-zinc-900/60">
                        <div class="flex items-center gap-2.5">
                            <span class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/40">
                                <x-heroicon-o-megaphone class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            </span>
                            <div>
                                <h2 class="text-base font-bold text-zinc-900 dark:text-zinc-100">
                                    {{ $adId ? 'Edit Advertisement Notice' : 'Create Advertisement Notice' }}
                                </h2>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">Manage procurement tender notice parameters for suppliers</p>
                            </div>
                        </div>

                        <button wire:click="closeDrawer" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                            <x-heroicon-o-x-mark class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        </button>
                    </div>

                    {{-- Form Body --}}
                    <form wire:submit="save" class="flex-1 overflow-y-auto p-6 space-y-5 text-sm">
                        <div>
                            <x-label for="referenceCode" :required="true">Tender Reference Code</x-label>
                            <input id="referenceCode" type="text" wire:model="referenceCode" placeholder="e.g. ADV-2026-001" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100 font-mono font-bold">
                        </div>

                        <div>
                            <x-label for="title" :required="true">Procurement Title</x-label>
                            <textarea id="title" wire:model="title" rows="3" placeholder="Enter full tender notice title..." class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100"></textarea>
                            @error('title') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-label for="category" :required="true">Procurement Category</x-label>
                                <select id="category" wire:model="category" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100">
                                    <option value="Supply">Supply</option>
                                    <option value="Services">Services</option>
                                    <option value="Works">Works</option>
                                    <option value="Consultant">Consultant</option>
                                </select>
                            </div>
                            <div>
                                <x-label for="documentFee" :required="true">Document Price (RM)</x-label>
                                <input id="documentFee" type="number" step="10" wire:model="documentFee" placeholder="50.00" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100 font-mono">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-label for="publishAt" :required="true">Publish Date & Time</x-label>
                                <input id="publishAt" type="datetime-local" wire:model="publishAt" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs p-2 text-zinc-900 dark:text-zinc-100">
                            </div>
                            <div>
                                <x-label for="closingAt" :required="true">Closing Date & Time</x-label>
                                <input id="closingAt" type="datetime-local" wire:model="closingAt" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs p-2 text-zinc-900 dark:text-zinc-100">
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200 dark:border-zinc-800 space-y-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-600 dark:text-zinc-300">Briefing & Site Visit Rules</h4>

                            <div>
                                <x-label for="briefingType">Attendance Requirement</x-label>
                                <select id="briefingType" wire:model.live="briefingType" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs p-2 text-zinc-900 dark:text-zinc-100">
                                    <option value="mandatory">Mandatory Attendance</option>
                                    <option value="optional">Optional Attendance</option>
                                    <option value="none">No Briefing Required</option>
                                </select>
                            </div>

                            @if($briefingType !== 'none')
                                <div class="space-y-3">
                                    <div>
                                        <x-label for="briefingAt">Briefing Date & Time</x-label>
                                        <input id="briefingAt" type="datetime-local" wire:model="briefingAt" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs p-2 text-zinc-900 dark:text-zinc-100">
                                    </div>
                                    <div>
                                        <x-label for="briefingVenue">Venue / Online Link</x-label>
                                        <input id="briefingVenue" type="text" wire:model="briefingVenue" placeholder="Meeting Room / Google Meet Link" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-xs p-2 text-zinc-900 dark:text-zinc-100">
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div>
                            <x-label for="status" :required="true">Publication Status</x-label>
                            <select id="status" wire:model="status" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100">
                                <option value="draft">Draft Notice</option>
                                <option value="published">Published & Active</option>
                                <option value="closed">Closed Bidding</option>
                            </select>
                        </div>
                    </form>

                    {{-- Footer --}}
                    <div class="p-4 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/60 dark:bg-zinc-900/60 flex items-center justify-end gap-3">
                        <x-button variant="outline" size="sm" wire:click="closeDrawer">Cancel</x-button>
                        <x-button variant="primary" size="sm" wire:click="save">Save Notice</x-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
