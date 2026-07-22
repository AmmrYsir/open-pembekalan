<?php

use Livewire\Component;

new class extends Component
{
    public string $advertisementId = '1';
    public string $activeTab = 'details'; // 'details' | 'mof-codes' | 'documents' | 'briefing' | 'submissions' | 'preview'
    public bool $expandFullTitle = false;

    // Modals
    public bool $showCorrigendumModal = false;
    public bool $showCancellationModal = false;

    // Form inputs for Corrigendum
    public string $corrigendumReason = '';
    public string $corrigendumDetails = '';

    // Form inputs for Cancellation
    public string $cancelReason = '';
    public string $cancelRef = '';

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
        'status' => 'published', // 'published' | 'corrigendum_issued' | 'cancelled' | 'draft' | 'closed'
        'cidb_code' => 'Not Applicable',
        'kbp_required' => true,
        'briefing_required' => true,
        'briefing_at' => '2026-08-05 10:00 AM',
        'briefing_venue' => 'Main Conference Room, Level 4, Perak State Sports Council Complex',
        'officer_name' => 'Ammar Yasir (Procurement Officer)',
        'officer_phone' => '05-254 1928 / Ext 104',
        'officer_email' => 'procurement@msnperak.gov.my',
        'cancellation_reason' => null,
        'cancellation_ref' => null,
        'cancelled_at' => null,
    ];

    // Mock Corrigendums / Addendums Issued History
    public array $corrigendums = [
        [
            'id' => 'corr_1',
            'code' => 'CORR-01/ADV-2026-001',
            'issued_at' => '2026-08-04 02:15 PM',
            'reason' => 'Closing date extension & briefing venue update due to administrative requests.',
            'details' => 'Tender closing date extended from 14 Aug to 21 Aug 2026. Briefing venue moved to Level 4 Conference Room.',
            'issued_by' => 'Ammar Yasir (Procurement Officer)',
        ],
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
            'filename' => 'Corrigendum_Notice_No_1.pdf',
            'filesize' => '1.1 MB',
            'type' => 'Corrigendum PDF',
            'downloads' => 19,
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
    ];

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function openCorrigendumModal(): void
    {
        $this->reset(['corrigendumReason', 'corrigendumDetails']);
        $this->showCorrigendumModal = true;
    }

    public function issueCorrigendum(): void
    {
        if (trim($this->corrigendumReason) === '') {
            $this->addError('corrigendumReason', 'Reason for corrigendum is required.');
            return;
        }

        $count = count($this->corrigendums) + 1;
        $this->corrigendums[] = [
            'id' => 'corr_' . $count,
            'code' => 'CORR-0' . $count . '/' . $this->ad['code'],
            'issued_at' => date('Y-m-d h:i A'),
            'reason' => $this->corrigendumReason,
            'details' => $this->corrigendumDetails ?: $this->corrigendumReason,
            'issued_by' => 'Ammar Yasir (Procurement Officer)',
        ];

        $this->ad['status'] = 'corrigendum_issued';
        $this->showCorrigendumModal = false;
        session()->flash('success', 'Official Corrigendum / Addendum notice successfully published to suppliers.');
    }

    public function openCancellationModal(): void
    {
        $this->reset(['cancelReason', 'cancelRef']);
        $this->cancelRef = 'REF-CANCEL-' . rand(1000, 9999);
        $this->showCancellationModal = true;
    }

    public function cancelAdvertisement(): void
    {
        if (trim($this->cancelReason) === '') {
            $this->addError('cancelReason', 'Cancellation reason is required.');
            return;
        }

        $this->ad['status'] = 'cancelled';
        $this->ad['cancellation_reason'] = $this->cancelReason;
        $this->ad['cancellation_ref'] = $this->cancelRef;
        $this->ad['cancelled_at'] = date('Y-m-d h:i A');

        $this->showCancellationModal = false;
        session()->flash('success', 'Advertisement notice has been officially CANCELLED.');
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

    {{-- CANCELLED NOTICE BANNER IF CANCELLED --}}
    @if($ad['status'] === 'cancelled')
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/50 text-rose-800 dark:text-rose-300 space-y-2">
            <div class="flex items-center gap-2 font-bold text-sm">
                <x-heroicon-o-x-circle class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                <span>THIS ADVERTISEMENT NOTICE HAS BEEN OFFICIALLY CANCELLED</span>
            </div>
            <p class="text-xs text-rose-700 dark:text-rose-300">
                <strong>Reason:</strong> {{ $ad['cancellation_reason'] }} | <strong>Approval Ref:</strong> {{ $ad['cancellation_ref'] }} | <strong>Date Cancelled:</strong> {{ $ad['cancelled_at'] }}
            </p>
        </div>
    @endif

    {{-- CORRIGENDUM ISSUED BANNER IF CORRIGENDUM ACTIVE --}}
    @if($ad['status'] === 'corrigendum_issued')
        <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/50 text-amber-800 dark:text-amber-300 space-y-2">
            <div class="flex items-center gap-2 font-bold text-sm">
                <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-amber-600 dark:text-amber-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                <span>OFFICIAL CORRIGENDUM / ADDENDUM NOTICE ISSUED</span>
            </div>
            <p class="text-xs text-amber-700 dark:text-amber-300">
                One or more official addendum notices have been published for this tender advertisement. Please refer to the Corrigendum Log below.
            </p>
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
                    @elseif($ad['status'] === 'corrigendum_issued')
                        <x-badge variant="warning" pill>Corrigendum Issued</x-badge>
                    @elseif($ad['status'] === 'cancelled')
                        <x-badge variant="danger" pill>Cancelled Notice</x-badge>
                    @else
                        <x-badge variant="secondary" pill>Draft Notice</x-badge>
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

            {{-- Right Compact Action Toolbar with Workflow Buttons --}}
            <div class="flex items-center gap-2 shrink-0 self-start pt-1 lg:pt-0 flex-wrap">
                @if($ad['status'] !== 'cancelled')
                    {{-- Issue Corrigendum Button --}}
                    <button
                        wire:click="openCorrigendumModal"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 hover:bg-amber-100 transition-colors border border-amber-200 dark:border-amber-800/40 cursor-pointer"
                    >
                        <x-heroicon-o-pencil-square class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        Issue Corrigendum / Addendum
                    </button>

                    {{-- Cancel Advertisement Button --}}
                    <button
                        wire:click="openCancellationModal"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 hover:bg-rose-100 transition-colors border border-rose-200 dark:border-rose-800/40 cursor-pointer"
                    >
                        <x-heroicon-o-x-circle class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        Cancel Advertisement
                    </button>
                @endif

                <a href="{{ route('advertisement') }}">
                    <x-button variant="secondary" size="sm" class="cursor-pointer">
                        <x-heroicon-o-arrow-left class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        Back
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

    {{-- TAB 1: ADVERTISEMENT DETAILS & CORRIGENDUM HISTORY LOG --}}
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

                {{-- CORRIGENDUM / ADDENDUM HISTORY AUDIT LOG CARD --}}
                @if(count($corrigendums) > 0)
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                            <span class="w-5 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                            Official Corrigendum / Addendum History Log
                            <span class="flex-1 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                        </h3>

                        <div class="space-y-3">
                            @foreach($corrigendums as $corr)
                                <div class="p-4 rounded-xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/40 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="font-mono text-xs font-bold text-amber-800 dark:text-amber-300">
                                            {{ $corr['code'] }}
                                        </span>
                                        <span class="text-xs text-zinc-400 font-mono">Issued: {{ $corr['issued_at'] }}</span>
                                    </div>
                                    <h4 class="text-xs font-bold text-zinc-900 dark:text-zinc-100">{{ $corr['reason'] }}</h4>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ $corr['details'] }}</p>
                                    <div class="text-[11px] text-zinc-400 font-mono pt-1 border-t border-amber-200/50">
                                        Issued by: {{ $corr['issued_by'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

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

                    <x-button variant="primary" size="sm" onclick="alert('Simulation: Add MOF Code');" class="cursor-pointer">
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

                    <x-button variant="primary" size="sm" onclick="alert('Simulation: Upload new document');" class="cursor-pointer">
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

    {{-- TAB 6: SUPPLIER PUBLIC PORTAL PREVIEW SIMULATION WITH CORRIGENDUM & CANCELLATION BANNERS --}}
    @if($activeTab === 'preview')
        <div class="p-6 rounded-2xl bg-zinc-900 text-white shadow-2xl border border-zinc-800 space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-zinc-800">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                    <span class="font-mono text-xs uppercase text-emerald-400 font-bold">Supplier Public Portal View Simulation</span>
                </div>
                <span class="text-xs text-zinc-400 font-mono">https://openpembekalan.gov.my/tender/{{ $ad['code'] }}</span>
            </div>

            {{-- PUBLIC PORTAL CANCELLATION BANNER --}}
            @if($ad['status'] === 'cancelled')
                <div class="p-4 rounded-xl bg-rose-500/20 border border-rose-500/40 text-rose-300 space-y-1">
                    <div class="flex items-center gap-2 font-bold text-sm text-rose-400">
                        <x-heroicon-o-x-circle class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        NOTICE: THIS TENDER ADVERTISEMENT HAS BEEN CANCELLED
                    </div>
                    <p class="text-xs text-zinc-300">
                        Document purchase and bid submissions are closed. Reason: {{ $ad['cancellation_reason'] }}
                    </p>
                </div>
            @endif

            {{-- PUBLIC PORTAL CORRIGENDUM BANNER --}}
            @if($ad['status'] === 'corrigendum_issued')
                <div class="p-4 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-300 space-y-2">
                    <div class="flex items-center gap-2 font-bold text-sm text-amber-400">
                        <x-heroicon-o-exclamation-triangle class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        NOTICE: OFFICIAL CORRIGENDUM / ADDENDUM PUBLISHED
                    </div>
                    <p class="text-xs text-zinc-300">
                        Suppliers are required to review the latest addendum document before submitting their proposals.
                    </p>
                </div>
            @endif

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
                    @if($ad['status'] === 'cancelled')
                        <button disabled class="px-6 py-2.5 rounded-xl bg-zinc-800 text-zinc-500 font-bold text-sm cursor-not-allowed border border-zinc-700">
                            Bidding Closed (Cancelled)
                        </button>
                    @else
                        <button onclick="alert('Simulation: Supplier clicks Purchase Document');" class="px-6 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-bold text-sm cursor-pointer shadow-lg shadow-emerald-500/20">
                            Purchase Document & Bid Now →
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ISSUE CORRIGENDUM / ADDENDUM MODAL --}}
    @if($showCorrigendumModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div wire:click="$set('showCorrigendumModal', false)" class="fixed inset-0 bg-zinc-950/60 backdrop-blur-xs transition-opacity"></div>

                <div class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-zinc-900 p-6 shadow-2xl border border-zinc-200 dark:border-zinc-800 space-y-5">
                    <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="p-2 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 border border-amber-200">
                                <x-heroicon-o-pencil-square class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            </span>
                            <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Issue Corrigendum / Addendum Notice</h3>
                        </div>
                        <button wire:click="$set('showCorrigendumModal', false)" class="text-zinc-400 hover:text-zinc-600">
                            <x-heroicon-o-x-mark class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        </button>
                    </div>

                    <div class="space-y-4 text-sm">
                        <div>
                            <x-label for="corrigendumReason" :required="true">Reason for Corrigendum / Amendment</x-label>
                            <input id="corrigendumReason" type="text" wire:model="corrigendumReason" placeholder="e.g. Closing date extension & technical specification amendment" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100">
                            @error('corrigendumReason') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <x-label for="corrigendumDetails">Amendment Details & Supplier Instructions</x-label>
                            <textarea id="corrigendumDetails" wire:model="corrigendumDetails" rows="4" placeholder="Describe the specific changes made to the tender advertisement..." class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100"></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                        <x-button variant="outline" size="sm" wire:click="$set('showCorrigendumModal', false)">Cancel</x-button>
                        <x-button variant="primary" size="sm" wire:click="issueCorrigendum">Publish Corrigendum</x-button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- CANCEL ADVERTISEMENT MODAL --}}
    @if($showCancellationModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div wire:click="$set('showCancellationModal', false)" class="fixed inset-0 bg-zinc-950/60 backdrop-blur-xs transition-opacity"></div>

                <div class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-zinc-900 p-6 shadow-2xl border border-zinc-200 dark:border-zinc-800 space-y-5">
                    <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="p-2 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 border border-rose-200">
                                <x-heroicon-o-x-circle class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            </span>
                            <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Cancel Active Advertisement Notice</h3>
                        </div>
                        <button wire:click="$set('showCancellationModal', false)" class="text-zinc-400 hover:text-zinc-600">
                            <x-heroicon-o-x-mark class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        </button>
                    </div>

                    <div class="space-y-4 text-sm">
                        <div>
                            <x-label for="cancelRef" :required="true">Official Approval Reference Number</x-label>
                            <input id="cancelRef" type="text" wire:model="cancelRef" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100 font-mono font-bold">
                        </div>

                        <div>
                            <x-label for="cancelReason" :required="true">Official Reason for Cancellation</x-label>
                            <textarea id="cancelReason" wire:model="cancelReason" rows="3" placeholder="Provide reason for cancelling this advertisement notice..." class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100"></textarea>
                            @error('cancelReason') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                        <x-button variant="outline" size="sm" wire:click="$set('showCancellationModal', false)">Cancel</x-button>
                        <x-button variant="danger" size="sm" wire:click="cancelAdvertisement">Confirm Cancellation</x-button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
