<?php

use Livewire\Component;

new class extends Component
{
    public string $search = '';
    public string $selectedCategory = 'all';
    public string $selectedStatus = 'all';

    public function getAdvertisementsProperty(): array
    {
        $all = [
            [
                'id' => 1,
                'title' => 'Supply and Delivery of High-Performance Laptops for Secondary Schools',
                'ref_no' => 'KPM/2026/QT/089',
                'agency' => 'Ministry of Education',
                'category' => 'Supplies',
                'status' => 'Open',
                'published_date' => '2026-07-15',
                'closing_date' => '2026-08-10',
                'estimated_value' => 'RM 450,000.00',
            ],
            [
                'id' => 2,
                'title' => 'Development and Deployment of Cloud-Based Procurement Database System',
                'ref_no' => 'KKM/2026/T/104',
                'agency' => 'Ministry of Health',
                'category' => 'Services',
                'status' => 'Open',
                'published_date' => '2026-07-17',
                'closing_date' => '2026-08-20',
                'estimated_value' => 'RM 1,200,000.00',
            ],
            [
                'id' => 3,
                'title' => 'Maintenance and Upgrading of Main Server Infrastructure at Federal Headquarters',
                'ref_no' => 'MAMPU/2026/QT/012',
                'agency' => 'MAMPU (JPM)',
                'category' => 'Services',
                'status' => 'Closing Soon',
                'published_date' => '2026-07-05',
                'closing_date' => '2026-07-25',
                'estimated_value' => 'RM 180,000.00',
            ],
            [
                'id' => 4,
                'title' => 'Civil Works and Refurbishment of Block C Research Laboratories',
                'ref_no' => 'KKR/2026/T/055',
                'agency' => 'Ministry of Works',
                'category' => 'Works',
                'status' => 'Open',
                'published_date' => '2026-07-10',
                'closing_date' => '2026-08-05',
                'estimated_value' => 'RM 3,500,000.00',
            ],
            [
                'id' => 5,
                'title' => 'Supply, Delivery, and Commissioning of Medical Imaging Scanners',
                'ref_no' => 'KKM/2026/T/211',
                'agency' => 'Ministry of Health',
                'category' => 'Supplies',
                'status' => 'Open',
                'published_date' => '2026-07-18',
                'closing_date' => '2026-09-01',
                'estimated_value' => 'RM 2,800,000.00',
            ]
        ];

        return array_filter($all, function ($ad) {
            // Search filter
            if ($this->search !== '') {
                $query = strtolower($this->search);
                $matchesTitle = str_contains(strtolower($ad['title']), $query);
                $matchesRef = str_contains(strtolower($ad['ref_no']), $query);
                $matchesAgency = str_contains(strtolower($ad['agency']), $query);
                
                if (!$matchesTitle && !$matchesRef && !$matchesAgency) {
                    return false;
                }
            }

            // Category filter
            if ($this->selectedCategory !== 'all' && $ad['category'] !== $this->selectedCategory) {
                return false;
            }

            // Status filter
            if ($this->selectedStatus !== 'all' && $ad['status'] !== $this->selectedStatus) {
                return false;
            }

            return true;
        });
    }
};
?>

<div class="space-y-8">
    <!-- Top Portal Stats Dashboard -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Stat card 1: Active Tenders -->
        <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl flex items-center gap-4 shadow-xs">
            <div class="p-3 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-xl">
                <x-heroicon-o-clipboard-document-list class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
            </div>
            <div>
                <p class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Active Tenders</p>
                <h4 class="text-2xl font-black text-zinc-950 dark:text-white mt-0.5">{{ count($this->advertisements) }} Open</h4>
            </div>
        </div>

        <!-- Stat card 2: Total Est. Value -->
        <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl flex items-center gap-4 shadow-xs">
            <div class="p-3 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-xl">
                <x-heroicon-o-currency-dollar class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
            </div>
            <div>
                <p class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Est. Budget Pool</p>
                <h4 class="text-2xl font-black text-zinc-950 dark:text-white mt-0.5">RM 8.13M</h4>
            </div>
        </div>

        <!-- Stat card 3: Approaching Deadlines -->
        <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl flex items-center gap-4 shadow-xs">
            <div class="p-3 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-xl">
                <x-heroicon-o-clock class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
            </div>
            <div>
                <p class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Closing Soon</p>
                <h4 class="text-2xl font-black text-zinc-950 dark:text-white mt-0.5">1 Alert</h4>
            </div>
        </div>
    </div>

    <!-- Main split portal layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left Filter Panel -->
        <div class="lg:col-span-4 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl p-6 shadow-xs space-y-6 lg:sticky lg:top-24">
            <div>
                <h4 class="text-sm font-bold text-zinc-950 dark:text-white uppercase tracking-wider border-b border-zinc-100 dark:border-zinc-800/60 pb-3">Search & Filters</h4>
            </div>

            <!-- Search input -->
            <div class="space-y-1.5">
                <x-label for="search_filter">Keywords</x-label>
                <x-input wire:model.live.debounce.250ms="search" id="search_filter" type="text" placeholder="Search title, ref, agency...">
                    <x-slot:icon>
                        <x-heroicon-o-magnifying-glass class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </x-slot:icon>
                </x-input>
            </div>

            <!-- Category Radio Group Cards -->
            <div class="space-y-3">
                <x-label>Category</x-label>
                <div class="grid grid-cols-1 gap-2">
                    @foreach(['all' => 'All Categories', 'Supplies' => 'Supplies & Materials', 'Services' => 'Services & Consulting', 'Works' => 'Civil Works & Construction'] as $val => $label)
                        <label class="relative flex items-center justify-between p-3 border rounded-xl cursor-pointer select-none transition-all duration-150
                            {{ $selectedCategory === $val ? 'border-emerald-500 bg-emerald-50/20 dark:bg-emerald-950/10' : 'border-zinc-200/80 dark:border-zinc-800/85 hover:border-zinc-300 dark:hover:border-zinc-700 bg-white/50 dark:bg-zinc-900/50' }}">
                            <div class="flex items-center gap-3">
                                <input type="radio" wire:model.live="selectedCategory" name="selectedCategory" value="{{ $val }}" class="w-4 h-4 text-emerald-600 border-zinc-300 focus:ring-emerald-500 bg-white dark:bg-zinc-900">
                                <span class="text-xs font-semibold text-zinc-900 dark:text-zinc-100">{{ $label }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Status Checkboxes / Buttons -->
            <div class="space-y-3">
                <x-label>Tender Status</x-label>
                <div class="grid grid-cols-1 gap-2">
                    @foreach(['all' => 'All Opportunities', 'Open' => 'Open Tenders Only', 'Closing Soon' => 'Closing Soon Only'] as $val => $label)
                        <label class="relative flex items-center justify-between p-3 border rounded-xl cursor-pointer select-none transition-all duration-150
                            {{ $selectedStatus === $val ? 'border-emerald-500 bg-emerald-50/20 dark:bg-emerald-950/10' : 'border-zinc-200/80 dark:border-zinc-800/85 hover:border-zinc-300 dark:hover:border-zinc-700 bg-white/50 dark:bg-zinc-900/50' }}">
                            <div class="flex items-center gap-3">
                                <input type="radio" wire:model.live="selectedStatus" name="selectedStatus" value="{{ $val }}" class="w-4 h-4 text-emerald-600 border-zinc-300 focus:ring-emerald-500 bg-white dark:bg-zinc-900">
                                <span class="text-xs font-semibold text-zinc-900 dark:text-zinc-100">{{ $label }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Reset filters -->
            @if ($search !== '' || $selectedCategory !== 'all' || $selectedStatus !== 'all')
                <button wire:click="$set('search', ''); $set('selectedCategory', 'all'); $set('selectedStatus', 'all')" type="button" class="w-full text-center py-2 border border-zinc-200 dark:border-zinc-800 text-zinc-600 dark:text-zinc-400 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-zinc-850 rounded-xl text-xs font-bold transition-all">
                    Reset Active Filters
                </button>
            @endif
        </div>

        <!-- Right Listings Grid -->
        <div class="lg:col-span-8 space-y-4">
            <div class="flex items-center justify-between px-1">
                <span class="text-xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Tender Listings ({{ count($this->advertisements) }} found)</span>
            </div>

            @if (count($this->advertisements) > 0)
                <div class="grid grid-cols-1 gap-4">
                    @foreach ($this->advertisements as $ad)
                        <div class="bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl p-6 transition-all duration-200 hover:border-emerald-500/50 dark:hover:border-emerald-500/50 hover:shadow-lg shadow-emerald-500/5 flex flex-col justify-between gap-5 relative overflow-hidden group">
                            
                            <!-- Bidding open pulse dot at top corner -->
                            @if ($ad['status'] === 'Open')
                                <div class="absolute top-4 right-4 flex items-center gap-1.5 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200/60 dark:border-emerald-800/40 px-2 py-0.5 rounded-full">
                                    <span class="relative flex h-1.5 w-1.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                                    </span>
                                    <span class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Active</span>
                                </div>
                            @else
                                <div class="absolute top-4 right-4 flex items-center gap-1.5 bg-amber-50 dark:bg-amber-950/20 border border-amber-200/60 dark:border-amber-800/40 px-2 py-0.5 rounded-full">
                                    <span class="relative flex h-1.5 w-1.5">
                                        <span class="animate-pulse absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-amber-500"></span>
                                    </span>
                                    <span class="text-[9px] font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">Closing</span>
                                </div>
                            @endif

                            <div class="space-y-4">
                                <!-- Categories & reference code -->
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                                        {{ $ad['category'] }}
                                    </span>
                                    <span class="text-xs text-zinc-400 dark:text-zinc-500 font-mono tracking-tight">
                                        {{ $ad['ref_no'] }}
                                    </span>
                                </div>

                                <!-- Title and agency -->
                                <div class="space-y-1">
                                    <h4 class="text-lg font-bold text-zinc-950 dark:text-white leading-tight group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                        {{ $ad['title'] }}
                                    </h4>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">
                                        {{ $ad['agency'] }}
                                    </p>
                                </div>

                                <!-- Timeline Progress Meter -->
                                <div class="space-y-1.5 pt-2">
                                    <div class="flex items-center justify-between text-[10px] font-semibold">
                                        <span class="text-zinc-400">Tender Period</span>
                                        <span class="{{ $ad['status'] === 'Closing Soon' ? 'text-amber-600' : 'text-emerald-600' }}">
                                            {{ $ad['status'] === 'Closing Soon' ? '7 days remaining' : '23 days remaining' }}
                                        </span>
                                    </div>
                                    <div class="w-full h-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-300 {{ $ad['status'] === 'Closing Soon' ? 'bg-amber-500 w-[85%]' : 'bg-emerald-500 w-[35%]' }}"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Section -->
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between border-t border-zinc-100 dark:border-zinc-800/80 pt-4 gap-4">
                                <div class="flex items-center gap-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                                    <x-heroicon-o-currency-dollar class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                    <span>Estimated value: <strong class="text-zinc-950 dark:text-white">{{ $ad['estimated_value'] }}</strong></span>
                                </div>

                                <div class="flex items-center gap-3">
                                    <a href="/login" class="flex-1 sm:flex-none text-center px-4 py-2 border border-zinc-200 dark:border-zinc-800 bg-white/60 dark:bg-zinc-900/60 hover:bg-zinc-50 dark:hover:bg-zinc-850 text-zinc-700 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white rounded-xl text-xs font-bold transition-all">
                                        View Details
                                    </a>
                                    <a href="/register" class="flex-1 sm:flex-none text-center px-4 py-2 bg-emerald-600 text-white font-bold hover:bg-emerald-500 rounded-xl text-xs transition-all shadow-md shadow-emerald-600/10">
                                        Register to Bid &rarr;
                                    </a>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800/80 rounded-2xl space-y-4 shadow-xs">
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-850 text-zinc-400 dark:text-zinc-500 rounded-full w-12 h-12 flex items-center justify-center mx-auto">
                        <x-heroicon-o-magnifying-glass class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-sm font-bold text-zinc-900 dark:text-white">No Tenders Found</h4>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 max-w-xs mx-auto">We couldn't find any active tenders matching your search criteria. Try modifying your filters.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
