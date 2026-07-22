<?php

use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component
{
    public string $search = '';
    public string $selectedCategory = 'all';
    public string $selectedStatus = 'all';

    public array $advertisements = [
        [
            'id' => '1',
            'code' => 'ADV-2026-001',
            'title' => 'TENDER FOR SECURITY GUARD SERVICES (WITHOUT FIREARMS) FOR A PERIOD OF TWO (2) YEARS AT SELADANG CAGE, PERAK STATE SPORTS COUNCIL',
            'category' => 'Services',
            'type' => 'Quotation',
            'agency' => 'Perak State Sports Council',
            'document_fee' => 50.00,
            'publish_at' => '2026-08-01 09:00 AM',
            'closing_at' => '2026-08-21 12:00 PM',
            'days_left' => 12,
            'status' => 'published',
            'purchased_count' => 18,
            'submitted_count' => 7,
            'briefing_required' => true,
        ],
        [
            'id' => '2',
            'code' => 'ADV-2026-002',
            'title' => 'SUPPLY AND INSTALLATION OF HIGH-PERFORMANCE ENTERPRISE SERVERS AND SOFTWARE LICENSING FOR PERAK IT DEPARTMENT',
            'category' => 'Supply',
            'type' => 'Open Tender',
            'agency' => 'Perak Science Technology & ICT Department',
            'document_fee' => 100.00,
            'publish_at' => '2026-07-15 09:00 AM',
            'closing_at' => '2026-07-30 12:00 PM',
            'days_left' => 3,
            'status' => 'closing_soon',
            'purchased_count' => 32,
            'submitted_count' => 14,
            'briefing_required' => false,
        ],
        [
            'id' => '3',
            'code' => 'ADV-2026-003',
            'title' => 'UPGRADING WORKS FOR MAIN CONFERENCE HALL AND ASSOCIATED ELECTRICAL INSTALLATIONS ON LOT 4021 BUKIT GANTANG',
            'category' => 'Works',
            'type' => 'Open Tender',
            'agency' => 'Public Works Department Perak',
            'document_fee' => 150.00,
            'publish_at' => '2026-08-05 09:00 AM',
            'closing_at' => '2026-08-28 12:00 PM',
            'days_left' => 18,
            'status' => 'published',
            'purchased_count' => 9,
            'submitted_count' => 2,
            'briefing_required' => true,
        ],
        [
            'id' => '4',
            'code' => 'ADV-2026-004',
            'title' => 'CONSULTANCY SERVICES FOR FEASIBILITY STUDY AND ARCHITECTURAL DESIGN OF TAIPING COMMUNITY SPORTS COMPLEX',
            'category' => 'Consultant',
            'type' => 'Request for Proposal',
            'agency' => 'Taiping Municipal Council',
            'document_fee' => 50.00,
            'publish_at' => '2026-06-01 09:00 AM',
            'closing_at' => '2026-06-25 12:00 PM',
            'days_left' => 0,
            'status' => 'closed',
            'purchased_count' => 14,
            'submitted_count' => 6,
            'briefing_required' => false,
        ],
        [
            'id' => '5',
            'code' => 'ADV-2026-005',
            'title' => 'SUPPLY OF OFFICE COMPUTER EQUIPMENT AND ANTIVIRUS SOFTWARE LICENSES FOR YEAR 2026',
            'category' => 'Supply',
            'type' => 'Quotation',
            'agency' => 'Perak State Secretariat Office',
            'document_fee' => 30.00,
            'publish_at' => '2026-08-10 09:00 AM',
            'closing_at' => '2026-08-31 12:00 PM',
            'days_left' => 22,
            'status' => 'draft',
            'purchased_count' => 0,
            'submitted_count' => 0,
            'briefing_required' => false,
        ],
    ];

    #[On('advertisement-saved')]
    public function refreshList(): void
    {
        // Refreshes list view
    }

    public function openDrawer(?string $id = null): void
    {
        $this->dispatch('open-advertisement-drawer', id: $id);
    }

    public function togglePublishStatus(string $id): void
    {
        foreach ($this->advertisements as &$ad) {
            if ($ad['id'] === $id) {
                $ad['status'] = $ad['status'] === 'published' ? 'draft' : 'published';
                session()->flash('success', 'Publication status for notice ' . $ad['code'] . ' has been updated.');
                break;
            }
        }
    }

    public function filteredAdvertisements(): array
    {
        return array_filter($this->advertisements, function($ad) {
            $matchSearch = trim($this->search) === '' || 
                str_contains(strtolower($ad['title']), strtolower($this->search)) ||
                str_contains(strtolower($ad['code']), strtolower($this->search)) ||
                str_contains(strtolower($ad['agency']), strtolower($this->search));

            $matchCategory = $this->selectedCategory === 'all' || strtolower($ad['category']) === strtolower($this->selectedCategory);
            $matchStatus = $this->selectedStatus === 'all' || strtolower($ad['status']) === strtolower($this->selectedStatus);

            return $matchSearch && $matchCategory && $matchStatus;
        });
    }
};
?>

<div class="space-y-6">

    {{-- Flash Notification --}}
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/50 text-emerald-700 dark:text-emerald-400 text-sm font-medium flex items-center gap-2">
            <x-heroicon-o-check-circle class="w-5 h-5 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
            {{ session('success') }}
        </div>
    @endif

    {{-- Header Card --}}
    <x-card class="!p-4 sm:!p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/40">
                        <x-heroicon-o-megaphone class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </span>
                    <h1 class="text-xl font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">
                        Procurement Advertisements & Notices
                    </h1>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                    Management and publishing of official procurement tender notices to registered suppliers.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <x-button variant="primary" size="sm" wire:click="openDrawer()" class="cursor-pointer shadow-xs">
                    <x-heroicon-o-plus class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    Create Advertisement Notice
                </x-button>
            </div>
        </div>
    </x-card>

    {{-- Summary Stat Cards (4 Cards) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Total Ads --}}
        <div class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-zinc-500">
                <span>Total Notices</span>
                <x-heroicon-o-megaphone class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
            </div>
            <div class="text-2xl font-bold font-mono text-zinc-900 dark:text-zinc-100">
                {{ count($advertisements) }} Notices
            </div>
            <p class="text-xs text-zinc-400 font-mono">2026 Cycle</p>
        </div>

        {{-- Card 2: Active Published Notices --}}
        <div class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-zinc-500">
                <span>Active Published Notices</span>
                <x-heroicon-o-check-circle class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
            </div>
            <div class="text-2xl font-bold font-mono text-emerald-600 dark:text-emerald-400">
                {{ count(array_filter($advertisements, fn($a) => in_array($a['status'], ['published', 'closing_soon']))) }} Active
            </div>
            <p class="text-xs text-emerald-600 dark:text-emerald-400 font-mono">Visible on Public Portal</p>
        </div>

        {{-- Card 3: Closing Soon Notices --}}
        <div class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-zinc-500">
                <span>Closing Soon (7 Days)</span>
                <x-heroicon-o-clock class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
            </div>
            <div class="text-2xl font-bold font-mono text-amber-600 dark:text-amber-400">
                {{ count(array_filter($advertisements, fn($a) => $a['status'] === 'closing_soon')) }} Notices
            </div>
            <p class="text-xs text-amber-600 dark:text-amber-400 font-mono">Bidding Box Closing</p>
        </div>

        {{-- Card 4: Purchased Documents & Bids --}}
        <div class="p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs space-y-2">
            <div class="flex items-center justify-between text-xs font-semibold text-zinc-500">
                <span>Documents Purchased</span>
                <x-heroicon-o-document-arrow-down class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
            </div>
            <div class="text-2xl font-bold font-mono text-indigo-600 dark:text-indigo-400">
                73 Purchased
            </div>
            <p class="text-xs text-indigo-600 dark:text-indigo-400 font-mono">29 Bids Submitted</p>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <x-card class="!p-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 text-sm">
            {{-- Search Bar --}}
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by notice title, reference code, or agency..."
                    class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 py-2.5 pl-10 pr-3.5 text-xs focus:outline-none focus:ring-1 focus:ring-emerald-500"
                >
            </div>

            {{-- Category Filter --}}
            <div class="flex items-center gap-3">
                <div class="space-y-1">
                    <select wire:model.live="selectedCategory" class="rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-xs py-2 px-3 focus:outline-none">
                        <option value="all">All Categories</option>
                        <option value="supply">Supply</option>
                        <option value="services">Services</option>
                        <option value="works">Works</option>
                        <option value="consultant">Consultant</option>
                    </select>
                </div>

                {{-- Status Filter --}}
                <div class="space-y-1">
                    <select wire:model.live="selectedStatus" class="rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 text-xs py-2 px-3 focus:outline-none">
                        <option value="all">All Statuses</option>
                        <option value="published">Active / Published</option>
                        <option value="closing_soon">Closing Soon</option>
                        <option value="draft">Draft Notice</option>
                        <option value="closed">Closed Bidding</option>
                    </select>
                </div>
            </div>
        </div>
    </x-card>

    {{-- Main Advertisements Table --}}
    <div class="overflow-x-auto rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs">
        <table class="min-w-full divide-y divide-zinc-100 dark:divide-zinc-800 text-sm">
            <thead class="bg-zinc-50/70 dark:bg-zinc-800/40">
                <tr>
                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Notice Reference</th>
                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Tender Title & Agency</th>
                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Category / Type</th>
                    <th scope="col" class="px-4 py-3.5 text-center text-xs font-semibold text-zinc-500 uppercase tracking-wider">Document Fee</th>
                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Closing Date & Time</th>
                    <th scope="col" class="px-4 py-3.5 text-center text-xs font-semibold text-zinc-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold text-zinc-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse($this->filteredAdvertisements() as $ad)
                    <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/20 transition-colors">
                        {{-- Ref Code --}}
                        <td class="px-4 py-4 whitespace-nowrap font-mono text-xs font-bold text-emerald-600 dark:text-emerald-400">
                            {{ $ad['code'] }}
                        </td>

                        {{-- Title & Agency --}}
                        <td class="px-4 py-4 max-w-lg">
                            <a href="{{ route('advertisement.show', $ad['id']) }}" class="font-bold text-zinc-900 dark:text-zinc-100 text-sm line-clamp-2 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                                {{ $ad['title'] }}
                            </a>
                            <div class="flex items-center gap-3 text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                <span class="flex items-center gap-1">
                                    <x-heroicon-o-building-office-2 class="w-3.5 h-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                    {{ $ad['agency'] }}
                                </span>
                                @if($ad['briefing_required'])
                                    <span class="px-1.5 py-0.5 rounded bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 text-[10px] font-bold border border-amber-200 dark:border-amber-800/40">
                                        Mandatory Briefing
                                    </span>
                                @endif
                            </div>
                        </td>

                        {{-- Category & Type --}}
                        <td class="px-4 py-4 whitespace-nowrap space-y-1">
                            <x-badge variant="primary">{{ $ad['category'] }}</x-badge>
                            <div class="text-xs text-zinc-500 font-mono">{{ $ad['type'] }}</div>
                        </td>

                        {{-- Document Fee --}}
                        <td class="px-4 py-4 whitespace-nowrap text-center font-mono font-bold text-xs text-zinc-900 dark:text-zinc-100">
                            RM {{ number_format($ad['document_fee'], 2) }}
                        </td>

                        {{-- Closing Date & Days Left --}}
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-xs font-mono font-semibold text-zinc-900 dark:text-zinc-100">{{ $ad['closing_at'] }}</div>
                            @if($ad['status'] === 'closed')
                                <div class="text-[11px] font-mono text-zinc-400">Bidding Closed</div>
                            @elseif($ad['days_left'] <= 3)
                                <div class="text-[11px] font-mono font-bold text-rose-500 animate-pulse">{{ $ad['days_left'] }} days left (Closing Soon)</div>
                            @else
                                <div class="text-[11px] font-mono text-emerald-600 dark:text-emerald-400">{{ $ad['days_left'] }} days remaining</div>
                            @endif
                        </td>

                        {{-- Status Badge --}}
                        <td class="px-4 py-4 whitespace-nowrap text-center">
                            @if($ad['status'] === 'published')
                                <x-badge variant="success" pill>Active / Published</x-badge>
                            @elseif($ad['status'] === 'closing_soon')
                                <x-badge variant="warning" pill>Closing Soon</x-badge>
                            @elseif($ad['status'] === 'draft')
                                <x-badge variant="secondary" pill>Draft Notice</x-badge>
                            @elseif($ad['status'] === 'closed')
                                <x-badge variant="danger" pill>Closed</x-badge>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-4 whitespace-nowrap text-right space-x-1">
                            <a
                                href="{{ route('advertisement.show', $ad['id']) }}"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 transition-colors border border-emerald-200 dark:border-emerald-800/40"
                            >
                                <x-heroicon-o-eye class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                View
                            </a>

                            <button
                                wire:click="openDrawer('{{ $ad['id'] }}')"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 transition-colors cursor-pointer"
                            >
                                <x-heroicon-o-pencil class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                Edit
                            </button>

                            <button
                                wire:click="togglePublishStatus('{{ $ad['id'] }}')"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 transition-colors border border-indigo-200 dark:border-indigo-800/40 cursor-pointer"
                                title="Toggle Publication Status"
                            >
                                <x-heroicon-o-arrow-path class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-zinc-400 text-sm">
                            No advertisement notices found matching your filter criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
