<?php

use Livewire\Component;

new class extends Component
{
    public string $advertisementId = '1';
    public string $activeTab = 'details'; // 'details' | 'mof-codes' | 'documents' | 'briefing' | 'submissions' | 'preview'
    public bool $expandFullTitle = false;

    // Mock Advertisement Detail State
    public array $ad = [
        'id' => '1',
        'code' => 'ADV-2026-001',
        'title' => 'TENDER FOR SECURITY GUARD SERVICES (WITHOUT FIREARMS) FOR A PERIOD OF TWO (2) YEARS AT SELADANG CAGE, PERAK STATE SPORTS COUNCIL',
        'category' => 'Services',
        'type' => 'Open Quotation',
        'agency' => 'Perak State Sports Council',
        'document_fee' => 50.00,
        'publish_at' => '2026-08-01 09:00 AM',
        'closing_at' => '2026-08-21 12:00 PM',
        'status' => 'published',
        'cidb_code' => 'Not Applicable',
        'kbp_required' => true,
        'briefing_required' => true,
        'briefing_at' => '2026-08-05 10:00 AM',
        'briefing_venue' => 'Main Conference Room, Level 4, Perak State Sports Council Complex',
        'officer_name' => 'Ammar Yasir (Procurement Officer)',
        'officer_phone' => '05-254 1928 / Ext 104',
        'officer_email' => 'procurement@msnperak.gov.my',
    ];

    // Mock Required MOF Codes for this Advertisement
    public array $mofCodes = [
        [
            'id' => 'mof_1',
            'code' => '220801',
            'category' => 'Perkhidmatan (Services)',
            'subcategory' => 'Kawalan Keselamatan (Security Services)',
            'description' => 'Kawalan Keselamatan Tanpa Senjata Api (Unarmed Guarding)',
            'is_mandatory' => true,
        ],
        [
            'id' => 'mof_2',
            'code' => '220802',
            'category' => 'Perkhidmatan (Services)',
            'subcategory' => 'Kawalan Keselamatan (Security Services)',
            'description' => 'Kawalan Keselamatan Bersenjata Api (Armed Guarding & Patrol)',
            'is_mandatory' => false,
        ],
        [
            'id' => 'mof_3',
            'code' => '221501',
            'category' => 'Perkhidmatan (Services)',
            'subcategory' => 'Pembersihan & Sanitasi (Cleaning Services)',
            'description' => 'Pembersihan Bangunan dan Pejabat (Building Housekeeping)',
            'is_mandatory' => false,
        ],
    ];

    // Mock Attached Documents for Supplier Download
    public array $attachedDocuments = [
        [
            'id' => 'doc_1',
            'filename' => 'Official_Tender_Advertisement_Notice.pdf',
            'filesize' => '2.4 MB',
            'type' => 'PDF Notice',
            'downloads' => 38,
        ],
        [
            'id' => 'doc_2',
            'filename' => 'Technical_Specification_Security_Guarding_V1.pdf',
            'filesize' => '4.1 MB',
            'type' => 'PDF Specification',
            'downloads' => 32,
        ],
        [
            'id' => 'doc_3',
            'filename' => 'Bill_of_Quantities_Pricing_Schedule.xlsx',
            'filesize' => '850 KB',
            'type' => 'Excel BOQ Template',
            'downloads' => 29,
        ],
    ];

    // Mock Submissions & Purchases Log
    public array $supplierPurchases = [
        [
            'supplier_name' => 'MEGA SECURITY SERVICES SDN BHD',
            'ssm_no' => '201801029384 (128492-X)',
            'purchased_at' => '2026-08-02 10:15 AM',
            'receipt_no' => 'REC-2026-8819',
            'bid_status' => 'submitted',
            'submitted_at' => '2026-08-14 02:30 PM',
        ],
        [
            'supplier_name' => 'PERAK PROTECTION & SAFETY SDN BHD',
            'ssm_no' => '201901048291 (130192-A)',
            'purchased_at' => '2026-08-03 11:45 AM',
            'receipt_no' => 'REC-2026-8832',
            'bid_status' => 'submitted',
            'submitted_at' => '2026-08-18 09:15 AM',
        ],
        [
            'supplier_name' => 'KINTA GUARDIAN SERVICES ENTERPRISE',
            'ssm_no' => '202003019284 (IP049281-W)',
            'purchased_at' => '2026-08-04 03:20 PM',
            'receipt_no' => 'REC-2026-8850',
            'bid_status' => 'pending',
            'submitted_at' => null,
        ],
    ];

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function toggleMofRequirement(string $id): void
    {
        foreach ($this->mofCodes as &$item) {
            if ($item['id'] === $id) {
                $item['is_mandatory'] = ! $item['is_mandatory'];
                session()->flash('success', 'MOF Code ' . $item['code'] . ' requirement rule updated.');
                break;
            }
        }
    }

    public function togglePublish(): void
    {
        $this->ad['status'] = $this->ad['status'] === 'published' ? 'draft' : 'published';
        session()->flash('success', 'Publication status has been updated.');
    }
};
?>

<div class="space-y-6">

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/50 text-emerald-700 dark:text-emerald-400 text-sm font-medium flex items-center gap-2">
            <x-heroicon-o-check-circle class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
            {{ session('success') }}
        </div>
    @endif

    {{-- COMPACT & CLEAN ADVERTISEMENT HEADER CARD --}}
    <x-card class="!p-4 sm:!p-5">
        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
            
            {{-- Left Content Area --}}
            <div class="space-y-2 min-w-0 flex-1">
                {{-- Compact Top Metadata Badges Strip --}}
                <div class="flex items-center gap-2 flex-wrap text-xs">
                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 font-mono font-bold border border-emerald-200 dark:border-emerald-800/40 shrink-0">
                        {{ $ad['code'] }}
                    </span>

                    <x-badge variant="primary">{{ $ad['category'] }}</x-badge>
                    <x-badge variant="secondary">{{ $ad['type'] }}</x-badge>

                    @if($ad['status'] === 'published')
                        <x-badge variant="success" pill>Active / Published</x-badge>
                    @else
                        <x-badge variant="warning" pill>Draft Notice</x-badge>
                    @endif

                    <span class="px-2.5 py-0.5 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-mono font-bold text-xs ml-auto sm:ml-0">
                        Document Fee: RM {{ number_format($ad['document_fee'], 2) }}
                    </span>
                </div>

                {{-- Project Title with Line Clamp & Expansion Toggle --}}
                <div class="space-y-1">
                    <h1 class="text-base sm:text-lg font-bold text-zinc-900 dark:text-zinc-100 tracking-tight leading-snug {{ $expandFullTitle ? '' : 'line-clamp-2' }}">
                        {{ $ad['title'] }}
                    </h1>

                    @if(strlen($ad['title']) > 80)
                        <button
                            wire:click="$toggle('expandFullTitle')"
                            class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 hover:underline cursor-pointer"
                        >
                            <span>{{ $expandFullTitle ? '▲ Show Less' : '▼ Read Full Project Title' }}</span>
                        </button>
                    @endif
                </div>

                {{-- Agency & Closing Date --}}
                <div class="flex items-center gap-4 text-xs text-zinc-500 dark:text-zinc-400 flex-wrap pt-0.5">
                    <span class="flex items-center gap-1 font-medium">
                        <x-heroicon-o-building-office-2 class="w-3.5 h-3.5 text-zinc-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        {{ $ad['agency'] }}
                    </span>

                    <span class="flex items-center gap-1">
                        <x-heroicon-o-clock class="w-3.5 h-3.5 text-zinc-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        Closing Date: <strong>{{ $ad['closing_at'] }}</strong>
                    </span>
                </div>
            </div>

            {{-- Right Compact Action Toolbar --}}
            <div class="flex items-center gap-2 shrink-0 self-start pt-1 lg:pt-0">
                <x-button variant="primary" size="sm" wire:click="togglePublish" class="cursor-pointer">
                    <x-heroicon-o-arrow-path class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    {{ $ad['status'] === 'published' ? 'Unpublish Notice' : 'Publish Notice' }}
                </x-button>

                <a href="{{ route('advertisement') }}">
                    <x-button variant="secondary" size="sm" class="cursor-pointer">
                        <x-heroicon-o-arrow-left class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        Back to Advertisements
                    </x-button>
                </a>
            </div>
        </div>
    </x-card>

    {{-- ── 6-TAB NAVIGATION BAR ── --}}
    <div class="border-b border-zinc-200 dark:border-zinc-800">
        <nav class="-mb-px flex gap-6 overflow-x-auto" aria-label="Tabs">
            {{-- Tab 1: Details --}}
            <button
                wire:click="setTab('details')"
                class="group inline-flex items-center gap-2 py-3 px-1 border-b-2 text-sm font-medium whitespace-nowrap cursor-pointer transition-colors {{ $activeTab === 'details' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 hover:border-zinc-300' }}"
            >
                <x-heroicon-o-document-text class="w-4 h-4 shrink-0 {{ $activeTab === 'details' ? 'text-emerald-500' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                Advertisement Details
            </button>

            {{-- Tab 2: MOF Code & Eligibility --}}
            <button
                wire:click="setTab('mof-codes')"
                class="group inline-flex items-center gap-2 py-3 px-1 border-b-2 text-sm font-medium whitespace-nowrap cursor-pointer transition-colors {{ $activeTab === 'mof-codes' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 hover:border-zinc-300' }}"
            >
                <x-heroicon-o-tag class="w-4 h-4 shrink-0 {{ $activeTab === 'mof-codes' ? 'text-emerald-500' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                MOF Codes & Eligibility
                <x-badge variant="primary" pill class="ml-1 text-[10px]">{{ count($mofCodes) }}</x-badge>
            </button>

            {{-- Tab 3: Uploaded Documents & Downloads --}}
            <button
                wire:click="setTab('documents')"
                class="group inline-flex items-center gap-2 py-3 px-1 border-b-2 text-sm font-medium whitespace-nowrap cursor-pointer transition-colors {{ $activeTab === 'documents' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 hover:border-zinc-300' }}"
            >
                <x-heroicon-o-paper-clip class="w-4 h-4 shrink-0 {{ $activeTab === 'documents' ? 'text-emerald-500' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                Uploaded Documents & Downloads
            </button>

            {{-- Tab 4: Briefing & Site Visit --}}
            <button
                wire:click="setTab('briefing')"
                class="group inline-flex items-center gap-2 py-3 px-1 border-b-2 text-sm font-medium whitespace-nowrap cursor-pointer transition-colors {{ $activeTab === 'briefing' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 hover:border-zinc-300' }}"
            >
                <x-heroicon-o-user-group class="w-4 h-4 shrink-0 {{ $activeTab === 'briefing' ? 'text-emerald-500' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                Briefing & Site Visit Schedule
            </button>

            {{-- Tab 5: Submissions & Purchases --}}
            <button
                wire:click="setTab('submissions')"
                class="group inline-flex items-center gap-2 py-3 px-1 border-b-2 text-sm font-medium whitespace-nowrap cursor-pointer transition-colors {{ $activeTab === 'submissions' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 hover:border-zinc-300' }}"
            >
                <x-heroicon-o-inbox-stack class="w-4 h-4 shrink-0 {{ $activeTab === 'submissions' ? 'text-emerald-500' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                Submissions & Document Purchases
            </button>

            {{-- Tab 6: Supplier Portal Preview --}}
            <button
                wire:click="setTab('preview')"
                class="group inline-flex items-center gap-2 py-3 px-1 border-b-2 text-sm font-medium whitespace-nowrap cursor-pointer transition-colors {{ $activeTab === 'preview' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 hover:border-zinc-300' }}"
            >
                <x-heroicon-o-eye class="w-4 h-4 shrink-0 {{ $activeTab === 'preview' ? 'text-emerald-500' : 'text-zinc-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                Supplier Public Portal Preview
            </button>
        </nav>
    </div>

    {{-- ── TAB CONTENT ── --}}

    {{-- TAB 1: ADVERTISEMENT DETAILS --}}
    @if($activeTab === 'details')
        <x-card>
            <div class="space-y-8">
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                        <span class="w-5 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                        Tender Notice Overview & Publication Parameters
                        <span class="flex-1 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                    </h3>

                    <dl class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                        <div>
                            <dt class="text-xs text-zinc-400 uppercase font-semibold">Tender Reference Code</dt>
                            <dd class="mt-1 font-mono font-bold text-emerald-600 dark:text-emerald-400">{{ $ad['code'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-zinc-400 uppercase font-semibold">Category & Procurement Type</dt>
                            <dd class="mt-1 font-medium text-zinc-900 dark:text-zinc-100">{{ $ad['category'] }} — {{ $ad['type'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-zinc-400 uppercase font-semibold">Document Fee Price (RM)</dt>
                            <dd class="mt-1 font-mono font-bold text-zinc-900 dark:text-zinc-100">RM {{ number_format($ad['document_fee'], 2) }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs text-zinc-400 uppercase font-semibold">Publication Opening Date</dt>
                            <dd class="mt-1 font-mono text-zinc-900 dark:text-zinc-100">{{ $ad['publish_at'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-zinc-400 uppercase font-semibold">Tender Closing Date & Time</dt>
                            <dd class="mt-1 font-mono font-bold text-rose-600 dark:text-rose-400">{{ $ad['closing_at'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-zinc-400 uppercase font-semibold">Bumiputera Status Requirement</dt>
                            <dd class="mt-1 font-medium text-zinc-900 dark:text-zinc-100">
                                @if($ad['kbp_required'])
                                    <x-badge variant="danger" pill>Mandatory Bumiputera (KBP)</x-badge>
                                @else
                                    <x-badge variant="secondary" pill>Open to All Suppliers</x-badge>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                        <span class="w-5 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                        Officer-in-Charge & Contact Information
                        <span class="flex-1 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                    </h3>

                    <dl class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                        <div>
                            <dt class="text-xs text-zinc-400 uppercase font-semibold">Procurement Officer Name</dt>
                            <dd class="mt-1 font-medium text-zinc-900 dark:text-zinc-100">{{ $ad['officer_name'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-zinc-400 uppercase font-semibold">Office Phone / Extension</dt>
                            <dd class="mt-1 font-mono text-zinc-900 dark:text-zinc-100">{{ $ad['officer_phone'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-zinc-400 uppercase font-semibold">Official Enquiry Email</dt>
                            <dd class="mt-1 font-mono text-emerald-600 dark:text-emerald-400">{{ $ad['officer_email'] }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </x-card>
    @endif

    {{-- TAB 2: MOF CODE & ELIGIBILITY --}}
    @if($activeTab === 'mof-codes')
        <x-card>
            <div class="space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-zinc-100 dark:border-zinc-800">
                    <div>
                        <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                            <x-heroicon-o-tag class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Ministry of Finance (MOF) Required Registration Codes
                        </h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                            Manage MOF codes required for suppliers to purchase and submit bids for this tender advertisement.
                        </p>
                    </div>

                    <x-button variant="primary" size="sm" onclick="alert('Simulasi: Tambah Kod MOF Baru');" class="cursor-pointer">
                        <x-heroicon-o-plus class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        Add MOF Code
                    </x-button>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                    <table class="min-w-full divide-y divide-zinc-100 dark:divide-zinc-800 text-sm">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/40">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">MOF Code</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Category & Subcategory</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Description</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-zinc-500 uppercase">Requirement Rule</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-zinc-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach($mofCodes as $item)
                                <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/20">
                                    <td class="px-4 py-4 whitespace-nowrap font-mono text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                        {{ $item['code'] }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="font-medium text-xs text-zinc-900 dark:text-zinc-100">{{ $item['subcategory'] }}</div>
                                        <div class="text-[11px] text-zinc-400 font-mono">{{ $item['category'] }}</div>
                                    </td>
                                    <td class="px-4 py-4 max-w-sm text-xs text-zinc-700 dark:text-zinc-300">
                                        {{ $item['description'] }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                        @if($item['is_mandatory'])
                                            <x-badge variant="danger" pill>Mandatory Code (Wajib)</x-badge>
                                        @else
                                            <x-badge variant="secondary" pill>Optional Code (Pilihan)</x-badge>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-right space-x-1">
                                        <button
                                            wire:click="toggleMofRequirement('{{ $item['id'] }}')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 transition-colors cursor-pointer"
                                            title="Toggle Mandatory / Optional Rule"
                                        >
                                            <x-heroicon-o-arrow-path class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                            Toggle Rule
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </x-card>
    @endif

    {{-- TAB 3: UPLOADED DOCUMENTS & DOWNLOADS --}}
    @if($activeTab === 'documents')
        <x-card>
            <div class="space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-zinc-100 dark:border-zinc-800">
                    <div>
                        <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                            <x-heroicon-o-paper-clip class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Official Tender Specification Documents
                        </h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                            Attached official documents accessible by registered suppliers after purchasing the tender form.
                        </p>
                    </div>

                    <x-button variant="primary" size="sm" onclick="alert('Simulasi: Muat naik dokumen tender baru');" class="cursor-pointer">
                        <x-heroicon-o-plus class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        Attach Document File
                    </x-button>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                    <table class="min-w-full divide-y divide-zinc-100 dark:divide-zinc-800 text-sm">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/40">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">#</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Document File Name</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Type & Size</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-zinc-500 uppercase">Downloads Count</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-zinc-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach($attachedDocuments as $idx => $doc)
                                <tr>
                                    <td class="px-4 py-3.5 font-mono text-xs font-bold text-zinc-400">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-3.5">
                                        <div class="font-semibold text-zinc-900 dark:text-zinc-100 font-mono text-xs">{{ $doc['filename'] }}</div>
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <x-badge variant="info">{{ $doc['type'] }}</x-badge>
                                        <span class="text-xs font-mono text-zinc-400 ml-1">({{ $doc['filesize'] }})</span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center font-mono font-bold text-emerald-600">
                                        {{ $doc['downloads'] }} downloads
                                    </td>
                                    <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                        <button onclick="alert('Download simulation for {{ $doc['filename'] }}');" class="text-xs font-semibold text-emerald-600 hover:underline cursor-pointer">
                                            Download File
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </x-card>
    @endif

    {{-- TAB 4: BRIEFING & SITE VISIT --}}
    @if($activeTab === 'briefing')
        <x-card>
            <div class="space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-zinc-100 dark:border-zinc-800">
                    <div>
                        <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                            <x-heroicon-o-user-group class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Briefing & Site Visit Schedule Details
                        </h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                            Attendance rules, venue information, and schedule for interested suppliers.
                        </p>
                    </div>

                    <x-badge variant="danger" pill>Mandatory Attendance</x-badge>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div class="p-5 rounded-2xl bg-amber-50/60 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 space-y-3">
                        <div class="flex items-center gap-2 text-amber-800 dark:text-amber-300 font-bold">
                            <x-heroicon-o-clock class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            <span>Briefing Date & Time</span>
                        </div>
                        <div class="text-lg font-mono font-bold text-amber-900 dark:text-amber-200">
                            {{ $ad['briefing_at'] }}
                        </div>
                        <p class="text-xs text-amber-700 dark:text-amber-400">
                            Suppliers who fail to attend this mandatory session will be disqualified from submitting bids.
                        </p>
                    </div>

                    <div class="p-5 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200 dark:border-zinc-800 space-y-3">
                        <div class="flex items-center gap-2 text-zinc-900 dark:text-zinc-100 font-bold">
                            <x-heroicon-o-map-pin class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            <span>Briefing Venue & Location</span>
                        </div>
                        <div class="text-sm font-medium text-zinc-800 dark:text-zinc-200">
                            {{ $ad['briefing_venue'] }}
                        </div>
                        <p class="text-xs text-zinc-400 font-mono">
                            Attendance registration opens 30 minutes prior to session commencement.
                        </p>
                    </div>
                </div>
            </div>
        </x-card>
    @endif

    {{-- TAB 5: SUBMISSIONS & PURCHASES --}}
    @if($activeTab === 'submissions')
        <x-card>
            <div class="space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-zinc-100 dark:border-zinc-800">
                    <div>
                        <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                            <x-heroicon-o-inbox-stack class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Submissions & Document Purchase Audit Log
                        </h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                            Registered suppliers log for purchased forms and submitted tender proposals.
                        </p>
                    </div>

                    <span class="px-3 py-1 rounded-xl bg-emerald-50 text-emerald-700 font-mono text-xs font-bold border border-emerald-200">
                        {{ count($supplierPurchases) }} Suppliers Registered
                    </span>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                    <table class="min-w-full divide-y divide-zinc-100 dark:divide-zinc-800 text-sm">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/40">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">#</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Supplier Name & SSM</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Receipt No.</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 uppercase">Purchase Date</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-zinc-500 uppercase">Submission Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach($supplierPurchases as $idx => $p)
                                <tr>
                                    <td class="px-4 py-3.5 font-mono text-xs font-bold text-zinc-400">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-3.5">
                                        <div class="font-bold text-zinc-900 dark:text-zinc-100 text-xs">{{ $p['supplier_name'] }}</div>
                                        <div class="text-[11px] font-mono text-zinc-400">SSM: {{ $p['ssm_no'] }}</div>
                                    </td>
                                    <td class="px-4 py-3.5 font-mono text-xs text-indigo-600 dark:text-indigo-400 font-bold">
                                        {{ $p['receipt_no'] }}
                                    </td>
                                    <td class="px-4 py-3.5 font-mono text-xs text-zinc-500">
                                        {{ $p['purchased_at'] }}
                                    </td>
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        @if($p['bid_status'] === 'submitted')
                                            <x-badge variant="success" pill>Submitted Bids ({{ $p['submitted_at'] }})</x-badge>
                                        @else
                                            <x-badge variant="warning" pill>Pending Submission</x-badge>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </x-card>
    @endif

    {{-- TAB 6: SUPPLIER PUBLIC PORTAL PREVIEW SIMULATION --}}
    @if($activeTab === 'preview')
        <div class="p-6 rounded-2xl bg-zinc-900 text-white shadow-2xl border border-zinc-800 space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-zinc-800">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                    <span class="font-mono text-xs uppercase text-emerald-400 font-bold">Supplier Public Portal View Simulation</span>
                </div>
                <span class="text-xs text-zinc-400 font-mono">https://openpembekalan.gov.my/tender/{{ $ad['code'] }}</span>
            </div>

            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-400 font-mono text-xs font-bold border border-emerald-500/30">
                        {{ $ad['code'] }}
                    </span>
                    <span class="px-2.5 py-1 rounded-lg bg-zinc-800 text-zinc-300 font-mono text-xs font-semibold">
                        {{ $ad['category'] }}
                    </span>
                </div>

                <h2 class="text-xl font-bold text-white leading-snug">
                    {{ $ad['title'] }}
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs font-mono pt-2">
                    <div class="p-3 rounded-xl bg-zinc-800/80 border border-zinc-700">
                        <span class="text-zinc-400">Document Price</span>
                        <div class="text-base font-bold text-emerald-400 mt-0.5">RM {{ number_format($ad['document_fee'], 2) }}</div>
                    </div>

                    <div class="p-3 rounded-xl bg-zinc-800/80 border border-zinc-700">
                        <span class="text-zinc-400">Closing Date</span>
                        <div class="text-base font-bold text-amber-400 mt-0.5">{{ $ad['closing_at'] }}</div>
                    </div>

                    <div class="p-3 rounded-xl bg-zinc-800/80 border border-zinc-700">
                        <span class="text-zinc-400">Issuing Agency</span>
                        <div class="text-sm font-bold text-white mt-0.5">{{ $ad['agency'] }}</div>
                    </div>
                </div>

                <div class="pt-4 flex items-center gap-4">
                    <button onclick="alert('Simulation: Supplier clicks Purchase Document');" class="px-6 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-bold text-sm cursor-pointer shadow-lg shadow-emerald-500/20">
                        Purchase Document & Bid Now →
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
