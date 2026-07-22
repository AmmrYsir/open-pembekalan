<?php

use App\Models\Acquisition;
use App\Models\Agency;
use App\Models\Subagency;
use App\Models\VotType;
use App\Models\AgencyOfficer;
use App\Models\User;
use App\Models\Assignment;
use App\Enums\AcquisitionType;
use App\Enums\AcquisitionMethod;
use App\Enums\AcquisitionCommitteeType;
use App\Livewire\Forms\AcquisitionForm;
use Livewire\Component;
use Livewire\Attributes\Computed;

new class extends Component
{
    public Acquisition $acquisition;
    public AcquisitionForm $form;

    public string $activeTab = 'project-info';
    public bool $isEditing = false;
    public bool $previewSupplierMode = false;

    // Add Item Modal State (Officer)
    public bool $showAddItemModal = false;
    public string $newItemChecklistType = 'technical'; // 'technical' | 'financial'
    public string $newItemTitle = '';
    public string $newItemDesc = '';
    public string $newItemInputType = 'file_upload';
    public string $newItemTemplateFilename = '';
    public bool $newItemIsRequired = true;

    // Supplier Preview Action Modal State
    public bool $showSupplierActionModal = false;
    public ?array $activeSupplierItem = null;
    public string $activeSupplierType = 'technical';
    public string $supplierInputText = '';
    public string $supplierInputNumber = '';
    public bool $supplierInputBoolean = false;
    public string $supplierUploadedFilename = '';

    // Technical Checklist Items
    public array $technicalChecklist = [
        [
            'id' => 'tech_1',
            'title' => 'Borang Spesifikasi Teknikal & Jadual Pematuhan',
            'desc' => 'Sila muat turun templat borang spesifikasi teknikal, isi dan lengkapkan sebelum memuat naik semula.',
            'input_type' => 'file_download_upload',
            'template_filename' => 'Templat_Spesifikasi_Teknikal_V1.pdf',
            'is_required' => true,
            'allowed_extensions' => '.pdf,.docx',
            'status' => 'pending',
            'value' => null,
        ],
        [
            'id' => 'tech_2',
            'title' => 'Katalog Teknis & Brosur Pengeluar',
            'desc' => 'Muat naik katalog teknikal atau brosur rasmi pengeluar bagi setiap barangan/perkhidmatan yang ditawarkan.',
            'input_type' => 'file_upload',
            'template_filename' => null,
            'is_required' => true,
            'allowed_extensions' => '.pdf',
            'status' => 'pending',
            'value' => null,
        ],
        [
            'id' => 'tech_3',
            'title' => 'Sijil Pendaftaran / Perlesenan (SIRIM / Authority)',
            'desc' => 'Muat naik salinan sijil perlesenan dan pendaftaran teknikal yang masih sah.',
            'input_type' => 'file_upload',
            'template_filename' => null,
            'is_required' => true,
            'allowed_extensions' => '.pdf,.jpg,.png',
            'status' => 'pending',
            'value' => null,
        ],
        [
            'id' => 'tech_4',
            'title' => 'Jenama & Model Barangan Ditawarkan',
            'desc' => 'Nyatakan jenama dan model spesifik bagi peralatan/perisian yang ditawarkan.',
            'input_type' => 'text_input',
            'template_filename' => null,
            'is_required' => true,
            'allowed_extensions' => null,
            'status' => 'pending',
            'value' => null,
        ],
        [
            'id' => 'tech_5',
            'title' => 'Tempoh Jaminan (Warranty Period in Months)',
            'desc' => 'Nyatakan tempoh jaminan dalam bilangan bulan (contoh: 36).',
            'input_type' => 'number_input',
            'template_filename' => null,
            'is_required' => true,
            'allowed_extensions' => null,
            'status' => 'pending',
            'value' => null,
        ],
        [
            'id' => 'tech_6',
            'title' => 'Pengesahan Pematuhan Syarat Tempoh Serahan',
            'desc' => 'Sahkan persetujuan membekalkan mengikut tempoh jadual serahan yang ditetapkan.',
            'input_type' => 'boolean',
            'template_filename' => null,
            'is_required' => true,
            'allowed_extensions' => null,
            'status' => 'pending',
            'value' => null,
        ],
    ];

    // Financial Checklist Items
    public array $financialChecklist = [
        [
            'id' => 'fin_1',
            'title' => 'Penyata Bank 3 Bulan Terkini',
            'desc' => 'Muat naik penyata akaun bank syarikat yang telah disahkan bagi 3 bulan terkini.',
            'input_type' => 'file_upload',
            'template_filename' => null,
            'is_required' => true,
            'allowed_extensions' => '.pdf',
            'status' => 'pending',
            'value' => null,
        ],
        [
            'id' => 'fin_2',
            'title' => 'Borang Ringkasan Tawaran Harga (Summary of Price)',
            'desc' => 'Sila muat turun templat Jadual Harga, isi maklumat pecahan harga dan muat naik semula beserta amaun tawaran bersih.',
            'input_type' => 'file_download_upload',
            'template_filename' => 'Templat_Jadual_Harga_Pekeliling.xlsx',
            'is_required' => true,
            'allowed_extensions' => '.xlsx,.pdf',
            'status' => 'pending',
            'value' => null,
        ],
        [
            'id' => 'fin_3',
            'title' => 'Jumlah Amaun Tawaran Harga Keseluruhan (RM)',
            'desc' => 'Masukkan jumlah keseluruhan amaun tawaran harga teknikal & kewangan dalam RM.',
            'input_type' => 'number_input',
            'template_filename' => null,
            'is_required' => true,
            'allowed_extensions' => null,
            'status' => 'pending',
            'value' => null,
        ],
        [
            'id' => 'fin_4',
            'title' => 'Penyata Kewangan Diperiksa (Audited Financial Statement)',
            'desc' => 'Muat naik salinan Penyata Imbangan & Laporan Juruaudit bagi tahun kewangan terakhir.',
            'input_type' => 'file_upload',
            'template_filename' => null,
            'is_required' => false,
            'allowed_extensions' => '.pdf',
            'status' => 'pending',
            'value' => null,
        ],
        [
            'id' => 'fin_5',
            'title' => 'Borang Akuan Deposit & Cukai GST/SST',
            'desc' => 'Muat turun borang akuan pengesahan cukai, tanda tangan dan muat naik semula.',
            'input_type' => 'file_download_upload',
            'template_filename' => 'Borang_Akuan_Cukai_Deposit.pdf',
            'is_required' => true,
            'allowed_extensions' => '.pdf',
            'status' => 'pending',
            'value' => null,
        ],
        [
            'id' => 'fin_6',
            'title' => 'Maklumat Kemudahan Kredit / Baris Kredit Bank',
            'desc' => 'Nyatakan nama bank dan nilai had kemudahan kredit yang diluluskan.',
            'input_type' => 'text_input',
            'template_filename' => null,
            'is_required' => false,
            'allowed_extensions' => null,
            'status' => 'pending',
            'value' => null,
        ],
    ];

    public function mount(Acquisition $acquisition): void
    {
        $this->acquisition = $acquisition;
        $this->form->fillFromModel($this->acquisition);
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function enableEdit(): void
    {
        $this->form->fillFromModel($this->acquisition);
        $this->isEditing = true;
    }

    public function cancelEdit(): void
    {
        $this->form->fillFromModel($this->acquisition);
        $this->isEditing = false;
    }

    public function save(): void
    {
        $this->form->update($this->acquisition);
        $this->acquisition->refresh();
        $this->isEditing = false;
        session()->flash('success', 'Acquisition details updated successfully.');
    }

    public function transitionTo(string $targetStateClass): void
    {
        $this->acquisition->status->transitionTo($targetStateClass);
        $this->acquisition->refresh();
        $this->form->fillFromModel($this->acquisition);
        session()->flash('success', 'Status updated to ' . $this->acquisition->status->label());
    }

    public function openAddItemModal(string $checklistType): void
    {
        $this->newItemChecklistType = $checklistType;
        $this->newItemTitle = '';
        $this->newItemDesc = '';
        $this->newItemInputType = 'file_upload';
        $this->newItemTemplateFilename = '';
        $this->newItemIsRequired = true;
        $this->showAddItemModal = true;
    }

    public function closeAddItemModal(): void
    {
        $this->showAddItemModal = false;
    }

    public function saveChecklistItem(): void
    {
        if (trim($this->newItemTitle) === '') {
            $this->addError('newItemTitle', 'Item title is required.');
            return;
        }

        $newItem = [
            'id' => ($this->newItemChecklistType === 'technical' ? 'tech_' : 'fin_') . time(),
            'title' => $this->newItemTitle,
            'desc' => $this->newItemDesc,
            'input_type' => $this->newItemInputType,
            'template_filename' => in_array($this->newItemInputType, ['file_download_upload', 'file_download']) ? ($this->newItemTemplateFilename ?: 'Document_Template.pdf') : null,
            'is_required' => $this->newItemIsRequired,
            'allowed_extensions' => str_contains($this->newItemInputType, 'file') ? '.pdf,.docx,.xlsx' : null,
            'status' => 'pending',
            'value' => null,
        ];

        if ($this->newItemChecklistType === 'technical') {
            $this->technicalChecklist[] = $newItem;
        } else {
            $this->financialChecklist[] = $newItem;
        }

        $this->showAddItemModal = false;
        session()->flash('success', 'Checklist item added successfully.');
    }

    public function deleteChecklistItem(string $type, string $id): void
    {
        if ($type === 'technical') {
            $this->technicalChecklist = array_values(array_filter($this->technicalChecklist, fn($item) => $item['id'] !== $id));
        } else {
            $this->financialChecklist = array_values(array_filter($this->financialChecklist, fn($item) => $item['id'] !== $id));
        }
        session()->flash('success', 'Checklist item removed.');
    }

    public function toggleItemRequired(string $type, string $id): void
    {
        if ($type === 'technical') {
            foreach ($this->technicalChecklist as &$item) {
                if ($item['id'] === $id) {
                    $item['is_required'] = !$item['is_required'];
                }
            }
        } else {
            foreach ($this->financialChecklist as &$item) {
                if ($item['id'] === $id) {
                    $item['is_required'] = !$item['is_required'];
                }
            }
        }
    }

    // Supplier Preview Action Modal Handlers
    public function openSupplierActionModal(string $type, string $id): void
    {
        $this->activeSupplierType = $type;
        $items = $type === 'technical' ? $this->technicalChecklist : $this->financialChecklist;
        
        foreach ($items as $item) {
            if ($item['id'] === $id) {
                $this->activeSupplierItem = $item;
                $this->supplierInputText = (string) ($item['value'] ?? '');
                $this->supplierInputNumber = (string) ($item['value'] ?? '');
                $this->supplierInputBoolean = (bool) ($item['value'] ?? false);
                $this->supplierUploadedFilename = str_contains($item['input_type'], 'file') ? ($item['value'] ?? 'Dokumen_Telah_Dimuatnaik.pdf') : '';
                break;
            }
        }

        $this->showSupplierActionModal = true;
    }

    public function closeSupplierActionModal(): void
    {
        $this->showSupplierActionModal = false;
        $this->activeSupplierItem = null;
    }

    public function submitSupplierAction(): void
    {
        if (!$this->activeSupplierItem) {
            return;
        }

        $id = $this->activeSupplierItem['id'];
        $inputType = $this->activeSupplierItem['input_type'];
        $val = null;

        if ($inputType === 'text_input') {
            $val = $this->supplierInputText;
        } elseif ($inputType === 'number_input') {
            $val = $this->supplierInputNumber;
        } elseif ($inputType === 'boolean') {
            $val = $this->supplierInputBoolean;
        } else {
            $val = $this->supplierUploadedFilename ?: 'Dokumen_Telah_Dimuatnaik.pdf';
        }

        if ($this->activeSupplierType === 'technical') {
            foreach ($this->technicalChecklist as &$item) {
                if ($item['id'] === $id) {
                    $item['status'] = 'completed';
                    $item['value'] = $val;
                }
            }
        } else {
            foreach ($this->financialChecklist as &$item) {
                if ($item['id'] === $id) {
                    $item['status'] = 'completed';
                    $item['value'] = $val;
                }
            }
        }

        $this->showSupplierActionModal = false;
        session()->flash('success', 'Tindakan penyerahan telah disimpan.');
    }

    public function updatedFormAgencyId(): void
    {
        $this->form->subagency_id = null;
        $this->form->user_id = null;
    }

    public function updatedFormSubagencyId(): void
    {
        $this->form->user_id = null;
    }

    #[Computed]
    public function agencies(): \Illuminate\Database\Eloquent\Collection
    {
        return Agency::orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function subagencies(): \Illuminate\Database\Eloquent\Collection
    {
        return Subagency::when($this->form->agency_id, fn ($q) => $q->where('agency_id', $this->form->agency_id))
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    #[Computed]
    public function officers(): \Illuminate\Database\Eloquent\Collection
    {
        if (!$this->form->agency_id) {
            return new \Illuminate\Database\Eloquent\Collection();
        }

        return User::whereIn('id',
            AgencyOfficer::where('agency_id', $this->form->agency_id)
                ->when($this->form->subagency_id, fn ($q) => $q->where('subagency_id', $this->form->subagency_id))
                ->pluck('user_id')
        )->orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function votTypes(): \Illuminate\Database\Eloquent\Collection
    {
        return VotType::orderBy('name')->get(['id', 'name', 'code']);
    }

    #[Computed]
    public function acquisitionTypes(): array
    {
        return AcquisitionType::cases();
    }

    #[Computed]
    public function acquisitionMethods(): array
    {
        return AcquisitionMethod::cases();
    }

    #[Computed]
    public function committeeTypes(): array
    {
        return AcquisitionCommitteeType::cases();
    }
};
?>

<div class="space-y-6">

    {{-- Flash Message --}}
    @if(session('success'))
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 4000)"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/50 text-emerald-700 dark:text-emerald-400 text-sm font-medium shadow-xs"
        >
            <x-heroicon-o-check class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" />
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Main Header & Overview Card ── --}}
    <x-card>
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            {{-- Left: Project title, numbers, badges --}}
            <div class="space-y-2 min-w-0">
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 text-xs font-mono font-semibold border border-emerald-200 dark:border-emerald-800/50">
                        {{ $acquisition->project_number }}
                    </span>

                    @if($acquisition->status)
                        <x-badge variant="{{ $acquisition->status->color() }}">
                            {{ $acquisition->status->label() }}
                        </x-badge>
                    @endif

                    @if($acquisition->type)
                        <x-badge variant="primary">
                            {{ $acquisition->type instanceof \App\Enums\AcquisitionType ? $acquisition->type->value : $acquisition->type }}
                        </x-badge>
                    @endif
                </div>

                <h1 class="text-xl sm:text-2xl font-bold text-zinc-900 dark:text-zinc-100 tracking-tight leading-snug">
                    {{ $acquisition->project_name }}
                </h1>

                <div class="flex items-center gap-4 text-xs text-zinc-500 dark:text-zinc-400 flex-wrap">
                    @if($acquisition->tender_number)
                        <span class="flex items-center gap-1.5 font-mono">
                            <x-heroicon-o-document-text class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Tender: {{ $acquisition->tender_number }}
                        </span>
                    @endif

                    @if($acquisition->agency)
                        <span class="flex items-center gap-1.5">
                            <x-heroicon-o-building-office-2 class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            {{ $acquisition->agency->name }}
                        </span>
                    @endif

                    @if($acquisition->siling_price !== null)
                        <span class="flex items-center gap-1.5 font-mono font-semibold text-emerald-600 dark:text-emerald-400">
                            <x-heroicon-o-currency-dollar class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            RM {{ number_format((float) $acquisition->siling_price, 2) }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Right: Actions & Pipeline Status Transitions --}}
            <div class="flex flex-wrap items-center gap-2 lg:justify-end">
                @if($acquisition->status)
                    @foreach($acquisition->status->transitionableStateInstances() as $targetState)
                        <x-button 
                            variant="primary" 
                            size="sm" 
                            wire:click="transitionTo('{{ addslashes(get_class($targetState)) }}')" 
                            wire:loading.attr="disabled"
                            wire:target="transitionTo('{{ addslashes(get_class($targetState)) }}')"
                        >
                            <x-heroicon-o-arrow-right-circle class="w-3.5 h-3.5 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            <span wire:loading.remove wire:target="transitionTo('{{ addslashes(get_class($targetState)) }}')">
                                Advance to {{ $targetState->label() }}
                            </span>
                            <span wire:loading wire:target="transitionTo('{{ addslashes(get_class($targetState)) }}')">
                                Updating...
                            </span>
                        </x-button>
                    @endforeach
                @endif

                @if($activeTab === 'project-info')
                    @if(!$isEditing)
                        <x-button variant="outline" size="sm" wire:click="enableEdit" class="cursor-pointer">
                            <x-heroicon-o-pencil class="w-3.5 h-3.5 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Edit Project Info
                        </x-button>
                    @else
                        <x-button variant="outline" size="sm" wire:click="cancelEdit" class="cursor-pointer">
                            Cancel
                        </x-button>
                        <x-button variant="primary" size="sm" wire:click="save" wire:loading.attr="disabled" wire:target="save">
                            <span wire:loading.remove wire:target="save">
                                <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-1.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                Save Changes
                            </span>
                            <span wire:loading wire:target="save" class="flex items-center gap-1.5">
                                <x-icon-spinner class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" />
                                Saving...
                            </span>
                        </x-button>
                    @endif
                @endif

                <a href="{{ route('acquisition') }}">
                    <x-button variant="secondary" size="sm" class="cursor-pointer">
                        <x-heroicon-o-arrow-left class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        Back to List
                    </x-button>
                </a>
            </div>
        </div>
    </x-card>

    {{-- ── Tab Navigation Bar ── --}}
    <div class="border-b border-zinc-200 dark:border-zinc-800">
        <nav class="-mb-px flex gap-6 overflow-x-auto" aria-label="Tabs">
            {{-- Tab 1: Project Information --}}
            <button
                wire:click="setTab('project-info')"
                class="group inline-flex items-center gap-2 py-3 px-1 border-b-2 text-sm font-medium whitespace-nowrap cursor-pointer transition-colors {{ $activeTab === 'project-info' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300 hover:border-zinc-300 dark:hover:border-zinc-700' }}"
            >
                <x-heroicon-o-document-text class="w-4 h-4 shrink-0 {{ $activeTab === 'project-info' ? 'text-emerald-500' : 'text-zinc-400 group-hover:text-zinc-500 dark:group-hover:text-zinc-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                Project Information
            </button>

            {{-- Tab 2: Committee --}}
            <button
                wire:click="setTab('committee')"
                class="group inline-flex items-center gap-2 py-3 px-1 border-b-2 text-sm font-medium whitespace-nowrap cursor-pointer transition-colors {{ $activeTab === 'committee' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300 hover:border-zinc-300 dark:hover:border-zinc-700' }}"
            >
                <x-heroicon-o-user-group class="w-4 h-4 shrink-0 {{ $activeTab === 'committee' ? 'text-emerald-500' : 'text-zinc-400 group-hover:text-zinc-500 dark:group-hover:text-zinc-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                Committee
            </button>

            {{-- Tab 3: Technical Checklist --}}
            <button
                wire:click="setTab('technical-checklist')"
                class="group inline-flex items-center gap-2 py-3 px-1 border-b-2 text-sm font-medium whitespace-nowrap cursor-pointer transition-colors {{ $activeTab === 'technical-checklist' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300 hover:border-zinc-300 dark:hover:border-zinc-700' }}"
            >
                <x-heroicon-o-clipboard-document-check class="w-4 h-4 shrink-0 {{ $activeTab === 'technical-checklist' ? 'text-emerald-500' : 'text-zinc-400 group-hover:text-zinc-500 dark:group-hover:text-zinc-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                Technical Checklist
            </button>

            {{-- Tab 4: Financial Checklist --}}
            <button
                wire:click="setTab('financial-checklist')"
                class="group inline-flex items-center gap-2 py-3 px-1 border-b-2 text-sm font-medium whitespace-nowrap cursor-pointer transition-colors {{ $activeTab === 'financial-checklist' ? 'border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300 hover:border-zinc-300 dark:hover:border-zinc-700' }}"
            >
                <x-heroicon-o-banknotes class="w-4 h-4 shrink-0 {{ $activeTab === 'financial-checklist' ? 'text-emerald-500' : 'text-zinc-400 group-hover:text-zinc-500 dark:group-hover:text-zinc-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                Financial Checklist
            </button>
        </nav>
    </div>

    {{-- ── TAB CONTENT ── --}}

    {{-- ════ TAB 1: PROJECT INFORMATION ════ --}}
    @if($activeTab === 'project-info')
        @if(!$isEditing)
            {{-- VIEW MODE --}}
            <x-card>
                <div class="space-y-8">
                    {{-- Section 1: Basic Info --}}
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                            <span class="w-5 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                            Project Identification
                            <span class="flex-1 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                        </h3>
                        <dl class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Project Number</dt>
                                <dd class="mt-1 text-sm font-mono font-medium text-zinc-900 dark:text-zinc-100">{{ $acquisition->project_number ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Tender Number</dt>
                                <dd class="mt-1 text-sm font-mono text-zinc-900 dark:text-zinc-100">{{ $acquisition->tender_number ?: '—' }}</dd>
                            </div>
                            <div class="md:col-span-3">
                                <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Project Name</dt>
                                <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100 font-semibold">{{ $acquisition->project_name ?: '—' }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Section 2: Classification --}}
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                            <span class="w-5 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                            Classification & Category
                            <span class="flex-1 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                        </h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Acquisition Type</dt>
                                <dd class="mt-1.5">
                                    @if($acquisition->type)
                                        <x-badge variant="primary">
                                            {{ $acquisition->type instanceof \App\Enums\AcquisitionType ? $acquisition->type->value : $acquisition->type }}
                                        </x-badge>
                                    @else
                                        <span class="text-sm text-zinc-400">—</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Acquisition Method</dt>
                                <dd class="mt-1.5">
                                    @if($acquisition->method)
                                        <x-badge variant="secondary">
                                            {{ $acquisition->method instanceof \App\Enums\AcquisitionMethod ? $acquisition->method->value : $acquisition->method }}
                                        </x-badge>
                                    @else
                                        <span class="text-sm text-zinc-400">—</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">VOT Type</dt>
                                <dd class="mt-1 text-sm font-mono text-zinc-900 dark:text-zinc-100">
                                    @if($acquisition->votType)
                                        {{ $acquisition->votType->code }} — {{ $acquisition->votType->name }}
                                    @else
                                        <span class="text-zinc-400">—</span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Committee Type</dt>
                                <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100 font-medium">
                                    @if($acquisition->committee_type)
                                        {{ $acquisition->committee_type instanceof \App\Enums\AcquisitionCommitteeType ? $acquisition->committee_type->label() : $acquisition->committee_type }}
                                    @else
                                        <span class="text-zinc-400">—</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Section 3: Financial Parameters --}}
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                            <span class="w-5 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                            Financial Details
                            <span class="flex-1 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                        </h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Ceiling Price</dt>
                                <dd class="mt-1 text-base font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ $acquisition->siling_price !== null ? 'RM '.number_format((float) $acquisition->siling_price, 2) : '—' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Allocation Warrant No.</dt>
                                <dd class="mt-1 text-sm font-mono text-zinc-900 dark:text-zinc-100 font-medium">{{ $acquisition->no_allocation_warrant ?: '—' }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Section 4: Agency & Officers --}}
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                            <span class="w-5 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                            Agency & Responsibility
                            <span class="flex-1 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                        </h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Agency</dt>
                                <dd class="mt-1 text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $acquisition->agency?->name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Sub-Agency</dt>
                                <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $acquisition->subagency?->name ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Officer In-Charge</dt>
                                <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100 font-medium">
                                    @if($acquisition->user_id)
                                        @php $usr = \App\Models\User::find($acquisition->user_id); @endphp
                                        {{ $usr?->name ?? '—' }}
                                    @else
                                        <span class="text-zinc-400">—</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Section 5: Mandatory Requirements --}}
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                            <span class="w-5 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                            Compliance & Requirements
                            <span class="flex-1 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            @foreach([
                                [$acquisition->is_required_kbp, 'KBP Required', 'Kontraktor Bumiputera registration requirement'],
                                [$acquisition->mof_required,    'MOF Required', 'Ministry of Finance code registration'],
                                [$acquisition->cidb_required,   'CIDB Required', 'Construction Industry Development Board'],
                            ] as [$val, $label, $desc])
                                <div class="p-4 rounded-2xl border {{ $val ? 'border-emerald-200 dark:border-emerald-800/50 bg-emerald-50/50 dark:bg-emerald-950/20' : 'border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/30' }}">
                                    <div class="flex items-center gap-2.5">
                                        @if($val)
                                            <span class="w-6 h-6 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                                <x-heroicon-o-check class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" />
                                            </span>
                                        @else
                                            <span class="w-6 h-6 rounded-full bg-zinc-200 dark:bg-zinc-800 text-zinc-400 flex items-center justify-center shrink-0">
                                                <x-heroicon-o-x-mark class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                            </span>
                                        @endif
                                        <div>
                                            <div class="text-xs font-semibold {{ $val ? 'text-emerald-800 dark:text-emerald-300' : 'text-zinc-600 dark:text-zinc-400' }}">{{ $label }}</div>
                                            <div class="text-xs text-zinc-500 dark:text-zinc-500 mt-0.5">{{ $desc }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </x-card>
        @else
            {{-- EDIT MODE --}}
            <x-card>
                <form wire:submit="save" class="space-y-8">

                    {{-- Project Identification --}}
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                            <span class="w-5 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                            Edit Project Identification
                            <span class="flex-1 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <x-input id="project_number" disabled label="Project Number" placeholder="PRJ-2026-001" wire:model="form.project_number" :required="true" :error="$errors->first('form.project_number')" />
                            <x-input id="tender_number" label="Tender Number" placeholder="TND-2026-001" wire:model="form.tender_number" :error="$errors->first('form.tender_number')" />
                            <div class="md:col-span-2">
                                <x-input id="project_name" label="Project Name" placeholder="Full name of procurement acquisition project" wire:model="form.project_name" :required="true" :error="$errors->first('form.project_name')" />
                            </div>
                        </div>
                    </div>

                    {{-- Classification --}}
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                            <span class="w-5 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                            Edit Classification & Category
                            <span class="flex-1 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <x-label for="type" :required="true">Acquisition Type</x-label>
                                <select id="type" wire:model="form.type" class="block w-full rounded-xl border {{ $errors->has('form.type') ? 'border-rose-500' : 'border-zinc-200 dark:border-zinc-700/80' }} bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 py-2.5 px-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400">
                                    <option value="">Select type...</option>
                                    @foreach($this->acquisitionTypes as $t)
                                        <option value="{{ $t->value }}">{{ $t->value }}</option>
                                    @endforeach
                                </select>
                                @error('form.type') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1.5">
                                <x-label for="method" :required="true">Acquisition Method</x-label>
                                <select id="method" wire:model="form.method" class="block w-full rounded-xl border {{ $errors->has('form.method') ? 'border-rose-500' : 'border-zinc-200 dark:border-zinc-700/80' }} bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 py-2.5 px-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400">
                                    <option value="">Select method...</option>
                                    @foreach($this->acquisitionMethods as $m)
                                        <option value="{{ $m->value }}">{{ $m->value }}</option>
                                    @endforeach
                                </select>
                                @error('form.method') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1.5">
                                <x-label for="vot_type_id">VOT Type</x-label>
                                <select id="vot_type_id" wire:model="form.vot_type_id" class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 py-2.5 px-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400">
                                    <option value="">None</option>
                                    @foreach($this->votTypes as $vot)
                                        <option value="{{ $vot->id }}">{{ $vot->code }} — {{ $vot->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1.5">
                                <x-label for="committee_type">Committee Type</x-label>
                                <select id="committee_type" wire:model="form.committee_type" class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 py-2.5 px-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400">
                                    <option value="">Select committee type...</option>
                                    @foreach($this->committeeTypes as $comm)
                                        <option value="{{ $comm->value }}">{{ $comm->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Financial Details --}}
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                            <span class="w-5 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                            Edit Financial Details
                            <span class="flex-1 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <x-label for="siling_price">Ceiling Price (RM)</x-label>
                                <div class="relative rounded-xl shadow-xs">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400 text-sm font-medium">RM</div>
                                    <input id="siling_price" type="number" step="0.01" min="0" wire:model="form.siling_price" placeholder="0.00" class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 py-2.5 pl-10 pr-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400">
                                </div>
                                @error('form.siling_price') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <x-input id="no_allocation_warrant" label="Allocation Warrant No." placeholder="WP-2026-0012" wire:model="form.no_allocation_warrant" :error="$errors->first('form.no_allocation_warrant')" />
                        </div>
                    </div>

                    {{-- Agency & Officer --}}
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                            <span class="w-5 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                            Edit Agency & Officer
                            <span class="flex-1 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <x-label for="agency_id">Agency</x-label>
                                <select id="agency_id" wire:model.live="form.agency_id" class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 py-2.5 px-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400">
                                    <option value="">No agency</option>
                                    @foreach($this->agencies as $agency)
                                        <option value="{{ $agency->id }}">{{ $agency->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <x-label for="subagency_id">Sub-Agency</x-label>
                                <select id="subagency_id" wire:model.live="form.subagency_id" @if(!$form->agency_id) disabled @endif class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 py-2.5 px-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option value="">No sub-agency</option>
                                    @foreach($this->subagencies as $sub)
                                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-1.5 sm:col-span-2">
                                <x-label for="user_id">Officer</x-label>
                                <select id="user_id" wire:model="form.user_id" @if(!$form->agency_id) disabled @endif class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 py-2.5 px-3.5 text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <option value="">Select officer...</option>
                                    @foreach($this->officers as $officer)
                                        <option value="{{ $officer->id }}">{{ $officer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Requirements --}}
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4 flex items-center gap-2">
                            <span class="w-5 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                            Edit Requirements & Flags
                            <span class="flex-1 h-px bg-zinc-300 dark:bg-zinc-700"></span>
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            @foreach([
                                ['is_required_kbp', 'KBP Required', 'Kontraktor Bumiputera'],
                                ['mof_required',    'MOF Required', 'Ministry of Finance'],
                                ['cidb_required',   'CIDB Required', 'Const. Industry Dev. Board'],
                            ] as [$field, $label, $desc])
                                <label class="flex items-start gap-3 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700/80 cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors">
                                    <div class="relative mt-0.5 shrink-0">
                                        <input type="checkbox" wire:model="form.{{ $field }}" class="peer sr-only" id="{{ $field }}">
                                        <div class="w-9 h-5 rounded-full bg-zinc-200 dark:bg-zinc-700 peer-checked:bg-emerald-500 transition-colors"></div>
                                        <div class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white shadow-sm transition-transform peer-checked:translate-x-4"></div>
                                    </div>
                                    <div>
                                        <div class="text-xs font-semibold text-zinc-700 dark:text-zinc-200">{{ $label }}</div>
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $desc }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                        <x-button variant="outline" size="sm" wire:click="cancelEdit">Cancel</x-button>
                        <x-button variant="primary" size="sm" type="submit" wire:loading.attr="disabled" wire:target="save">
                            <span wire:loading.remove wire:target="save">Save Changes</span>
                            <span wire:loading wire:target="save">Saving...</span>
                        </x-button>
                    </div>
                </form>
            </x-card>
        @endif
    @endif

    {{-- ════ TAB 2: COMMITTEE ════ --}}
    @if($activeTab === 'committee')
        <x-card>
            <div class="space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-zinc-100 dark:border-zinc-800">
                    <div>
                        <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                            <x-heroicon-o-user-group class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Acquisition Evaluation Committee
                        </h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                            Committee setup and appointed evaluation members for {{ $acquisition->project_number }}.
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <x-badge variant="info">
                            {{ $acquisition->committee_type ? ($acquisition->committee_type instanceof \App\Enums\AcquisitionCommitteeType ? $acquisition->committee_type->label() : $acquisition->committee_type) : 'Unassigned Type' }}
                        </x-badge>
                    </div>
                </div>

                {{-- Committee Details Summary --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/80 dark:border-zinc-800">
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Committee Classification</span>
                        <div class="mt-1 text-sm font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ $acquisition->committee_type ? ($acquisition->committee_type instanceof \App\Enums\AcquisitionCommitteeType ? $acquisition->committee_type->label() : $acquisition->committee_type) : 'Not specified' }}
                        </div>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Lead Agency / Department</span>
                        <div class="mt-1 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $acquisition->agency?->name ?? 'Primary Procurement Agency' }}
                        </div>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Assigned Officer</span>
                        <div class="mt-1 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                            @if($acquisition->user_id)
                                @php $usr = \App\Models\User::find($acquisition->user_id); @endphp
                                {{ $usr?->name ?? '—' }}
                            @else
                                <span class="text-zinc-400">Unassigned</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Appointed Members Table --}}
                <div class="space-y-3">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                        Appointed Committee Members
                    </h4>

                    <div class="overflow-x-auto rounded-xl border border-zinc-100 dark:border-zinc-800">
                        <table class="min-w-full divide-y divide-zinc-100 dark:divide-zinc-800">
                            <thead class="bg-zinc-50/70 dark:bg-zinc-800/40">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase">Role / Position</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase">Officer Name</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase">Department</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900 text-sm">
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-zinc-100">Committee Chairman (Pengerusi Jawatankuasa)</td>
                                    <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">
                                        {{ $acquisition->user_id ? \App\Models\User::find($acquisition->user_id)?->name : 'Senior Evaluation Officer' }}
                                    </td>
                                    <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $acquisition->agency?->name ?? 'Bahagian Perolehan' }}</td>
                                    <td class="px-4 py-3"><x-badge variant="success" pill>Appointed</x-badge></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-zinc-100">Technical Evaluator (Ahli Jawatankuasa Teknikal)</td>
                                    <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">Technical Assessor Officer</td>
                                    <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">Jabatan Teknologi Maklumat</td>
                                    <td class="px-4 py-3"><x-badge variant="success" pill>Appointed</x-badge></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-zinc-100">Financial Evaluator (Ahli Jawatankuasa Kewangan)</td>
                                    <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">Financial Auditor Officer</td>
                                    <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">Bahagian Kewangan & Akaun</td>
                                    <td class="px-4 py-3"><x-badge variant="info" pill>Pending Confirmation</x-badge></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </x-card>
    @endif

    {{-- ════ TAB 3: TECHNICAL CHECKLIST ════ --}}
    @if($activeTab === 'technical-checklist')
        <x-card>
            <div class="space-y-6">
                {{-- Clean Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-zinc-100 dark:border-zinc-800">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                                <x-heroicon-o-clipboard-document-check class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                Technical Checklist Setup
                            </h3>
                            @if($previewSupplierMode)
                                <x-badge variant="warning" pill>Supplier View Preview</x-badge>
                            @else
                                <x-badge variant="success" pill>Officer Mode</x-badge>
                            @endif
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                            Configure required technical documents, template downloads, and input fields for suppliers to fulfill.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <button
                            wire:click="$toggle('previewSupplierMode')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all {{ $previewSupplierMode ? 'bg-amber-500 text-white shadow-xs' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}"
                        >
                            <x-heroicon-o-eye class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            {{ $previewSupplierMode ? 'Exit Supplier Preview' : 'Preview Supplier View' }}
                        </button>

                        @if(!$previewSupplierMode)
                            <x-button variant="primary" size="sm" wire:click="openAddItemModal('technical')" class="cursor-pointer">
                                <x-heroicon-o-plus class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                Add Checklist Item
                            </x-button>
                        @endif
                    </div>
                </div>

                {{-- ── OFFICER CONFIGURATION MODE ── --}}
                @if(!$previewSupplierMode)
                    <div class="overflow-x-auto rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs">
                        <table class="min-w-full divide-y divide-zinc-100 dark:divide-zinc-800">
                            <thead class="bg-zinc-50/70 dark:bg-zinc-800/40">
                                <tr>
                                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">#</th>
                                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Requirement / Document Title</th>
                                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Submission Type</th>
                                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Requirement</th>
                                    <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold text-zinc-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 text-sm">
                                @foreach($technicalChecklist as $index => $item)
                                    <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/20 transition-colors">
                                        <td class="px-4 py-3.5 font-mono text-xs font-bold text-zinc-400">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3.5">
                                            <div class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm">{{ $item['title'] }}</div>
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $item['desc'] }}</div>
                                            @if($item['template_filename'])
                                                <div class="mt-1 flex items-center gap-1.5 text-xs font-mono text-indigo-600 dark:text-indigo-400">
                                                    <x-heroicon-o-paper-clip class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                                    <span>Template: {{ $item['template_filename'] }}</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap">
                                            @if($item['input_type'] === 'file_download_upload')
                                                <x-badge variant="info" pill>Download Template & Upload</x-badge>
                                            @elseif($item['input_type'] === 'file_upload')
                                                <x-badge variant="primary" pill>File Upload</x-badge>
                                            @elseif($item['input_type'] === 'text_input')
                                                <x-badge variant="warning" pill>Text Input</x-badge>
                                            @elseif($item['input_type'] === 'number_input')
                                                <x-badge variant="success" pill>Number Input</x-badge>
                                            @elseif($item['input_type'] === 'boolean')
                                                <x-badge variant="secondary" pill>Yes/No Compliance</x-badge>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap">
                                            <button wire:click="toggleItemRequired('technical', '{{ $item['id'] }}')" class="cursor-pointer">
                                                @if($item['is_required'])
                                                    <x-badge variant="danger" pill>Wajib / Mandatory</x-badge>
                                                @else
                                                    <x-badge variant="secondary" pill>Pilihan / Optional</x-badge>
                                                @endif
                                            </button>
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap text-right">
                                            <button
                                                wire:click="deleteChecklistItem('technical', '{{ $item['id'] }}')"
                                                class="p-1.5 rounded-lg text-zinc-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 cursor-pointer transition-colors"
                                                title="Delete item"
                                            >
                                                <x-heroicon-o-trash class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    {{-- ── SUPPLIER PREVIEW MODE (TABULAR VIEW WITH ACTION BUTTONS) ── --}}
                    <div class="space-y-4">
                        <div class="p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 flex items-center justify-between text-xs text-amber-800 dark:text-amber-300">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-information-circle class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                <span>Simulasi Paparan Pembekal — Klik butang tindakan di kolum kanan untuk melengkapkan penyerahan dokumen.</span>
                            </div>
                            @php
                                $completedTechCount = count(array_filter($technicalChecklist, fn($i) => ($i['status'] ?? '') === 'completed'));
                            @endphp
                            <span class="font-mono font-bold">{{ $completedTechCount }} / {{ count($technicalChecklist) }} Completed</span>
                        </div>

                        <div class="overflow-x-auto rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs">
                            <table class="min-w-full divide-y divide-zinc-100 dark:divide-zinc-800">
                                <thead class="bg-zinc-50/70 dark:bg-zinc-800/40">
                                    <tr>
                                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">#</th>
                                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Requirement / Document Title</th>
                                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Submission Type</th>
                                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold text-zinc-500 uppercase tracking-wider">Supplier Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 text-sm">
                                    @foreach($technicalChecklist as $index => $item)
                                        <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/20 transition-colors">
                                            <td class="px-4 py-4 font-mono text-xs font-bold text-zinc-400">{{ $index + 1 }}</td>
                                            <td class="px-4 py-4 max-w-md">
                                                <div class="flex items-center gap-2">
                                                    <h4 class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm">{{ $item['title'] }}</h4>
                                                    @if($item['is_required'])
                                                        <span class="text-xs text-rose-500 font-bold">*Wajib</span>
                                                    @endif
                                                </div>
                                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $item['desc'] }}</p>

                                                @if(($item['status'] ?? '') === 'completed' && !empty($item['value']))
                                                    <div class="mt-2 text-xs font-mono text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                                                        <x-heroicon-o-check-circle class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                                        <span>Submitted: {{ is_bool($item['value']) ? 'Patuh (Yes)' : $item['value'] }}</span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                @if($item['input_type'] === 'file_download_upload')
                                                    <x-badge variant="info">Muat Turun & Muat Naik</x-badge>
                                                @elseif($item['input_type'] === 'file_upload')
                                                    <x-badge variant="primary">Muat Naik Dokumen</x-badge>
                                                @elseif($item['input_type'] === 'text_input')
                                                    <x-badge variant="warning">Jawapan Teks</x-badge>
                                                @elseif($item['input_type'] === 'number_input')
                                                    <x-badge variant="success">Nilai Nombor</x-badge>
                                                @elseif($item['input_type'] === 'boolean')
                                                    <x-badge variant="secondary">Pematuhan</x-badge>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                @if(($item['status'] ?? '') === 'completed')
                                                    <x-badge variant="success" pill>Telah Selesai</x-badge>
                                                @else
                                                    <x-badge variant="warning" pill>Belum Selesai</x-badge>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    @if(in_array($item['input_type'], ['file_download_upload', 'file_download']))
                                                        <a
                                                            href="#"
                                                            onclick="alert('Simulasi: Muat turun templat {{ $item['template_filename'] }}'); return false;"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 transition-colors border border-indigo-200 dark:border-indigo-800/50"
                                                        >
                                                            <x-heroicon-o-arrow-down-tray class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                                            Muat Turun Templat
                                                        </a>
                                                    @endif

                                                    @if(in_array($item['input_type'], ['file_upload', 'file_download_upload']))
                                                        <button
                                                            wire:click="openSupplierActionModal('technical', '{{ $item['id'] }}')"
                                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white cursor-pointer transition-colors shadow-xs"
                                                        >
                                                            <x-heroicon-o-arrow-up-tray class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                                            Muat Naik Fail
                                                        </button>
                                                    @elseif($item['input_type'] === 'text_input')
                                                        <button
                                                            wire:click="openSupplierActionModal('technical', '{{ $item['id'] }}')"
                                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold bg-amber-600 hover:bg-amber-500 text-white cursor-pointer transition-colors shadow-xs"
                                                        >
                                                            <x-heroicon-o-pencil-square class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                                            Isi Maklumat
                                                        </button>
                                                    @elseif($item['input_type'] === 'number_input')
                                                        <button
                                                            wire:click="openSupplierActionModal('technical', '{{ $item['id'] }}')"
                                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold bg-blue-600 hover:bg-blue-500 text-white cursor-pointer transition-colors shadow-xs"
                                                        >
                                                            <x-heroicon-o-hashtag class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                                            Masukkan Nilai
                                                        </button>
                                                    @elseif($item['input_type'] === 'boolean')
                                                        <button
                                                            wire:click="openSupplierActionModal('technical', '{{ $item['id'] }}')"
                                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold bg-zinc-800 dark:bg-zinc-700 hover:bg-zinc-700 text-white cursor-pointer transition-colors shadow-xs"
                                                        >
                                                            <x-heroicon-o-check-circle class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                                            Sahkan Pematuhan
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </x-card>
    @endif

    {{-- ════ TAB 4: FINANCIAL CHECKLIST ════ --}}
    @if($activeTab === 'financial-checklist')
        <x-card>
            <div class="space-y-6">
                {{-- Clean Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-zinc-100 dark:border-zinc-800">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                                <x-heroicon-o-banknotes class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                Financial Checklist Setup
                            </h3>
                            @if($previewSupplierMode)
                                <x-badge variant="warning" pill>Supplier View Preview</x-badge>
                            @else
                                <x-badge variant="success" pill>Officer Mode</x-badge>
                            @endif
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                            Configure required financial documents, tender pricing tables, and bank statement uploads for suppliers.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <button
                            wire:click="$toggle('previewSupplierMode')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold cursor-pointer transition-all {{ $previewSupplierMode ? 'bg-amber-500 text-white shadow-xs' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}"
                        >
                            <x-heroicon-o-eye class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            {{ $previewSupplierMode ? 'Exit Supplier Preview' : 'Preview Supplier View' }}
                        </button>

                        @if(!$previewSupplierMode)
                            <x-button variant="primary" size="sm" wire:click="openAddItemModal('financial')" class="cursor-pointer">
                                <x-heroicon-o-plus class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                Add Financial Item
                            </x-button>
                        @endif
                    </div>
                </div>

                {{-- ── OFFICER CONFIGURATION MODE ── --}}
                @if(!$previewSupplierMode)
                    <div class="overflow-x-auto rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs">
                        <table class="min-w-full divide-y divide-zinc-100 dark:divide-zinc-800">
                            <thead class="bg-zinc-50/70 dark:bg-zinc-800/40">
                                <tr>
                                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">#</th>
                                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Requirement / Document Title</th>
                                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Submission Type</th>
                                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Requirement</th>
                                    <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold text-zinc-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 text-sm">
                                @foreach($financialChecklist as $index => $item)
                                    <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/20 transition-colors">
                                        <td class="px-4 py-3.5 font-mono text-xs font-bold text-zinc-400">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3.5">
                                            <div class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm">{{ $item['title'] }}</div>
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $item['desc'] }}</div>
                                            @if($item['template_filename'])
                                                <div class="mt-1 flex items-center gap-1.5 text-xs font-mono text-indigo-600 dark:text-indigo-400">
                                                    <x-heroicon-o-paper-clip class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                                    <span>Template: {{ $item['template_filename'] }}</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap">
                                            @if($item['input_type'] === 'file_download_upload')
                                                <x-badge variant="info" pill>Download Template & Upload</x-badge>
                                            @elseif($item['input_type'] === 'file_upload')
                                                <x-badge variant="primary" pill>File Upload</x-badge>
                                            @elseif($item['input_type'] === 'text_input')
                                                <x-badge variant="warning" pill>Text Input</x-badge>
                                            @elseif($item['input_type'] === 'number_input')
                                                <x-badge variant="success" pill>Number Input (RM)</x-badge>
                                            @elseif($item['input_type'] === 'boolean')
                                                <x-badge variant="secondary" pill>Yes/No Compliance</x-badge>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap">
                                            <button wire:click="toggleItemRequired('financial', '{{ $item['id'] }}')" class="cursor-pointer">
                                                @if($item['is_required'])
                                                    <x-badge variant="danger" pill>Wajib / Mandatory</x-badge>
                                                @else
                                                    <x-badge variant="secondary" pill>Pilihan / Optional</x-badge>
                                                @endif
                                            </button>
                                        </td>
                                        <td class="px-4 py-3.5 whitespace-nowrap text-right">
                                            <button
                                                wire:click="deleteChecklistItem('financial', '{{ $item['id'] }}')"
                                                class="p-1.5 rounded-lg text-zinc-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 cursor-pointer transition-colors"
                                                title="Delete item"
                                            >
                                                <x-heroicon-o-trash class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    {{-- ── SUPPLIER PREVIEW MODE (TABULAR VIEW WITH ACTION BUTTONS) ── --}}
                    <div class="space-y-4">
                        <div class="p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 flex items-center justify-between text-xs text-amber-800 dark:text-amber-300">
                            <div class="flex items-center gap-2">
                                <x-heroicon-o-information-circle class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                <span>Simulasi Paparan Pembekal — Klik butang tindakan di kolum kanan untuk melengkapkan penyerahan dokumen kewangan.</span>
                            </div>
                            @php
                                $completedFinCount = count(array_filter($financialChecklist, fn($i) => ($i['status'] ?? '') === 'completed'));
                            @endphp
                            <span class="font-mono font-bold">{{ $completedFinCount }} / {{ count($financialChecklist) }} Completed</span>
                        </div>

                        <div class="overflow-x-auto rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs">
                            <table class="min-w-full divide-y divide-zinc-100 dark:divide-zinc-800">
                                <thead class="bg-zinc-50/70 dark:bg-zinc-800/40">
                                    <tr>
                                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">#</th>
                                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Requirement / Document Title</th>
                                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Submission Type</th>
                                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold text-zinc-500 uppercase tracking-wider">Supplier Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 text-sm">
                                    @foreach($financialChecklist as $index => $item)
                                        <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/20 transition-colors">
                                            <td class="px-4 py-4 font-mono text-xs font-bold text-zinc-400">{{ $index + 1 }}</td>
                                            <td class="px-4 py-4 max-w-md">
                                                <div class="flex items-center gap-2">
                                                    <h4 class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm">{{ $item['title'] }}</h4>
                                                    @if($item['is_required'])
                                                        <span class="text-xs text-rose-500 font-bold">*Wajib</span>
                                                    @endif
                                                </div>
                                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $item['desc'] }}</p>

                                                @if(($item['status'] ?? '') === 'completed' && !empty($item['value']))
                                                    <div class="mt-2 text-xs font-mono text-emerald-600 dark:text-emerald-400 flex items-center gap-1.5">
                                                        <x-heroicon-o-check-circle class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                                        <span>Submitted: {{ is_bool($item['value']) ? 'Patuh (Yes)' : $item['value'] }}</span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                @if($item['input_type'] === 'file_download_upload')
                                                    <x-badge variant="info">Muat Turun & Muat Naik</x-badge>
                                                @elseif($item['input_type'] === 'file_upload')
                                                    <x-badge variant="primary">Muat Naik Dokumen</x-badge>
                                                @elseif($item['input_type'] === 'text_input')
                                                    <x-badge variant="warning">Jawapan Teks</x-badge>
                                                @elseif($item['input_type'] === 'number_input')
                                                    <x-badge variant="success">Nilai Nombor (RM)</x-badge>
                                                @elseif($item['input_type'] === 'boolean')
                                                    <x-badge variant="secondary">Pematuhan</x-badge>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                @if(($item['status'] ?? '') === 'completed')
                                                    <x-badge variant="success" pill>Telah Selesai</x-badge>
                                                @else
                                                    <x-badge variant="warning" pill>Belum Selesai</x-badge>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    @if(in_array($item['input_type'], ['file_download_upload', 'file_download']))
                                                        <a
                                                            href="#"
                                                            onclick="alert('Simulasi: Muat turun templat {{ $item['template_filename'] }}'); return false;"
                                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 transition-colors border border-indigo-200 dark:border-indigo-800/50"
                                                        >
                                                            <x-heroicon-o-arrow-down-tray class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                                            Muat Turun Templat
                                                        </a>
                                                    @endif

                                                    @if(in_array($item['input_type'], ['file_upload', 'file_download_upload']))
                                                        <button
                                                            wire:click="openSupplierActionModal('financial', '{{ $item['id'] }}')"
                                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white cursor-pointer transition-colors shadow-xs"
                                                        >
                                                            <x-heroicon-o-arrow-up-tray class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                                            Muat Naik Fail
                                                        </button>
                                                    @elseif($item['input_type'] === 'text_input')
                                                        <button
                                                            wire:click="openSupplierActionModal('financial', '{{ $item['id'] }}')"
                                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold bg-amber-600 hover:bg-amber-500 text-white cursor-pointer transition-colors shadow-xs"
                                                        >
                                                            <x-heroicon-o-pencil-square class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                                            Isi Maklumat
                                                        </button>
                                                    @elseif($item['input_type'] === 'number_input')
                                                        <button
                                                            wire:click="openSupplierActionModal('financial', '{{ $item['id'] }}')"
                                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold bg-blue-600 hover:bg-blue-500 text-white cursor-pointer transition-colors shadow-xs"
                                                        >
                                                            <x-heroicon-o-hashtag class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                                            Masukkan Nilai
                                                        </button>
                                                    @elseif($item['input_type'] === 'boolean')
                                                        <button
                                                            wire:click="openSupplierActionModal('financial', '{{ $item['id'] }}')"
                                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold bg-zinc-800 dark:bg-zinc-700 hover:bg-zinc-700 text-white cursor-pointer transition-colors shadow-xs"
                                                        >
                                                            <x-heroicon-o-check-circle class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                                            Sahkan Pematuhan
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </x-card>
    @endif

    {{-- ════ ADD CHECKLIST ITEM MODAL (OFFICER) ════ --}}
    @if($showAddItemModal)
        <div
            x-data
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
        >
            <div class="fixed inset-0 bg-zinc-950/60 backdrop-blur-sm" wire:click="closeAddItemModal"></div>

            <div class="relative z-10 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 p-6 w-full max-w-lg space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                        Tambah Perkara Senarai Semak {{ ucfirst($newItemChecklistType) }}
                    </h3>
                    <button wire:click="closeAddItemModal" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                        <x-heroicon-o-x-mark class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </button>
                </div>

                <form wire:submit="saveChecklistItem" class="space-y-4">
                    <div>
                        <x-label for="newItemTitle" :required="true">Tajuk Perkara / Nama Dokumen</x-label>
                        <input id="newItemTitle" type="text" wire:model="newItemTitle" placeholder="contoh: Borang Pengesahan Lesen SIRIM" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100 focus:ring-emerald-500 focus:border-emerald-500">
                        @error('newItemTitle') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <x-label for="newItemDesc">Keterangan / Arahan Kepada Pembekal</x-label>
                        <textarea id="newItemDesc" wire:model="newItemDesc" rows="2" placeholder="Jelaskan apa yang perlu dimuat naik atau diisi oleh pembekal..." class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                    </div>

                    <div>
                        <x-label for="newItemInputType" :required="true">Jenis Keperluan Input / Dokumen</x-label>
                        <select id="newItemInputType" wire:model.live="newItemInputType" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="file_upload">Muat Naik Fail Dokumen (File Upload)</option>
                            <option value="file_download_upload">Muat Turun Templat Pegawai & Muat Naik Semula</option>
                            <option value="text_input">Jawapan Teks Ringkas (Text Input)</option>
                            <option value="number_input">Input Nombor / Nilai Harga (Number Input)</option>
                            <option value="boolean">Kotak Semak Pematuhan (Yes/No Checkbox)</option>
                        </select>
                    </div>

                    @if(in_array($newItemInputType, ['file_download_upload', 'file_download']))
                        <div>
                            <x-label for="newItemTemplateFilename">Nama Fail Templat (Dokumen Rujukan Pegawai)</x-label>
                            <input id="newItemTemplateFilename" type="text" wire:model="newItemTemplateFilename" placeholder="contoh: Borang_Templat_Spesifikasi.pdf" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100">
                        </div>
                    @endif

                    <label class="flex items-center gap-3 cursor-pointer pt-2">
                        <input type="checkbox" wire:model="newItemIsRequired" class="rounded border-zinc-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                        <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-200">Tanda sebagai Wajib (Mandatory Item)</span>
                    </label>

                    <div class="flex justify-end gap-3 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                        <x-button variant="outline" size="sm" type="button" wire:click="closeAddItemModal">Batal</x-button>
                        <x-button variant="primary" size="sm" type="submit">Simpan Perkara</x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ════ SUPPLIER PREVIEW ACTION MODAL ════ --}}
    @if($showSupplierActionModal && $activeSupplierItem)
        <div
            x-data
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
        >
            <div class="fixed inset-0 bg-zinc-950/60 backdrop-blur-sm" wire:click="closeSupplierActionModal"></div>

            <div class="relative z-10 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 p-6 w-full max-w-lg space-y-5">
                <div class="flex items-start justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div>
                        <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Tindakan Penyerahan Pembekal</span>
                        <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 mt-0.5">
                            {{ $activeSupplierItem['title'] }}
                        </h3>
                    </div>
                    <button wire:click="closeSupplierActionModal" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                        <x-heroicon-o-x-mark class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </button>
                </div>

                <div class="space-y-4">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ $activeSupplierItem['desc'] }}
                    </p>

                    @if(in_array($activeSupplierItem['input_type'], ['file_download_upload', 'file_download']))
                        <div class="p-3 rounded-xl bg-indigo-50/60 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/40 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2 text-xs text-indigo-700 dark:text-indigo-300 font-medium">
                                <x-heroicon-o-arrow-down-tray class="w-4 h-4 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                <span>Templat: <strong class="font-mono">{{ $activeSupplierItem['template_filename'] }}</strong></span>
                            </div>
                            <a href="#" onclick="alert('Simulasi muat turun templat'); return false;" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-600 text-white hover:bg-indigo-500">
                                Muat Turun
                            </a>
                        </div>
                    @endif

                    {{-- Dynamic Input Field --}}
                    @if(in_array($activeSupplierItem['input_type'], ['file_upload', 'file_download_upload']))
                        <div class="space-y-2">
                            <x-label for="supplierUploadedFilename">Pilih Fail Dokumen Untuk Dimuat Naik</x-label>
                            <input id="supplierUploadedFilename" type="text" wire:model="supplierUploadedFilename" placeholder="nama_fail_dokumen_pembekal.pdf" class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100">
                            <p class="text-xs text-zinc-400 font-mono">Format: {{ $activeSupplierItem['allowed_extensions'] ?: '.pdf, .docx' }} (Max 25MB)</p>
                        </div>
                    @elseif($activeSupplierItem['input_type'] === 'text_input')
                        <div class="space-y-2">
                            <x-label for="supplierInputText">Masukkan Maklumat / Teks Jawapan</x-label>
                            <textarea id="supplierInputText" wire:model="supplierInputText" rows="3" placeholder="Nyatakan jawapan atau maklumat perkhidmatan..." class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100"></textarea>
                        </div>
                    @elseif($activeSupplierItem['input_type'] === 'number_input')
                        <div class="space-y-2">
                            <x-label for="supplierInputNumber">Masukkan Nilai Nombor / Amaun RM</x-label>
                            <input id="supplierInputNumber" type="number" step="0.01" wire:model="supplierInputNumber" placeholder="0.00" class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100">
                        </div>
                    @elseif($activeSupplierItem['input_type'] === 'boolean')
                        <label class="flex items-center gap-3 p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/40 cursor-pointer">
                            <input type="checkbox" wire:model="supplierInputBoolean" class="rounded border-zinc-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                            <span class="text-xs font-semibold text-zinc-700 dark:text-zinc-200">Saya mengesahkan patuh kepada semua syarat dan spesifikasi yang ditetapkan.</span>
                        </label>
                    @endif
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                    <x-button variant="outline" size="sm" wire:click="closeSupplierActionModal">Tutup</x-button>
                    <x-button variant="primary" size="sm" wire:click="submitSupplierAction">Simpan Penyerahan</x-button>
                </div>
            </div>
        </div>
    @endif

</div>
