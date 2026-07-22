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

    public string $activeTab = 'project-info'; // 'project-info' | 'committee' | 'technical-checklist' | 'financial-checklist' | 'technical-spec' | 'financial-pricelist' | 'scoring' | 'documents'
    public bool $isEditing = false;
    public bool $previewSupplierMode = false;
    public bool $isEvaluationUnlocked = false; // Lock state for evaluation tabs
    public bool $expandFullTitle = false; // Toggle full project title view

    public function toggleFullTitle(): void
    {
        $this->expandFullTitle = !$this->expandFullTitle;
    }

    // Add Checklist Item Modal State (Officer)
    public bool $showAddItemModal = false;
    public string $newItemChecklistType = 'technical';
    public string $newItemTitle = '';
    public string $newItemDesc = '';
    public string $newItemInputType = 'file_upload';
    public string $newItemTemplateFilename = '';
    public float $newItemWeightage = 15.0;
    public bool $newItemIsRequired = true;

    // Supplier Preview Action Modal State
    public bool $showSupplierActionModal = false;
    public ?array $activeSupplierItem = null;
    public string $activeSupplierType = 'technical';
    public string $supplierInputText = '';
    public string $supplierInputNumber = '';
    public bool $supplierInputBoolean = false;
    public string $supplierUploadedFilename = '';

    // Modals for Technical Spec & Financial Pricelist
    public bool $showAddTechSpecModal = false;
    public int $newSpecLevel = 1;
    public string $newSpecParentId = '';
    public string $newSpecCode = '';
    public string $newSpecName = '';
    public string $newSpecDesc = '';
    public string $newSpecType = 'text';
    public float $newSpecWeightage = 10.0;

    public bool $showAddPricelistModal = false;
    public string $newPriceCode = '';
    public string $newPriceName = '';
    public string $newPriceUom = 'Unit';
    public int $newPriceQty = 1;
    public float $newPriceEstUnitPrice = 0.0;
    public float $newPriceWeightage = 10.0;

    // Scoring Tab Configurations
    public float $techWeightageRatio = 70.0;
    public float $finWeightageRatio = 30.0;
    public float $passingTechMark = 70.0;

    // Document for Procurement Modal State
    public bool $showProcurementDocModal = false;
    public ?array $activeProcurementDoc = null;

    // Meeting Notice Modal State (Committee Tab)
    public bool $showMeetingNoticeModal = false;
    public string $noticeCommitteeType = 'JK Spesifikasi';
    public string $noticeSubject = 'Mesyuarat Penyediaan Spesifikasi & Semakan Checklist';
    public string $noticeVenue = 'Bilik Mesyuarat Utama / Google Meet';
    public string $noticeStartAt = '2026-08-01T09:00';
    public string $noticeEndAt = '2026-08-01T12:30';
    public string $noticeAgenda = 'Memuktamadkan Spesifikasi Teknikal (3-Lapisan), Jadual Harga BOQ, Technical/Financial Checklists, dan Scoring.';

    // Committee Meeting Notices List
    public array $meetingNotices = [
        [
            'id' => 'notice_1',
            'code' => 'NOTIS-JKSPES-2026/01',
            'committee_type' => 'JK Spesifikasi',
            'subject' => 'Mesyuarat Penyediaan Spesifikasi & Semakan Checklist',
            'venue' => 'Bilik Mesyuarat Utama, Aras 4 / Google Meet',
            'start_at' => '2026-08-01 09:00 AM',
            'end_at' => '2026-08-01 12:30 PM',
            'agenda' => 'Memuktamadkan Spesifikasi Teknikal (3-Lapisan), Jadual Harga BOQ, Technical & Financial Checklists, serta Nisbah Scoring.',
            'status' => 'sent',
            'recipients' => ['Senior Evaluation Officer (Pengerusi)', 'Technical Assessor Officer', 'Financial Auditor Officer'],
            'focus_tabs' => ['Technical Checklist', 'Financial Checklist', 'Technical Specification', 'Financial Pricelist', 'Scoring'],
        ],
        [
            'id' => 'notice_2',
            'code' => 'NOTIS-JKTEK-2026/01',
            'committee_type' => 'JK Penilaian Teknikal',
            'subject' => 'Mesyuarat Penilaian Dokumen Teknikal Pembekal',
            'venue' => 'Bilik Mesyuarat Perolehan 2',
            'start_at' => '2026-08-15 02:00 PM',
            'end_at' => '2026-08-15 05:00 PM',
            'agenda' => 'Penilaian dan pemarkahan dokumen teknikal pembekal yang telah diserahkan mengikut kriteria 3-lapisan.',
            'status' => 'draft',
            'recipients' => ['Technical Assessor Officer', 'Senior Technical Evaluator'],
            'focus_tabs' => ['Technical Checklist', 'Technical Specification'],
        ],
    ];

    // Official Procurement Printable Documents List (English Naming Conventions)
    public array $procurementDocuments = [
        [
            'id' => 'doc_tech_check',
            'code' => 'DOC-TECH-01',
            'title' => 'Technical Submission Checklist Document',
            'malay_ref' => 'Checklist Teknikal Dokumen Penyerahan',
            'desc' => 'Official printable checklist schedule detailing all required technical documents for supplier submission.',
            'category' => 'Checklist',
        ],
        [
            'id' => 'doc_fin_check',
            'code' => 'DOC-FIN-01',
            'title' => 'Financial Submission Checklist Document',
            'malay_ref' => 'Checklist Kewangan Dokumen Penyerahan',
            'desc' => 'Official printable checklist schedule detailing financial statements, audited reports, and tax declarations.',
            'category' => 'Checklist',
        ],
        [
            'id' => 'doc_boq_price',
            'code' => 'DOC-BOQ-01',
            'title' => 'Indicative Pricing Schedule & BOQ',
            'malay_ref' => 'Tawaran Harga Indikatif & Pecahan BOQ',
            'desc' => 'Formatted itemized Bill of Quantities pricing schedule for vendor net price quotation.',
            'category' => 'Pricing',
        ],
        [
            'id' => 'doc_tech_spec',
            'code' => 'DOC-SPEC-01',
            'title' => 'Technical Specification Schedule',
            'malay_ref' => 'Jadual Spesifikasi Teknikal Lengkap',
            'desc' => 'Detailed 3-level technical specification requirements schedule and evaluation compliance criteria.',
            'category' => 'Specification',
        ],
        [
            'id' => 'doc_terms_cond',
            'code' => 'DOC-TNC-01',
            'title' => 'General Terms & Conditions',
            'malay_ref' => 'Syarat-syarat Am Perolehan',
            'desc' => 'General procurement regulations, bidder eligibility rules, submission guidelines, and legal terms.',
            'category' => 'Legal',
        ],
        [
            'id' => 'doc_tender_stmt',
            'code' => 'DOC-STT-01',
            'title' => 'Supplier Tender Statement',
            'malay_ref' => 'Kenyataan Tawaran Pembekal',
            'desc' => 'Formal tender offer declaration statement to be signed by supplier authorized director.',
            'category' => 'Declaration',
        ],
        [
            'id' => 'doc_bidder_decl',
            'code' => 'DOC-IP-01',
            'title' => 'Sample Bidder Declaration Letter (Integrity Pact)',
            'malay_ref' => 'Sampel Surat Akuan Pembida (Pakatan Integriti)',
            'desc' => 'Mandatory Anti-Corruption Integrity Pact declaration letter sample for submitting suppliers.',
            'category' => 'Declaration',
        ],
        [
            'id' => 'doc_loa_sample',
            'code' => 'DOC-LOA-01',
            'title' => 'Sample Letter of Acceptance (LOA)',
            'malay_ref' => 'Sampel Surat Setuju Terima (SST)',
            'desc' => 'Official sample letter of acceptance issued by government/agency to the appointed winning supplier.',
            'category' => 'Contract',
        ],
    ];

    // Technical Checklist Items (Item #1 is Technical Specification)
    public array $technicalChecklist = [
        [
            'id' => 'tech_1',
            'title' => '1. Borang Spesifikasi Teknikal (Technical Specification Sheet)',
            'desc' => 'Dokumen spesifikasi teknikal terperinci mengikut struktur 3-lapisan (Rujuk Tab Technical Specification).',
            'input_type' => 'file_download_upload',
            'template_filename' => 'Spesifikasi_Teknikal_Lengkap_V1.pdf',
            'is_required' => true,
            'weightage' => 30.0,
            'allowed_extensions' => '.pdf,.docx',
            'status' => 'pending',
            'value' => null,
            'is_primary_link' => true,
            'target_tab' => 'technical-spec',
        ],
        [
            'id' => 'tech_2',
            'title' => 'Katalog Teknis & Brosur Pengeluar',
            'desc' => 'Muat naik katalog teknikal atau brosur rasmi pengeluar bagi setiap barangan/perkhidmatan yang ditawarkan.',
            'input_type' => 'file_upload',
            'template_filename' => null,
            'is_required' => true,
            'weightage' => 20.0,
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
            'weightage' => 20.0,
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
            'weightage' => 15.0,
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
            'weightage' => 15.0,
            'allowed_extensions' => null,
            'status' => 'pending',
            'value' => null,
        ],
    ];

    // Financial Checklist Items (Item #1 is Financial Pricelist)
    public array $financialChecklist = [
        [
            'id' => 'fin_1',
            'title' => '1. Jadual Harga Ringkasan & Pecahan BOQ (Financial Pricelist Schedule)',
            'desc' => 'Jadual pecahan harga dan tawaran bersih kewangan bagi setiap item BOQ (Rujuk Tab Financial Pricelist).',
            'input_type' => 'file_download_upload',
            'template_filename' => 'Templat_Jadual_Harga_BOQ.xlsx',
            'is_required' => true,
            'weightage' => 35.0,
            'allowed_extensions' => '.xlsx,.pdf',
            'status' => 'pending',
            'value' => null,
            'is_primary_link' => true,
            'target_tab' => 'financial-pricelist',
        ],
        [
            'id' => 'fin_2',
            'title' => 'Penyata Bank 3 Bulan Terkini',
            'desc' => 'Muat naik penyata akaun bank syarikat yang telah disahkan bagi 3 bulan terkini.',
            'input_type' => 'file_upload',
            'template_filename' => null,
            'is_required' => true,
            'weightage' => 25.0,
            'allowed_extensions' => '.pdf',
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
            'weightage' => 20.0,
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
            'weightage' => 10.0,
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
            'weightage' => 10.0,
            'allowed_extensions' => '.pdf',
            'status' => 'pending',
            'value' => null,
        ],
    ];

    // Technical Specification (3-Layer Nested Structure)
    public array $technicalSpecs = [
        [
            'id' => 'spec_cat_1',
            'level' => 1,
            'code' => '1.0',
            'name' => 'INFRASTRUKTUR PELAYAN & HARDWARE',
            'desc' => 'Spesifikasi fizikal pelayan, pemproses, dan memori utama.',
            'type' => 'category',
            'weightage' => 40.0,
            'children' => [
                [
                    'id' => 'spec_subcat_1_1',
                    'level' => 2,
                    'code' => '1.1',
                    'name' => 'Unit Pemprosesan & Memori Utama',
                    'desc' => 'Spesifikasi teras pemprosesan CPU & RAM.',
                    'type' => 'subcategory',
                    'weightage' => 25.0,
                    'children' => [
                        [
                            'id' => 'spec_item_1_1_1',
                            'level' => 3,
                            'code' => '1.1.1',
                            'name' => 'Pemproses CPU (Processor Architecture)',
                            'desc' => 'Minimum Dual Intel Xeon Scalable 3.2GHz (Min 32 Cores/64 Threads).',
                            'type' => 'text',
                            'weightage' => 15.0,
                            'max_mark' => 15,
                        ],
                        [
                            'id' => 'spec_item_1_1_2',
                            'level' => 3,
                            'code' => '1.1.2',
                            'name' => 'Kapasiti Memori Utama (RAM)',
                            'desc' => 'Minimum 128GB DDR5 ECC Registered RAM (Boleh dinaik taraf ke 512GB).',
                            'type' => 'number',
                            'weightage' => 10.0,
                            'max_mark' => 10,
                        ],
                    ],
                ],
                [
                    'id' => 'spec_subcat_1_2',
                    'level' => 2,
                    'code' => '1.2',
                    'name' => 'Sistem Storan & Controller (Storage System)',
                    'desc' => 'Spesifikasi pemacu NVMe/SSD & RAID Controller.',
                    'type' => 'subcategory',
                    'weightage' => 15.0,
                    'children' => [
                        [
                            'id' => 'spec_item_1_2_1',
                            'level' => 3,
                            'code' => '1.2.1',
                            'name' => 'Kapasiti Storan Enterprise NVMe SSD',
                            'desc' => 'Minimum 4x 1.92TB Enterprise NVMe SSD RAID 10.',
                            'type' => 'text',
                            'weightage' => 15.0,
                            'max_mark' => 15,
                        ],
                    ],
                ],
            ],
        ],
        [
            'id' => 'spec_cat_2',
            'level' => 1,
            'code' => '2.0',
            'name' => 'PERISIAN DAN PERLESENAN (SOFTWARE & LICENSING)',
            'desc' => 'Perlesenan sistem pengoperasian, virtualisasi, dan keselamatan.',
            'type' => 'category',
            'weightage' => 35.0,
            'children' => [
                [
                    'id' => 'spec_subcat_2_1',
                    'level' => 2,
                    'code' => '2.1',
                    'name' => 'Sistem Pengoperasian & Virtualisasi',
                    'desc' => 'Lesen Enterprise OS & Hypervisor.',
                    'type' => 'subcategory',
                    'weightage' => 20.0,
                    'children' => [
                        [
                            'id' => 'spec_item_2_1_1',
                            'level' => 3,
                            'code' => '2.1.1',
                            'name' => 'Lesen Red Hat Enterprise Linux / Windows Server Enterprise',
                            'desc' => 'Lesen rasmi berserta sokongan kemas kini 3 tahun.',
                            'type' => 'boolean',
                            'weightage' => 20.0,
                            'max_mark' => 20,
                        ],
                    ],
                ],
            ],
        ],
        [
            'id' => 'spec_cat_3',
            'level' => 1,
            'code' => '3.0',
            'name' => 'SOKONGAN & PERKHIDMATAN (SUPPORT & SLA)',
            'desc' => 'Syarat perkhidmatan sokongan teknikal dan latihan.',
            'type' => 'category',
            'weightage' => 25.0,
            'children' => [
                [
                    'id' => 'spec_subcat_3_1',
                    'level' => 2,
                    'code' => '3.1',
                    'name' => 'Penyelenggaraan & Masa Tindak Balas SLA',
                    'desc' => 'Jadual sokongan dan masa tindak balas kerosakan.',
                    'type' => 'subcategory',
                    'weightage' => 25.0,
                    'children' => [
                        [
                            'id' => 'spec_item_3_1_1',
                            'level' => 3,
                            'code' => '3.1.1',
                            'name' => 'Masa Tindak Balas Tapak 24x7 (On-Site Response Time)',
                            'desc' => 'Masa tindak balasan kerosakan kritikal dalam tempoh 4 jam.',
                            'type' => 'choice',
                            'weightage' => 25.0,
                            'max_mark' => 25,
                        ],
                    ],
                ],
            ],
        ],
    ];

    // Financial Pricelist (Bill of Quantities / BOQ Schedule)
    public array $financialPricelist = [
        [
            'id' => 'price_1',
            'item_code' => 'BOQ-01',
            'name' => 'Bekalan & Pemasangan Pelayan Enterprise High Performance',
            'uom' => 'Unit',
            'qty' => 2,
            'est_unit_price' => 45000.00,
            'weightage' => 40.0,
        ],
        [
            'id' => 'price_2',
            'item_code' => 'BOQ-02',
            'name' => 'Perlesenan Sistem Pengoperasian Enterprise (3 Tahun)',
            'uom' => 'Lesen',
            'qty' => 2,
            'est_unit_price' => 12000.00,
            'weightage' => 20.0,
        ],
        [
            'id' => 'price_3',
            'item_code' => 'BOQ-03',
            'name' => 'Perkhidmatan Konfigurasi & Migrasi Data Rangkaian',
            'uom' => 'Pakej',
            'qty' => 1,
            'est_unit_price' => 15000.00,
            'weightage' => 20.0,
        ],
        [
            'id' => 'price_4',
            'item_code' => 'BOQ-04',
            'name' => 'Latihan Pentadbiran Sistem & Penyelenggaraan SLA 24x7',
            'uom' => 'Sesi',
            'qty' => 2,
            'est_unit_price' => 7500.00,
            'weightage' => 20.0,
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

    public function toggleEvaluationLock(): void
    {
        $this->isEvaluationUnlocked = !$this->isEvaluationUnlocked;
        if ($this->isEvaluationUnlocked) {
            session()->flash('success', 'Akses tab penilaian telah dibuka menerusi Notis Mesyuarat.');
        } else {
            session()->flash('success', 'Akses tab penilaian telah dikunci semula.');
        }
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

    // Modal Handlers
    public function openAddItemModal(string $checklistType): void
    {
        $this->newItemChecklistType = $checklistType;
        $this->newItemTitle = '';
        $this->newItemDesc = '';
        $this->newItemInputType = 'file_upload';
        $this->newItemTemplateFilename = '';
        $this->newItemWeightage = 15.0;
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
            'weightage' => (float) $this->newItemWeightage,
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

    // Technical Spec Modal Handlers
    public function openAddTechSpecModal(int $level = 1, string $parentId = ''): void
    {
        $this->newSpecLevel = $level;
        $this->newSpecParentId = $parentId;
        $this->newSpecCode = '';
        $this->newSpecName = '';
        $this->newSpecDesc = '';
        $this->newSpecType = $level === 3 ? 'text' : ($level === 1 ? 'category' : 'subcategory');
        $this->newSpecWeightage = 10.0;
        $this->showAddTechSpecModal = true;
    }

    public function closeAddTechSpecModal(): void
    {
        $this->showAddTechSpecModal = false;
    }

    public function saveTechSpecItem(): void
    {
        if (trim($this->newSpecName) === '') {
            $this->addError('newSpecName', 'Specification item name is required.');
            return;
        }

        $newItem = [
            'id' => 'spec_' . time(),
            'level' => $this->newSpecLevel,
            'code' => $this->newSpecCode ?: ($this->newSpecLevel === 1 ? '4.0' : '4.1'),
            'name' => $this->newSpecName,
            'desc' => $this->newSpecDesc,
            'type' => $this->newSpecType,
            'weightage' => (float) $this->newSpecWeightage,
            'max_mark' => $this->newSpecLevel === 3 ? (int) $this->newSpecWeightage : null,
            'children' => [],
        ];

        if ($this->newSpecLevel === 1) {
            $this->technicalSpecs[] = $newItem;
        } elseif ($this->newSpecLevel === 2) {
            foreach ($this->technicalSpecs as &$cat) {
                if ($cat['id'] === $this->newSpecParentId) {
                    $cat['children'][] = $newItem;
                    break;
                }
            }
        } elseif ($this->newSpecLevel === 3) {
            foreach ($this->technicalSpecs as &$cat) {
                foreach ($cat['children'] as &$sub) {
                    if ($sub['id'] === $this->newSpecParentId) {
                        $sub['children'][] = $newItem;
                        break 2;
                    }
                }
            }
        }

        $this->showAddTechSpecModal = false;
        session()->flash('success', 'Technical specification item added.');
    }

    public function deleteTechSpecNode(string $id): void
    {
        $this->technicalSpecs = array_values(array_filter($this->technicalSpecs, function($cat) use ($id) {
            if ($cat['id'] === $id) return false;
            $cat['children'] = array_values(array_filter($cat['children'], function($sub) use ($id) {
                if ($sub['id'] === $id) return false;
                $sub['children'] = array_values(array_filter($sub['children'], fn($item) => $item['id'] !== $id));
                return true;
            }));
            return true;
        }));

        session()->flash('success', 'Specification item deleted.');
    }

    // Financial Pricelist Modal Handlers
    public function openAddPricelistModal(): void
    {
        $this->newPriceCode = 'BOQ-0' . (count($this->financialPricelist) + 1);
        $this->newPriceName = '';
        $this->newPriceUom = 'Unit';
        $this->newPriceQty = 1;
        $this->newPriceEstUnitPrice = 0.0;
        $this->newPriceWeightage = 10.0;
        $this->showAddPricelistModal = true;
    }

    public function closeAddPricelistModal(): void
    {
        $this->showAddPricelistModal = false;
    }

    public function savePricelistItem(): void
    {
        if (trim($this->newPriceName) === '') {
            $this->addError('newPriceName', 'Item description is required.');
            return;
        }

        $this->financialPricelist[] = [
            'id' => 'price_' . time(),
            'item_code' => $this->newPriceCode ?: 'BOQ-' . rand(10, 99),
            'name' => $this->newPriceName,
            'uom' => $this->newPriceUom,
            'qty' => (int) $this->newPriceQty,
            'est_unit_price' => (float) $this->newPriceEstUnitPrice,
            'weightage' => (float) $this->newPriceWeightage,
        ];

        $this->showAddPricelistModal = false;
        session()->flash('success', 'Pricelist item added.');
    }

    public function deletePricelistItem(string $id): void
    {
        $this->financialPricelist = array_values(array_filter($this->financialPricelist, fn($item) => $item['id'] !== $id));
        session()->flash('success', 'Pricelist item removed.');
    }

    // Procurement Document Preview Modal Handler
    public function openProcurementDocModal(string $docId): void
    {
        foreach ($this->procurementDocuments as $doc) {
            if ($doc['id'] === $docId) {
                $this->activeProcurementDoc = $doc;
                break;
            }
        }
        $this->showProcurementDocModal = true;
    }

    public function closeProcurementDocModal(): void
    {
        $this->showProcurementDocModal = false;
        $this->activeProcurementDoc = null;
    }

    // Meeting Notice Modal Handlers
    public function openMeetingNoticeModal(): void
    {
        $this->noticeCommitteeType = 'JK Spesifikasi';
        $this->noticeSubject = 'Mesyuarat Penyediaan Spesifikasi & Semakan Checklist';
        $this->noticeVenue = 'Bilik Mesyuarat Utama / Google Meet';
        $this->noticeStartAt = date('Y-m-d\TH:i', strtotime('+2 days 09:00'));
        $this->noticeEndAt = date('Y-m-d\TH:i', strtotime('+2 days 12:30'));
        $this->noticeAgenda = 'Memuktamadkan Spesifikasi Teknikal (3-Lapisan), Jadual Harga BOQ, Technical/Financial Checklists, dan Scoring.';
        $this->showMeetingNoticeModal = true;
    }

    public function closeMeetingNoticeModal(): void
    {
        $this->showMeetingNoticeModal = false;
    }

    public function saveMeetingNotice(): void
    {
        if (trim($this->noticeSubject) === '') {
            $this->addError('noticeSubject', 'Subject is required.');
            return;
        }

        $focusMap = [
            'JK Spesifikasi' => ['Technical Checklist', 'Financial Checklist', 'Technical Specification', 'Financial Pricelist', 'Scoring'],
            'JK Penilaian Teknikal' => ['Technical Checklist', 'Technical Specification'],
            'JK Penilaian Kewangan' => ['Financial Checklist', 'Financial Pricelist', 'Scoring'],
            'JK Pembuka Tawaran' => ['Technical Checklist', 'Financial Checklist'],
            'JK Lembaga Perolehan' => ['Scoring', 'Document for Procurement'],
        ];

        $this->meetingNotices[] = [
            'id' => 'notice_' . time(),
            'code' => 'NOTIS-' . strtoupper(str_replace(' ', '', $this->noticeCommitteeType)) . '-' . date('Y') . '/' . (count($this->meetingNotices) + 1),
            'committee_type' => $this->noticeCommitteeType,
            'subject' => $this->noticeSubject,
            'venue' => $this->noticeVenue,
            'start_at' => date('d M Y h:i A', strtotime($this->noticeStartAt)),
            'end_at' => date('d M Y h:i A', strtotime($this->noticeEndAt)),
            'agenda' => $this->noticeAgenda,
            'status' => 'sent',
            'recipients' => ['Appointed Committee Members of ' . $this->noticeCommitteeType],
            'focus_tabs' => $focusMap[$this->noticeCommitteeType] ?? ['Committee'],
        ];

        $this->isEvaluationUnlocked = true; // Auto-unlock evaluation tabs upon issuing meeting notice
        $this->showMeetingNoticeModal = false;
        session()->flash('success', 'Notis Mesyuarat ' . $this->noticeCommitteeType . ' telah diterbitkan. Tab Penilaian kini dibuka!');
    }

    public function sendMeetingNotice(string $noticeId): void
    {
        foreach ($this->meetingNotices as &$n) {
            if ($n['id'] === $noticeId) {
                $n['status'] = 'sent';
                $this->isEvaluationUnlocked = true;
                session()->flash('success', 'Notis mesyuarat ' . $n['code'] . ' telah dihantar dan tab penilaian telah dibuka!');
                break;
            }
        }
    }

    // Supplier Action Modal
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

    {{-- ── COMPACT & CLEAN ACQUISITION HEADER CARD ── --}}
    <x-card class="!p-4 sm:!p-5">
        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
            
            {{-- Left Content Area --}}
            <div class="space-y-2 min-w-0 flex-1">
                {{-- Compact Top Metadata Badges Strip --}}
                <div class="flex items-center gap-2 flex-wrap text-xs">
                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 font-mono font-bold border border-emerald-200 dark:border-emerald-800/40 shrink-0">
                        {{ $acquisition->project_number }}
                    </span>

                    @if($acquisition->tender_number)
                        <span class="px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-mono text-[11px] border border-zinc-200 dark:border-zinc-700 shrink-0">
                            Tender: {{ $acquisition->tender_number }}
                        </span>
                    @endif

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

                    @if($acquisition->siling_price !== null)
                        <span class="px-2.5 py-0.5 rounded-md bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-mono font-bold text-xs">
                            RM {{ number_format((float) $acquisition->siling_price, 2) }}
                        </span>
                    @endif
                </div>

                {{-- Project Title (Responsive & Truncation Handling for Long Malay Titles) --}}
                <div class="space-y-1">
                    <h1 class="text-base sm:text-lg font-bold text-zinc-900 dark:text-zinc-100 tracking-tight leading-snug {{ $expandFullTitle ? '' : 'line-clamp-2' }}">
                        {{ $acquisition->project_name }}
                    </h1>

                    @if(strlen($acquisition->project_name) > 90)
                        <button
                            wire:click="$toggle('expandFullTitle')"
                            class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 hover:underline cursor-pointer"
                        >
                            <span>{{ $expandFullTitle ? '▲ Tunjukkan Tajuk Ringkas' : '▼ Baca Tajuk Penuh Perolehan' }}</span>
                        </button>
                    @endif
                </div>

                {{-- Agency & Officer Subline --}}
                <div class="flex items-center gap-4 text-xs text-zinc-500 dark:text-zinc-400 flex-wrap pt-0.5">
                    @if($acquisition->agency)
                        <span class="flex items-center gap-1 font-medium">
                            <x-heroicon-o-building-office-2 class="w-3.5 h-3.5 text-zinc-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            {{ $acquisition->agency->name }}
                        </span>
                    @endif

                    @if($acquisition->user_id)
                        <span class="flex items-center gap-1">
                            <x-heroicon-o-user class="w-3.5 h-3.5 text-zinc-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Pegawai: <strong>{{ \App\Models\User::find($acquisition->user_id)?->name ?? 'Ditugaskan' }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            {{-- Right Compact Action Toolbar --}}
            <div class="flex items-center gap-2 shrink-0 self-start pt-1 lg:pt-0">
                @if($acquisition->status)
                    @foreach($acquisition->status->transitionableStateInstances() as $targetState)
                        <x-button 
                            variant="primary" 
                            size="sm" 
                            wire:click="transitionTo('{{ addslashes(get_class($targetState)) }}')" 
                            wire:loading.attr="disabled"
                            wire:target="transitionTo('{{ addslashes(get_class($targetState)) }}')"
                        >
                            <x-heroicon-o-arrow-right-circle class="w-3.5 h-3.5 mr-1 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            <span wire:loading.remove wire:target="transitionTo('{{ addslashes(get_class($targetState)) }}')">
                                {{ $targetState->label() }}
                            </span>
                            <span wire:loading wire:target="transitionTo('{{ addslashes(get_class($targetState)) }}')">
                                ...
                            </span>
                        </x-button>
                    @endforeach
                @endif

                @if($activeTab === 'project-info')
                    @if(!$isEditing)
                        <x-button variant="outline" size="sm" wire:click="enableEdit" class="cursor-pointer">
                            <x-heroicon-o-pencil class="w-3.5 h-3.5 mr-1 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Edit
                        </x-button>
                    @else
                        <x-button variant="outline" size="sm" wire:click="cancelEdit" class="cursor-pointer">
                            Cancel
                        </x-button>
                        <x-button variant="primary" size="sm" wire:click="save" wire:loading.attr="disabled" wire:target="save">
                            <span wire:loading.remove wire:target="save">
                                Save
                            </span>
                            <span wire:loading wire:target="save">
                                ...
                            </span>
                        </x-button>
                    @endif
                @endif

                <a href="{{ route('acquisition') }}">
                    <x-button variant="secondary" size="sm" class="cursor-pointer">
                        <x-heroicon-o-arrow-left class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        Back
                    </x-button>
                </a>
            </div>
        </div>
    </x-card>

    {{-- ── DE-CLUTTERED SEGMENTED TAB NAVIGATION BAR ── --}}
    <div class="space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-2 rounded-2xl bg-zinc-100/80 dark:bg-zinc-900/80 border border-zinc-200/80 dark:border-zinc-800">
            
            {{-- Segment 1: Main Management --}}
            <div class="flex items-center gap-1.5 overflow-x-auto">
                <span class="px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 shrink-0">
                    Main:
                </span>
                
                <button
                    wire:click="setTab('project-info')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap cursor-pointer transition-all {{ $activeTab === 'project-info' ? 'bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200' }}"
                >
                    <x-heroicon-o-document-text class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    Project Info
                </button>

                <button
                    wire:click="setTab('committee')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap cursor-pointer transition-all {{ $activeTab === 'committee' ? 'bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200' }}"
                >
                    <x-heroicon-o-user-group class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    Committee & Notices
                </button>

                <button
                    wire:click="setTab('documents')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap cursor-pointer transition-all {{ $activeTab === 'documents' ? 'bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200' }}"
                >
                    <x-heroicon-o-printer class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    Printable Docs
                </button>
            </div>

            {{-- Segment 2: Evaluation Suite (With Lock Status 🔒) --}}
            <div class="flex items-center gap-1.5 overflow-x-auto pt-2 md:pt-0 border-t md:border-t-0 border-zinc-200 dark:border-zinc-800">
                <div class="flex items-center gap-1 shrink-0">
                    <span class="px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                        Evaluation Suite:
                    </span>
                    <button wire:click="toggleEvaluationLock" class="text-xs font-mono font-semibold px-2 py-0.5 rounded-md cursor-pointer transition-colors {{ $isEvaluationUnlocked ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 border border-emerald-200 dark:border-emerald-800/40' : 'bg-rose-50 dark:bg-rose-950/40 text-rose-600 border border-rose-200 dark:border-rose-800/40' }}" title="Click to toggle lock state for testing">
                        {{ $isEvaluationUnlocked ? 'Unlocked 🔓' : 'Locked 🔒' }}
                    </button>
                </div>

                @foreach([
                    ['technical-checklist', 'Tech Checklist', 'clipboard-document-check'],
                    ['financial-checklist', 'Fin Checklist', 'banknotes'],
                    ['technical-spec', 'Tech Specs', 'cpu-chip'],
                    ['financial-pricelist', 'Fin Pricelist', 'calculator'],
                    ['scoring', 'Scoring', 'chart-bar'],
                ] as [$tKey, $tLabel, $tIcon])
                    <button
                        wire:click="setTab('{{ $tKey }}')"
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap cursor-pointer transition-all {{ $activeTab === $tKey ? 'bg-emerald-600 text-white shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200' }}"
                    >
                        <span>{{ $tLabel }}</span>
                        @if(!$isEvaluationUnlocked)
                            <x-heroicon-o-lock-closed class="w-3 h-3 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" />
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── TAB CONTENT ── --}}

    {{-- ════ LOCKED ACCESS NOTICE CARD FOR EVALUATION TABS ════ --}}
    @if(in_array($activeTab, ['technical-checklist', 'financial-checklist', 'technical-spec', 'financial-pricelist', 'scoring']) && !$isEvaluationUnlocked)
        <x-card class="text-center py-12">
            <div class="max-w-md mx-auto space-y-4">
                <div class="w-16 h-16 rounded-full bg-rose-50 dark:bg-rose-950/40 text-rose-500 border border-rose-200 dark:border-rose-800/50 flex items-center justify-center mx-auto">
                    <x-heroicon-o-lock-closed class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                </div>

                <div class="space-y-1">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">
                        Akses Tab Penilaian Dikunci (Restricted Access)
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                        Tab modul penilaian ini dikunci sehingga <strong>Notis Mesyuarat (JK Spesifikasi / JK Penilaian)</strong> diterbitkan secara rasmi kepada ahli jawatankuasa yang dilantik.
                    </p>
                </div>

                <div class="p-3.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200 dark:border-zinc-800 text-xs text-zinc-600 dark:text-zinc-300 font-mono">
                    Status: Notis Mesyuarat Perlu Diterbitkan Terlebih Dahulu
                </div>

                <div class="pt-2 flex items-center justify-center gap-3">
                    <button wire:click="setTab('committee')" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 transition-colors">
                        <x-heroicon-o-user-group class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        Terbitkan Notis di Tab Committee
                    </button>

                    <button wire:click="toggleEvaluationLock" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white transition-colors shadow-xs cursor-pointer">
                        <x-heroicon-o-lock-open class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        Buka Kunci Akses (Officer Override)
                    </button>
                </div>
            </div>
        </x-card>
    @endif

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

    {{-- ════ TAB 2: COMMITTEE & MEETING NOTICES ════ --}}
    @if($activeTab === 'committee')
        <x-card>
            <div class="space-y-8">
                {{-- Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-zinc-100 dark:border-zinc-800">
                    <div>
                        <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                            <x-heroicon-o-user-group class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Acquisition Evaluation Committee & Meeting Notices
                        </h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                            Committee setup, appointed evaluation members, and official Meeting Notices (Notis Mesyuarat).
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <x-button variant="primary" size="sm" wire:click="openMeetingNoticeModal" class="cursor-pointer">
                            <x-heroicon-o-envelope class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Issue Meeting Notice
                        </x-button>
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

                {{-- ── MEETING NOTICES SECTION (NOTIS MESYUARAT) ── --}}
                <div class="space-y-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                                <x-heroicon-o-bell-alert class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                Meeting Notices Schedule & Member Notifications
                            </h4>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                Issued meeting notices grouped by committee type (e.g. JK Spesifikasi, JK Penilaian Teknikal).
                            </p>
                        </div>

                        <span class="px-2.5 py-1 rounded-xl text-xs font-mono font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                            {{ count($meetingNotices) }} Notices Issued
                        </span>
                    </div>

                    <div class="space-y-4">
                        @foreach($meetingNotices as $n)
                            <div class="p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                                    <div class="flex items-center gap-3">
                                        <span class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white font-mono text-xs font-bold">
                                            {{ $n['code'] }}
                                        </span>
                                        <x-badge variant="primary">{{ $n['committee_type'] }}</x-badge>
                                        @if($n['status'] === 'sent')
                                            <x-badge variant="success" pill>Notice Issued & Unlocked 🔓</x-badge>
                                        @else
                                            <x-badge variant="warning" pill>Draft Notice 🔒</x-badge>
                                        @endif
                                    </div>

                                    <div class="text-xs font-mono text-zinc-500 flex items-center gap-1.5">
                                        <x-heroicon-o-clock class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                        <span>{{ $n['start_at'] }} — {{ $n['end_at'] }}</span>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <h5 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ $n['subject'] }}</h5>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400"><strong class="text-zinc-800 dark:text-zinc-200">Venue:</strong> {{ $n['venue'] }}</p>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400"><strong class="text-zinc-800 dark:text-zinc-200">Agenda Focus:</strong> {{ $n['agenda'] }}</p>
                                </div>

                                {{-- Focus Tabs Links --}}
                                <div class="flex items-center gap-2 flex-wrap text-xs">
                                    <span class="text-zinc-400 font-medium">Unlocked Scope:</span>
                                    @foreach($n['focus_tabs'] as $tabName)
                                        <span class="px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 font-mono text-xs border border-indigo-200 dark:border-indigo-800/40">
                                            {{ $tabName }}
                                        </span>
                                    @endforeach
                                </div>

                                {{-- Recipient Officers --}}
                                <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between gap-4 text-xs">
                                    <div class="flex items-center gap-1.5 text-zinc-500">
                                        <x-heroicon-o-paper-airplane class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                        <span>Notified Members: <strong>{{ implode(', ', $n['recipients']) }}</strong></span>
                                    </div>

                                    @if($n['status'] === 'draft')
                                        <x-button variant="primary" size="sm" wire:click="sendMeetingNotice('{{ $n['id'] }}')">
                                            Send Notice & Unlock Tabs
                                        </x-button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </x-card>
    @endif

    {{-- ════ TAB 3: TECHNICAL CHECKLIST ════ --}}
    @if($activeTab === 'technical-checklist' && $isEvaluationUnlocked)
        @php
            $totalTechChecklistWeightage = array_reduce($technicalChecklist, fn($acc, $i) => $acc + ($i['weightage'] ?? 0), 0);
        @endphp
        <x-card>
            <div class="space-y-6">
                {{-- Clean Header with Total Weightage --}}
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

                            {{-- Total Weightage Badge --}}
                            <span class="px-2.5 py-1 rounded-xl text-xs font-mono font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50">
                                Total Weightage: {{ $totalTechChecklistWeightage }}%
                            </span>
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                            Configure required technical submission documents. <strong>Item #1 is linked directly to Technical Specification</strong>.
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
                                    <th scope="col" class="px-4 py-3.5 text-center text-xs font-semibold text-zinc-500 uppercase tracking-wider">Weightage (%)</th>
                                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Requirement</th>
                                    <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold text-zinc-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 text-sm">
                                @foreach($technicalChecklist as $index => $item)
                                    <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/20 transition-colors {{ !empty($item['is_primary_link']) ? 'bg-emerald-50/30 dark:bg-emerald-950/10' : '' }}">
                                        <td class="px-4 py-3.5 font-mono text-xs font-bold text-zinc-400">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3.5">
                                            <div class="flex items-center gap-2">
                                                <h4 class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm">{{ $item['title'] }}</h4>
                                                @if(!empty($item['is_primary_link']))
                                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-600 text-white">Primary Item #1</span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $item['desc'] }}</p>

                                            @if(!empty($item['is_primary_link']))
                                                <div class="mt-1.5">
                                                    <button wire:click="setTab('technical-spec')" class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline cursor-pointer">
                                                        <x-heroicon-o-cpu-chip class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                                        Urutkan / Lihat Tab Technical Specification →
                                                    </button>
                                                </div>
                                            @elseif($item['template_filename'])
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
                                        <td class="px-4 py-3.5 whitespace-nowrap text-center font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                            {{ $item['weightage'] ?? 0 }}%
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
                                            @if(empty($item['is_primary_link']))
                                                <button
                                                    wire:click="deleteChecklistItem('technical', '{{ $item['id'] }}')"
                                                    class="p-1.5 rounded-lg text-zinc-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 cursor-pointer transition-colors"
                                                    title="Delete item"
                                                >
                                                    <x-heroicon-o-trash class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                                </button>
                                            @else
                                                <span class="text-xs text-zinc-400 italic">Protected</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    {{-- ── SUPPLIER PREVIEW MODE ── --}}
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
    @if($activeTab === 'financial-checklist' && $isEvaluationUnlocked)
        @php
            $totalFinChecklistWeightage = array_reduce($financialChecklist, fn($acc, $i) => $acc + ($i['weightage'] ?? 0), 0);
        @endphp
        <x-card>
            <div class="space-y-6">
                {{-- Clean Header with Total Weightage --}}
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

                            {{-- Total Weightage Badge --}}
                            <span class="px-2.5 py-1 rounded-xl text-xs font-mono font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50">
                                Total Weightage: {{ $totalFinChecklistWeightage }}%
                            </span>
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                            Configure required financial submission documents. <strong>Item #1 is linked directly to Financial Pricelist (BOQ)</strong>.
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
                                    <th scope="col" class="px-4 py-3.5 text-center text-xs font-semibold text-zinc-500 uppercase tracking-wider">Weightage (%)</th>
                                    <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Requirement</th>
                                    <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold text-zinc-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 text-sm">
                                @foreach($financialChecklist as $index => $item)
                                    <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/20 transition-colors {{ !empty($item['is_primary_link']) ? 'bg-emerald-50/30 dark:bg-emerald-950/10' : '' }}">
                                        <td class="px-4 py-3.5 font-mono text-xs font-bold text-zinc-400">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3.5">
                                            <div class="flex items-center gap-2">
                                                <h4 class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm">{{ $item['title'] }}</h4>
                                                @if(!empty($item['is_primary_link']))
                                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-600 text-white">Primary Item #1</span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $item['desc'] }}</p>

                                            @if(!empty($item['is_primary_link']))
                                                <div class="mt-1.5">
                                                    <button wire:click="setTab('financial-pricelist')" class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline cursor-pointer">
                                                        <x-heroicon-o-calculator class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                                        Urutkan / Lihat Tab Financial Pricelist (BOQ) →
                                                    </button>
                                                </div>
                                            @elseif($item['template_filename'])
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
                                        <td class="px-4 py-3.5 whitespace-nowrap text-center font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                            {{ $item['weightage'] ?? 0 }}%
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
                                            @if(empty($item['is_primary_link']))
                                                <button
                                                    wire:click="deleteChecklistItem('financial', '{{ $item['id'] }}')"
                                                    class="p-1.5 rounded-lg text-zinc-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 cursor-pointer transition-colors"
                                                    title="Delete item"
                                                >
                                                    <x-heroicon-o-trash class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                                </button>
                                            @else
                                                <span class="text-xs text-zinc-400 italic">Protected</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    {{-- ── SUPPLIER PREVIEW MODE ── --}}
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

    {{-- ════ TAB 5: TECHNICAL SPECIFICATION (3-LAYER NESTED HIERARCHY) ════ --}}
    @if($activeTab === 'technical-spec' && $isEvaluationUnlocked)
        @php
            $totalTechSpecWeightage = array_reduce($technicalSpecs, fn($acc, $cat) => $acc + ($cat['weightage'] ?? 0), 0);
        @endphp
        <x-card>
            <div class="space-y-6">
                {{-- Header with Total Weightage --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-zinc-100 dark:border-zinc-800">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                                <x-heroicon-o-cpu-chip class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                Technical Specification & Evaluation Criteria
                            </h3>

                            {{-- Total Weightage Badge --}}
                            <span class="px-2.5 py-1 rounded-xl text-xs font-mono font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50">
                                Total Weightage: {{ $totalTechSpecWeightage }}%
                            </span>
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                            Hierarchical 3-level technical breakdown (Category → Subcategory → Item Specification) for supplier technical scoring.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <x-button variant="primary" size="sm" wire:click="openAddTechSpecModal(1)" class="cursor-pointer">
                            <x-heroicon-o-plus class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Add Level 1 Category
                        </x-button>
                    </div>
                </div>

                {{-- 3-Layer Nested Tree Render --}}
                <div class="space-y-4">
                    @foreach($technicalSpecs as $cat)
                        {{-- Level 1: Category --}}
                        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/60 dark:bg-zinc-900/60 overflow-hidden shadow-xs">
                            <div class="p-4 bg-zinc-100/80 dark:bg-zinc-800/60 flex items-center justify-between gap-4 border-b border-zinc-200/80 dark:border-zinc-800">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="px-2 py-0.5 rounded-lg bg-emerald-600 text-white font-mono text-xs font-bold">
                                        {{ $cat['code'] }}
                                    </span>
                                    <div class="min-w-0">
                                        <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-tight">{{ $cat['name'] }}</h4>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $cat['desc'] }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 shrink-0">
                                    <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-1 rounded-lg border border-emerald-200 dark:border-emerald-800/50">
                                        Weight: {{ $cat['weightage'] }}%
                                    </span>
                                    <button
                                        wire:click="openAddTechSpecModal(2, '{{ $cat['id'] }}')"
                                        class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 text-zinc-700 dark:text-zinc-300 transition-colors cursor-pointer"
                                    >
                                        + Subcategory (L2)
                                    </button>
                                    <button wire:click="deleteTechSpecNode('{{ $cat['id'] }}')" class="p-1 rounded-lg text-zinc-400 hover:text-rose-600 cursor-pointer">
                                        <x-heroicon-o-trash class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                    </button>
                                </div>
                            </div>

                            {{-- Level 2: Subcategory --}}
                            <div class="p-4 space-y-4">
                                @foreach($cat['children'] as $sub)
                                    <div class="pl-4 border-l-2 border-emerald-500/40 dark:border-emerald-500/30 space-y-3">
                                        <div class="flex items-center justify-between gap-4 p-3 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <span class="px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 font-mono text-xs font-semibold border border-indigo-200 dark:border-indigo-800/40">
                                                    {{ $sub['code'] }}
                                                </span>
                                                <div>
                                                    <h5 class="text-xs font-bold text-zinc-800 dark:text-zinc-200">{{ $sub['name'] }}</h5>
                                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $sub['desc'] }}</p>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2 shrink-0">
                                                <span class="text-xs font-mono font-medium text-zinc-500">Weight: {{ $sub['weightage'] }}%</span>
                                                <button
                                                    wire:click="openAddTechSpecModal(3, '{{ $sub['id'] }}')"
                                                    class="px-2 py-1 rounded-lg text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/40 hover:bg-emerald-100 transition-colors cursor-pointer"
                                                >
                                                    + Spec Item (L3)
                                                </button>
                                                <button wire:click="deleteTechSpecNode('{{ $sub['id'] }}')" class="p-1 rounded-lg text-zinc-400 hover:text-rose-600 cursor-pointer">
                                                    <x-heroicon-o-trash class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Level 3: Specification Sub-item --}}
                                        <div class="pl-4 space-y-2">
                                            @foreach($sub['children'] as $item)
                                                <div class="p-3 rounded-xl bg-zinc-50/70 dark:bg-zinc-900/40 border border-zinc-200/60 dark:border-zinc-800/60 flex items-center justify-between gap-3 text-xs">
                                                    <div class="flex items-start gap-2 min-w-0">
                                                        <span class="font-mono font-bold text-zinc-400 shrink-0">{{ $item['code'] }}</span>
                                                        <div class="min-w-0">
                                                            <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $item['name'] }}</div>
                                                            <div class="text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $item['desc'] }}</div>
                                                        </div>
                                                    </div>

                                                    <div class="flex items-center gap-3 shrink-0">
                                                        <x-badge variant="info" pill>Type: {{ ucfirst($item['type']) }}</x-badge>
                                                        <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">Max Mark: {{ $item['max_mark'] }}</span>
                                                        <button wire:click="deleteTechSpecNode('{{ $item['id'] }}')" class="text-zinc-400 hover:text-rose-600 cursor-pointer">
                                                            <x-heroicon-o-trash class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-card>
    @endif

    {{-- ════ TAB 6: FINANCIAL PRICELIST (BILL OF QUANTITIES) ════ --}}
    @if($activeTab === 'financial-pricelist' && $isEvaluationUnlocked)
        @php
            $totalPricelistWeightage = array_reduce($financialPricelist, fn($acc, $i) => $acc + ($i['weightage'] ?? 0), 0);
        @endphp
        <x-card>
            <div class="space-y-6">
                {{-- Header & Summary --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-zinc-100 dark:border-zinc-800">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                                <x-heroicon-o-calculator class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                Financial Pricelist & Bill of Quantities (BOQ)
                            </h3>

                            {{-- Total Weightage Badge --}}
                            <span class="px-2.5 py-1 rounded-xl text-xs font-mono font-bold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50">
                                Total Weightage: {{ $totalPricelistWeightage }}%
                            </span>
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                            Itemized financial schedule for vendor bidding & price score evaluation.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <x-button variant="primary" size="sm" wire:click="openAddPricelistModal" class="cursor-pointer">
                            <x-heroicon-o-plus class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Add Pricelist Item
                        </x-button>
                    </div>
                </div>

                {{-- Summary Stats --}}
                @php
                    $totalEstBudget = array_reduce($financialPricelist, fn($acc, $i) => $acc + ($i['qty'] * $i['est_unit_price']), 0);
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/80 dark:border-zinc-800">
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Total Estimated Budget</span>
                        <div class="mt-1 text-base font-mono font-bold text-emerald-600 dark:text-emerald-400">
                            RM {{ number_format($totalEstBudget, 2) }}
                        </div>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Total Pricelist Items</span>
                        <div class="mt-1 text-base font-mono font-bold text-zinc-900 dark:text-zinc-100">
                            {{ count($financialPricelist) }} Items
                        </div>
                    </div>
                    <div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Total Pricelist Weightage</span>
                        <div class="mt-1 text-base font-mono font-bold text-indigo-600 dark:text-indigo-400">
                            {{ $totalPricelistWeightage }}%
                        </div>
                    </div>
                </div>

                {{-- Pricelist Schedule Table --}}
                <div class="overflow-x-auto rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs">
                    <table class="min-w-full divide-y divide-zinc-100 dark:divide-zinc-800 text-sm">
                        <thead class="bg-zinc-50/70 dark:bg-zinc-800/40">
                            <tr>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Item Ref</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Item Description</th>
                                <th scope="col" class="px-4 py-3.5 text-center text-xs font-semibold text-zinc-500 uppercase tracking-wider">Qty / UOM</th>
                                <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold text-zinc-500 uppercase tracking-wider">Est. Unit Price (RM)</th>
                                <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold text-zinc-500 uppercase tracking-wider">Est. Subtotal (RM)</th>
                                <th scope="col" class="px-4 py-3.5 text-center text-xs font-semibold text-zinc-500 uppercase tracking-wider">Score Weight</th>
                                <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold text-zinc-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach($financialPricelist as $item)
                                @php $subtotal = $item['qty'] * $item['est_unit_price']; @endphp
                                <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/20 transition-colors">
                                    <td class="px-4 py-4 font-mono font-bold text-xs text-emerald-600 dark:text-emerald-400 whitespace-nowrap">{{ $item['item_code'] }}</td>
                                    <td class="px-4 py-4">
                                        <div class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm">{{ $item['name'] }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-center whitespace-nowrap font-mono text-xs font-medium">
                                        {{ $item['qty'] }} {{ $item['uom'] }}
                                    </td>
                                    <td class="px-4 py-4 text-right whitespace-nowrap font-mono text-xs font-medium">
                                        RM {{ number_format($item['est_unit_price'], 2) }}
                                    </td>
                                    <td class="px-4 py-4 text-right whitespace-nowrap font-mono text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                        RM {{ number_format($subtotal, 2) }}
                                    </td>
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        <x-badge variant="info">{{ $item['weightage'] }}%</x-badge>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-right">
                                        <button
                                            wire:click="deletePricelistItem('{{ $item['id'] }}')"
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
            </div>
        </x-card>
    @endif

    {{-- ════ TAB 7: SCORING (OVERALL EVALUATION MATRIX) ════ --}}
    @if($activeTab === 'scoring' && $isEvaluationUnlocked)
        @php
            $sumTechChecklist = array_reduce($technicalChecklist, fn($a, $i) => $a + ($i['weightage'] ?? 0), 0);
            $sumFinChecklist = array_reduce($financialChecklist, fn($a, $i) => $a + ($i['weightage'] ?? 0), 0);
            $sumTechSpecs = array_reduce($technicalSpecs, fn($a, $c) => $a + ($c['weightage'] ?? 0), 0);
            $sumFinPricelist = array_reduce($financialPricelist, fn($a, $i) => $a + ($i['weightage'] ?? 0), 0);
        @endphp
        <x-card>
            <div class="space-y-6">
                {{-- Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-zinc-100 dark:border-zinc-800">
                    <div>
                        <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                            <x-heroicon-o-chart-bar class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Overall Scoring & Evaluation Matrix
                        </h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                            Configure evaluation weightage ratios, technical passing threshold marks, and overall scoring calculation.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50 font-mono text-xs font-bold">
                            Ratio: {{ $techWeightageRatio }}% Technical : {{ $finWeightageRatio }}% Financial
                        </span>
                    </div>
                </div>

                {{-- Weightage Configuration Controls --}}
                <div class="p-5 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/80 dark:border-zinc-800 space-y-4">
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                        Configuration Ratio & Threshold Marks
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="space-y-1.5">
                            <x-label for="techWeightageRatio">Nisbah Pemberat Teknikal (%)</x-label>
                            <input id="techWeightageRatio" type="number" min="0" max="100" wire:model.live="techWeightageRatio" class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 font-mono font-bold text-emerald-600 dark:text-emerald-400">
                        </div>

                        <div class="space-y-1.5">
                            <x-label for="finWeightageRatio">Nisbah Pemberat Kewangan (%)</x-label>
                            <input id="finWeightageRatio" type="number" min="0" max="100" wire:model.live="finWeightageRatio" class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 font-mono font-bold text-indigo-600 dark:text-indigo-400">
                        </div>

                        <div class="space-y-1.5">
                            <x-label for="passingTechMark">Lulus Min. Teknikal (%)</x-label>
                            <input id="passingTechMark" type="number" min="0" max="100" wire:model.live="passingTechMark" class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 font-mono font-bold text-amber-600 dark:text-amber-400">
                        </div>
                    </div>
                </div>

                {{-- All 4 Evaluation Tables Weightage Summary Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Card 1: Technical Checklist --}}
                    <div class="p-4 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 space-y-2">
                        <div class="flex items-center justify-between text-xs font-semibold text-zinc-500">
                            <span>Technical Checklist</span>
                            <x-heroicon-o-clipboard-document-check class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        </div>
                        <div class="text-xl font-bold font-mono text-zinc-900 dark:text-zinc-100">
                            {{ $sumTechChecklist }}%
                        </div>
                        <p class="text-xs text-zinc-400 font-mono">{{ count($technicalChecklist) }} Checklist Items</p>
                    </div>

                    {{-- Card 2: Technical Specs --}}
                    <div class="p-4 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 space-y-2">
                        <div class="flex items-center justify-between text-xs font-semibold text-zinc-500">
                            <span>Technical Specs (3-Layer)</span>
                            <x-heroicon-o-cpu-chip class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        </div>
                        <div class="text-xl font-bold font-mono text-emerald-600 dark:text-emerald-400">
                            {{ $sumTechSpecs }}%
                        </div>
                        <p class="text-xs text-zinc-400 font-mono">{{ count($technicalSpecs) }} Categories</p>
                    </div>

                    {{-- Card 3: Financial Checklist --}}
                    <div class="p-4 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 space-y-2">
                        <div class="flex items-center justify-between text-xs font-semibold text-zinc-500">
                            <span>Financial Checklist</span>
                            <x-heroicon-o-banknotes class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        </div>
                        <div class="text-xl font-bold font-mono text-zinc-900 dark:text-zinc-100">
                            {{ $sumFinChecklist }}%
                        </div>
                        <p class="text-xs text-zinc-400 font-mono">{{ count($financialChecklist) }} Checklist Items</p>
                    </div>

                    {{-- Card 4: Financial Pricelist --}}
                    <div class="p-4 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 space-y-2">
                        <div class="flex items-center justify-between text-xs font-semibold text-zinc-500">
                            <span>Financial Pricelist (BOQ)</span>
                            <x-heroicon-o-calculator class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        </div>
                        <div class="text-xl font-bold font-mono text-indigo-600 dark:text-indigo-400">
                            {{ $sumFinPricelist }}%
                        </div>
                        <p class="text-xs text-zinc-400 font-mono">{{ count($financialPricelist) }} BOQ Items</p>
                    </div>
                </div>

                {{-- Full Scoring Calculation Formula Table --}}
                <div class="overflow-x-auto rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs">
                    <table class="min-w-full divide-y divide-zinc-100 dark:divide-zinc-800 text-sm">
                        <thead class="bg-zinc-50/70 dark:bg-zinc-800/40">
                            <tr>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Evaluation Domain</th>
                                <th scope="col" class="px-4 py-3.5 text-left text-xs font-semibold text-zinc-500 uppercase tracking-wider">Primary Item Component</th>
                                <th scope="col" class="px-4 py-3.5 text-center text-xs font-semibold text-zinc-500 uppercase tracking-wider">Internal List Weightage</th>
                                <th scope="col" class="px-4 py-3.5 text-center text-xs font-semibold text-zinc-500 uppercase tracking-wider">Overall Domain Ratio</th>
                                <th scope="col" class="px-4 py-3.5 text-right text-xs font-semibold text-zinc-500 uppercase tracking-wider">Weighted Max Score</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            <tr>
                                <td class="px-4 py-4 font-semibold text-emerald-700 dark:text-emerald-400">Teknikal (Technical)</td>
                                <td class="px-4 py-4">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100">Technical Specification (3-Layer) & Technical Checklist</div>
                                    <div class="text-xs text-zinc-500">Item #1: Borang Spesifikasi Teknikal Sheet</div>
                                </td>
                                <td class="px-4 py-4 text-center font-mono font-bold">{{ $sumTechSpecs }}%</td>
                                <td class="px-4 py-4 text-center font-mono font-bold text-emerald-600">{{ $techWeightageRatio }}%</td>
                                <td class="px-4 py-4 text-right font-mono font-bold text-emerald-600">{{ $techWeightageRatio }} Marks</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-4 font-semibold text-indigo-700 dark:text-indigo-400">Kewangan (Financial)</td>
                                <td class="px-4 py-4">
                                    <div class="font-medium text-zinc-900 dark:text-zinc-100">Financial Pricelist (BOQ Schedule) & Financial Checklist</div>
                                    <div class="text-xs text-zinc-500">Item #1: Jadual Harga Ringkasan & Pecahan BOQ</div>
                                </td>
                                <td class="px-4 py-4 text-center font-mono font-bold">{{ $sumFinPricelist }}%</td>
                                <td class="px-4 py-4 text-center font-mono font-bold text-indigo-600">{{ $finWeightageRatio }}%</td>
                                <td class="px-4 py-4 text-right font-mono font-bold text-indigo-600">{{ $finWeightageRatio }} Marks</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-zinc-50/80 dark:bg-zinc-800/60 font-bold">
                            <tr>
                                <td colspan="3" class="px-4 py-3.5 text-zinc-900 dark:text-zinc-100">JUMLAH MARKAH KESELURUHAN (TOTAL OVERALL SCORE)</td>
                                <td class="px-4 py-3.5 text-center font-mono text-emerald-600 dark:text-emerald-400">100.0%</td>
                                <td class="px-4 py-3.5 text-right font-mono text-emerald-600 dark:text-emerald-400">100.0 MARKS</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </x-card>
    @endif

    {{-- ════ TAB 8: DOCUMENT FOR PROCUREMENT (PRINTABLE DOCUMENTS) ════ --}}
    @if($activeTab === 'documents')
        <x-card>
            <div class="space-y-6">
                {{-- Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-zinc-100 dark:border-zinc-800">
                    <div>
                        <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                            <x-heroicon-o-printer class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Document for Procurement
                        </h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                            Official printable document suite formatted for supplier printout, submission, and contract execution.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800/50 font-mono text-xs font-bold">
                            8 Printable Procurement Templates Available
                        </span>
                    </div>
                </div>

                {{-- Document Cards Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach($procurementDocuments as $doc)
                        <div class="p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-xs hover:border-emerald-500/40 transition-colors flex flex-col justify-between space-y-4">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="px-2.5 py-0.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 font-mono text-xs font-bold text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700">
                                        {{ $doc['code'] }}
                                    </span>
                                    <x-badge variant="info" pill>{{ $doc['category'] }}</x-badge>
                                </div>

                                <h4 class="text-base font-bold text-zinc-900 dark:text-zinc-100 leading-snug">
                                    {{ $doc['title'] }}
                                </h4>

                                <div class="text-xs text-emerald-600 dark:text-emerald-400 font-mono italic">
                                    Rujukan: {{ $doc['malay_ref'] }}
                                </div>

                                <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                    {{ $doc['desc'] }}
                                </p>
                            </div>

                            <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between gap-3">
                                <span class="text-xs text-zinc-400 font-mono flex items-center gap-1">
                                    <x-heroicon-o-document-arrow-down class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                    PDF & Print Ready
                                </span>

                                <x-button variant="primary" size="sm" wire:click="openProcurementDocModal('{{ $doc['id'] }}')" class="cursor-pointer">
                                    <x-heroicon-o-printer class="w-3.5 h-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                                    Preview & Print Document
                                </x-button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-card>
    @endif

    {{-- ════ CREATE MEETING NOTICE MODAL (COMMITTEE TAB) ════ --}}
    @if($showMeetingNoticeModal)
        <div x-data class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-zinc-950/60 backdrop-blur-sm" wire:click="closeMeetingNoticeModal"></div>

            <div class="relative z-10 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 p-6 w-full max-w-xl space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <x-heroicon-o-envelope class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        Terbitkan Notis Mesyuarat Jawatankuasa (Meeting Notice)
                    </h3>
                    <button wire:click="closeMeetingNoticeModal" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                        <x-heroicon-o-x-mark class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </button>
                </div>

                <form wire:submit="saveMeetingNotice" class="space-y-4">
                    <div>
                        <x-label for="noticeCommitteeType" :required="true">Kumpulan Jawatankuasa (Committee Type)</x-label>
                        <select id="noticeCommitteeType" wire:model="noticeCommitteeType" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100">
                            <option value="JK Spesifikasi">JK Spesifikasi (Technical & Financial Specs, BOQ, Checklists, Scoring)</option>
                            <option value="JK Penilaian Teknikal">JK Penilaian Teknikal (Pemarkahan Teknikal Pembekal)</option>
                            <option value="JK Penilaian Kewangan">JK Penilaian Kewangan (Pemarkahan Kewangan BOQ)</option>
                            <option value="JK Pembuka Tawaran">JK Pembuka Tawaran (Peti Cadangan Tender)</option>
                            <option value="JK Lembaga Perolehan">JK Lembaga Perolehan (Keputusan Kelulusan Final)</option>
                        </select>
                    </div>

                    <div>
                        <x-label for="noticeSubject" :required="true">Tajuk / Subjek Notis Mesyuarat</x-label>
                        <input id="noticeSubject" type="text" wire:model="noticeSubject" placeholder="e.g. Mesyuarat Penyediaan Spesifikasi & Semakan Checklist" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100">
                        @error('noticeSubject') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-label for="noticeStartAt" :required="true">Tarikh & Masa Mula (Start)</x-label>
                            <input id="noticeStartAt" type="datetime-local" wire:model="noticeStartAt" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100">
                        </div>
                        <div>
                            <x-label for="noticeEndAt" :required="true">Tarikh & Masa Tamat (End)</x-label>
                            <input id="noticeEndAt" type="datetime-local" wire:model="noticeEndAt" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100">
                        </div>
                    </div>

                    <div>
                        <x-label for="noticeVenue">Lokasi / Pautan Mesyuarat (Venue)</x-label>
                        <input id="noticeVenue" type="text" wire:model="noticeVenue" placeholder="e.g. Bilik Mesyuarat Utama / Google Meet Link" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100">
                    </div>

                    <div>
                        <x-label for="noticeAgenda">Agenda & Skop Perbincangan</x-label>
                        <textarea id="noticeAgenda" wire:model="noticeAgenda" rows="3" placeholder="Jelaskan skop tugas dan dokumen yang perlu disemak oleh ahli jawatankuasa..." class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                        <x-button variant="outline" size="sm" type="button" wire:click="closeMeetingNoticeModal">Batal</x-button>
                        <x-button variant="primary" size="sm" type="submit">Terbitkan & Hantar Notis</x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ════ PRINTABLE PROCUREMENT DOCUMENT PREVIEW MODAL ════ --}}
    @if($showProcurementDocModal && $activeProcurementDoc)
        <div x-data class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-zinc-950/70 backdrop-blur-sm" wire:click="closeProcurementDocModal"></div>

            <div class="relative z-10 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden">
                {{-- Modal Header --}}
                <div class="p-4 sm:p-5 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between gap-4 bg-zinc-50 dark:bg-zinc-900/90">
                    <div>
                        <span class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400">{{ $activeProcurementDoc['code'] }}</span>
                        <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">
                            {{ $activeProcurementDoc['title'] }}
                        </h3>
                    </div>

                    <div class="flex items-center gap-3">
                        <button
                            onclick="window.print()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white cursor-pointer transition-colors shadow-xs"
                        >
                            <x-heroicon-o-printer class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                            Print / Download PDF
                        </button>

                        <button wire:click="closeProcurementDocModal" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                            <x-heroicon-o-x-mark class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                        </button>
                    </div>
                </div>

                {{-- Printable Formatted Content Sheet --}}
                <div class="p-6 sm:p-10 overflow-y-auto space-y-8 bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 text-sm font-sans leading-relaxed">
                    {{-- Official Letterhead --}}
                    <div class="text-center space-y-2 pb-6 border-b-2 border-zinc-900 dark:border-zinc-100">
                        <div class="text-xs font-bold tracking-widest text-zinc-500 uppercase">KERAJAAN MALAYSIA / MALAYSIA GOVERNMENT</div>
                        <h2 class="text-lg font-bold uppercase tracking-tight">{{ $acquisition->agency?->name ?? 'JABATAN PEROLEHAN KERAJAAN' }}</h2>
                        <p class="text-xs text-zinc-500 font-mono">DOKUMEN RASMI PEROLEHAN / OFFICIAL PROCUREMENT DOCUMENTATION</p>
                    </div>

                    {{-- Document Reference Metadata --}}
                    <div class="grid grid-cols-2 gap-4 text-xs font-mono bg-zinc-50 dark:bg-zinc-900/60 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800">
                        <div>
                            <div><strong>Project Number:</strong> {{ $acquisition->project_number }}</div>
                            <div><strong>Tender Reference:</strong> {{ $acquisition->tender_number ?: 'TND-2026-N/A' }}</div>
                            <div><strong>Acquisition Type:</strong> {{ $acquisition->type ? ($acquisition->type instanceof \App\Enums\AcquisitionType ? $acquisition->type->value : $acquisition->type) : 'PEROLEHAN' }}</div>
                        </div>
                        <div class="text-right">
                            <div><strong>Document Ref:</strong> {{ $activeProcurementDoc['code'] }}</div>
                            <div><strong>Malay Title:</strong> {{ $activeProcurementDoc['malay_ref'] }}</div>
                            <div><strong>Date Generated:</strong> {{ date('d F Y') }}</div>
                        </div>
                    </div>

                    {{-- Dynamic Body Content per Document Type --}}
                    <div class="space-y-6">
                        <h3 class="text-center font-bold text-base uppercase underline underline-offset-4">
                            {{ $activeProcurementDoc['title'] }}
                        </h3>

                        @if($activeProcurementDoc['id'] === 'doc_tech_check')
                            <p class="text-xs text-zinc-600 dark:text-zinc-400">
                                This document serves as the official printable technical submission checklist required from suppliers bidding for project <strong>{{ $acquisition->project_name }}</strong>.
                            </p>
                            <table class="min-w-full divide-y divide-zinc-300 dark:divide-zinc-700 text-xs border border-zinc-200 dark:border-zinc-800">
                                <thead class="bg-zinc-100 dark:bg-zinc-800">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-bold">#</th>
                                        <th class="px-3 py-2 text-left font-bold">Requirement Title</th>
                                        <th class="px-3 py-2 text-left font-bold">Type</th>
                                        <th class="px-3 py-2 text-center font-bold">Mandatory</th>
                                        <th class="px-3 py-2 text-center font-bold">Supplier Verification Check</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                    @foreach($technicalChecklist as $idx => $tItem)
                                        <tr>
                                            <td class="px-3 py-2 font-mono font-bold">{{ $idx + 1 }}</td>
                                            <td class="px-3 py-2 font-semibold">{{ $tItem['title'] }}</td>
                                            <td class="px-3 py-2 uppercase">{{ $tItem['input_type'] }}</td>
                                            <td class="px-3 py-2 text-center font-bold">{{ $tItem['is_required'] ? 'YES' : 'NO' }}</td>
                                            <td class="px-3 py-2 text-center font-mono">[ &nbsp; ] Complete</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @elseif($activeProcurementDoc['id'] === 'doc_fin_check')
                            <p class="text-xs text-zinc-600 dark:text-zinc-400">
                                Official printable financial checklist schedule required for company financial audit and tax compliance.
                            </p>
                            <table class="min-w-full divide-y divide-zinc-300 dark:divide-zinc-700 text-xs border border-zinc-200 dark:border-zinc-800">
                                <thead class="bg-zinc-100 dark:bg-zinc-800">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-bold">#</th>
                                        <th class="px-3 py-2 text-left font-bold">Financial Requirement Title</th>
                                        <th class="px-3 py-2 text-left font-bold">Submission Format</th>
                                        <th class="px-3 py-2 text-center font-bold">Mandatory</th>
                                        <th class="px-3 py-2 text-center font-bold">Verification Check</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                    @foreach($financialChecklist as $idx => $fItem)
                                        <tr>
                                            <td class="px-3 py-2 font-mono font-bold">{{ $idx + 1 }}</td>
                                            <td class="px-3 py-2 font-semibold">{{ $fItem['title'] }}</td>
                                            <td class="px-3 py-2 uppercase">{{ $fItem['input_type'] }}</td>
                                            <td class="px-3 py-2 text-center font-bold">{{ $fItem['is_required'] ? 'YES' : 'NO' }}</td>
                                            <td class="px-3 py-2 text-center font-mono">[ &nbsp; ] Verified</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @elseif($activeProcurementDoc['id'] === 'doc_boq_price')
                            <p class="text-xs text-zinc-600 dark:text-zinc-400">
                                Itemized Bill of Quantities (BOQ) price submission form for project <strong>{{ $acquisition->project_name }}</strong>.
                            </p>
                            <table class="min-w-full divide-y divide-zinc-300 dark:divide-zinc-700 text-xs border border-zinc-200 dark:border-zinc-800">
                                <thead class="bg-zinc-100 dark:bg-zinc-800">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-bold">Item Ref</th>
                                        <th class="px-3 py-2 text-left font-bold">Item Description</th>
                                        <th class="px-3 py-2 text-center font-bold">Qty / UOM</th>
                                        <th class="px-3 py-2 text-right font-bold">Unit Price Quoted (RM)</th>
                                        <th class="px-3 py-2 text-right font-bold">Subtotal Quoted (RM)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 font-mono">
                                    @foreach($financialPricelist as $pItem)
                                        <tr>
                                            <td class="px-3 py-2 font-bold text-emerald-600">{{ $pItem['item_code'] }}</td>
                                            <td class="px-3 py-2 font-sans font-medium">{{ $pItem['name'] }}</td>
                                            <td class="px-3 py-2 text-center">{{ $pItem['qty'] }} {{ $pItem['uom'] }}</td>
                                            <td class="px-3 py-2 text-right">RM ________________</td>
                                            <td class="px-3 py-2 text-right">RM ________________</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="space-y-4 text-xs leading-relaxed text-zinc-700 dark:text-zinc-300">
                                <p>
                                    <strong>1. INTRODUCTORY CLAUSE:</strong> This document constitutes formal legal and administrative documentation governing the procurement project <strong>{{ $acquisition->project_name }}</strong> under project number <strong>{{ $acquisition->project_number }}</strong>.
                                </p>
                                <p>
                                    <strong>2. OBLIGATIONS & COMPLIANCE:</strong> The participating tenderer agrees to comply fully with all specified technical specifications, financial requirements, and anti-corruption integrity pact provisions as mandated by Government Procurement Instructions and Ministry of Finance Circulars.
                                </p>
                                <p>
                                    <strong>3. SUBMISSION DECLARATION:</strong> All information provided in this document is true, complete, and legally binding. Any false statements shall result in immediate disqualification and blacklisting from future government procurement activities.
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- Formal Signature Block --}}
                    <div class="pt-10 border-t border-zinc-200 dark:border-zinc-800 grid grid-cols-2 gap-8 text-xs">
                        <div class="space-y-12">
                            <div>
                                <p class="font-bold">Authorized Officer Signature & Chop</p>
                                <p class="text-zinc-500">Government / Procurement Department</p>
                            </div>
                            <div class="border-b border-zinc-400 w-48"></div>
                            <div>
                                <p><strong>Name:</strong> {{ $acquisition->user_id ? \App\Models\User::find($acquisition->user_id)?->name : 'Senior Procurement Officer' }}</p>
                                <p><strong>Date:</strong> ________________________</p>
                            </div>
                        </div>

                        <div class="space-y-12">
                            <div>
                                <p class="font-bold">Supplier Authorized Director Signature & Stamp</p>
                                <p class="text-zinc-500">Participating Vendor Company</p>
                            </div>
                            <div class="border-b border-zinc-400 w-48"></div>
                            <div>
                                <p><strong>Company Name:</strong> ________________________</p>
                                <p><strong>Date:</strong> ________________________</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ════ ADD TECHNICAL SPEC MODAL ════ --}}
    @if($showAddTechSpecModal)
        <div x-data class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-zinc-950/60 backdrop-blur-sm" wire:click="closeAddTechSpecModal"></div>

            <div class="relative z-10 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 p-6 w-full max-w-lg space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                        Tambah Spesifikasi Teknikal (Tahap {{ $newSpecLevel }})
                    </h3>
                    <button wire:click="closeAddTechSpecModal" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                        <x-heroicon-o-x-mark class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </button>
                </div>

                <form wire:submit="saveTechSpecItem" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-label for="newSpecCode" :required="true">Kod Item</x-label>
                            <input id="newSpecCode" type="text" wire:model="newSpecCode" placeholder="e.g. 1.0, 1.1, 1.1.1" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100">
                        </div>
                        <div>
                            <x-label for="newSpecWeightage">Pemberat (%) / Markah Maks</x-label>
                            <input id="newSpecWeightage" type="number" step="0.5" wire:model="newSpecWeightage" placeholder="10.0" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100">
                        </div>
                    </div>

                    <div>
                        <x-label for="newSpecName" :required="true">Nama Spesifikasi / Kategori</x-label>
                        <input id="newSpecName" type="text" wire:model="newSpecName" placeholder="Tajuk spesifikasi teknikal..." class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100">
                        @error('newSpecName') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <x-label for="newSpecDesc">Keterangan / Terperinci</x-label>
                        <textarea id="newSpecDesc" wire:model="newSpecDesc" rows="2" placeholder="Terangkan keperluan spesifikasi min..." class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100"></textarea>
                    </div>

                    @if($newSpecLevel === 3)
                        <div>
                            <x-label for="newSpecType">Jenis Penilaian</x-label>
                            <select id="newSpecType" wire:model="newSpecType" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100">
                                <option value="text">Teks / Penjelasan</option>
                                <option value="number">Nombor / Nilai Terukur</option>
                                <option value="boolean">Pematuhan Ya/Tidak</option>
                                <option value="choice">Pilihan Peringkat SLA</option>
                            </select>
                        </div>
                    @endif

                    <div class="flex justify-end gap-3 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                        <x-button variant="outline" size="sm" type="button" wire:click="closeAddTechSpecModal">Batal</x-button>
                        <x-button variant="primary" size="sm" type="submit">Simpan Spesifikasi</x-button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ════ ADD FINANCIAL PRICELIST MODAL ════ --}}
    @if($showAddPricelistModal)
        <div x-data class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-zinc-950/60 backdrop-blur-sm" wire:click="closeAddPricelistModal"></div>

            <div class="relative z-10 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 p-6 w-full max-w-lg space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                        Tambah Perkara Jadual Harga (BOQ)
                    </h3>
                    <button wire:click="closeAddPricelistModal" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                        <x-heroicon-o-x-mark class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" />
                    </button>
                </div>

                <form wire:submit="savePricelistItem" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-label for="newPriceCode" :required="true">Kod Rujukan BOQ</x-label>
                            <input id="newPriceCode" type="text" wire:model="newPriceCode" placeholder="BOQ-01" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100">
                        </div>
                        <div>
                            <x-label for="newPriceUom">Unit Ukuran (UOM)</x-label>
                            <input id="newPriceUom" type="text" wire:model="newPriceUom" placeholder="e.g. Unit, Lesen, Pakej" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100">
                        </div>
                    </div>

                    <div>
                        <x-label for="newPriceName" :required="true">Keterangan Barangan / Perkhidmatan</x-label>
                        <input id="newPriceName" type="text" wire:model="newPriceName" placeholder="Keterangan item jadual harga..." class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100">
                        @error('newPriceName') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <x-label for="newPriceQty">Kuantiti</x-label>
                            <input id="newPriceQty" type="number" min="1" wire:model="newPriceQty" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100">
                        </div>
                        <div>
                            <x-label for="newPriceEstUnitPrice">Harga Unit (RM)</x-label>
                            <input id="newPriceEstUnitPrice" type="number" step="0.01" wire:model="newPriceEstUnitPrice" placeholder="0.00" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100">
                        </div>
                        <div>
                            <x-label for="newPriceWeightage">Pemberat (%)</x-label>
                            <input id="newPriceWeightage" type="number" step="0.5" wire:model="newPriceWeightage" placeholder="10" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                        <x-button variant="outline" size="sm" type="button" wire:click="closeAddPricelistModal">Batal</x-button>
                        <x-button variant="primary" size="sm" type="submit">Simpan Item Harga</x-button>
                    </div>
                </form>
            </div>
        </div>
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

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-label for="newItemInputType" :required="true">Jenis Keperluan Input</x-label>
                            <select id="newItemInputType" wire:model.live="newItemInputType" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100">
                                <option value="file_upload">Muat Naik Fail</option>
                                <option value="file_download_upload">Muat Turun & Muat Naik</option>
                                <option value="text_input">Jawapan Teks</option>
                                <option value="number_input">Input Nombor</option>
                                <option value="boolean">Kotak Semak Pematuhan</option>
                            </select>
                        </div>
                        <div>
                            <x-label for="newItemWeightage">Pemberat (%)</x-label>
                            <input id="newItemWeightage" type="number" step="0.5" wire:model="newItemWeightage" placeholder="15" class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-sm p-2.5 text-zinc-900 dark:text-zinc-100">
                        </div>
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
