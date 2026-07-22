<?php

namespace App\Livewire\Forms;

use App\Enums\AcquisitionCommitteeType;
use App\Enums\AcquisitionMethod;
use App\Enums\AcquisitionType;
use App\Models\Acquisition;
use App\Models\Sequence;
use Livewire\Form;

class AcquisitionForm extends Form
{
    public ?Acquisition $acquisition = null;

    public string $type = '';

    public string $method = '';

    public string $project_number = '';

    public string $project_name = '';

    public ?string $status = 'DRAFT';

    public ?int $vot_type_id = null;

    public string $tender_number = '';

    public string $siling_price = '';

    public string $no_allocation_warrant = '';

    public ?int $agency_id = null;

    public ?int $subagency_id = null;

    public ?int $user_id = null;

    public bool $is_required_kbp = false;

    public bool $mof_required = false;

    public bool $cidb_required = false;

    public string $committee_type = '';

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string'],
            'method' => ['required', 'string'],
            'project_number' => ['required', 'string', 'max:100'],
            'project_name' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string'],
            'vot_type_id' => ['nullable', 'integer', 'exists:vot_types,id'],
            'tender_number' => ['nullable', 'string', 'max:100'],
            'siling_price' => ['nullable', 'numeric', 'min:0'],
            'no_allocation_warrant' => ['nullable', 'string', 'max:100'],
            'agency_id' => ['nullable', 'integer', 'exists:agencies,id'],
            'subagency_id' => ['nullable', 'integer', 'exists:subagencies,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'is_required_kbp' => ['boolean'],
            'mof_required' => ['boolean'],
            'cidb_required' => ['boolean'],
            'committee_type' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function validationAttributes(): array
    {
        return [
            'type' => 'Acquisition Type',
            'method' => 'Acquisition Method',
            'project_number' => 'Project Number',
            'project_name' => 'Project Name',
            'status' => 'Status',
            'vot_type_id' => 'VOT Type',
            'tender_number' => 'Tender Number',
            'siling_price' => 'Ceiling Price',
            'no_allocation_warrant' => 'Allocation Warrant No.',
            'agency_id' => 'Agency',
            'subagency_id' => 'Sub-Agency',
            'user_id' => 'Officer',
            'committee_type' => 'Committee Type',
        ];
    }

    public function fillFromModel(Acquisition $a): void
    {
        $this->acquisition = $a;

        $this->type = $a->type instanceof AcquisitionType ? $a->type->value : '';
        $this->method = $a->method instanceof AcquisitionMethod ? $a->method->value : '';
        $this->project_number = $a->project_number ?? '';
        $this->project_name = $a->project_name ?? '';
        $this->status = $a->status?->getValue() ?? 'DRAFT';
        $this->vot_type_id = $a->vot_type_id;
        $this->tender_number = $a->tender_number ?? '';
        $this->siling_price = $a->siling_price !== null ? (string) $a->siling_price : '';
        $this->no_allocation_warrant = $a->no_allocation_warrant ?? '';
        $this->agency_id = $a->agency_id;
        $this->subagency_id = $a->subagency_id;
        $this->is_required_kbp = (bool) $a->is_required_kbp;
        $this->mof_required = (bool) $a->mof_required;
        $this->cidb_required = (bool) $a->cidb_required;
        $this->committee_type = $a->committee_type instanceof AcquisitionCommitteeType ? $a->committee_type->value : ($a->committee_type ?? '');
        $this->user_id = $a->user_id;
    }

    /**
     * @return array<string, mixed>
     */
    public function getMappedData(): array
    {
        return [
            'type' => $this->type,
            'method' => $this->method,
            'project_number' => $this->project_number,
            'project_name' => $this->project_name,
            'status' => $this->status ?: 'DRAFT',
            'vot_type_id' => $this->vot_type_id,
            'tender_number' => $this->tender_number ?: null,
            'siling_price' => $this->siling_price !== '' ? (float) $this->siling_price : null,
            'no_allocation_warrant' => $this->no_allocation_warrant ?: null,
            'agency_id' => $this->agency_id,
            'subagency_id' => $this->subagency_id,
            'user_id' => $this->user_id,
            'is_required_kbp' => $this->is_required_kbp,
            'mof_required' => $this->mof_required,
            'cidb_required' => $this->cidb_required,
            'committee_type' => $this->committee_type ?: null,
        ];
    }

    public function store(): Acquisition
    {
        $this->validate();

        $data = $this->getMappedData();
        if (empty($data['status'])) {
            $data['status'] = 'DRAFT';
        }

        return Acquisition::create($data);
    }

    public function update(Acquisition $a): void
    {
        $this->validate();
        $a->update($this->getMappedData());
    }

    public function resetForm(): void
    {
        $this->acquisition = null;
        $this->type = '';
        $this->method = '';
        $this->project_number = Sequence::where('slug', 'project-number')->first()->next_sequence ?? '';
        $this->project_name = '';
        $this->status = 'DRAFT';
        $this->vot_type_id = null;
        $this->tender_number = '';
        $this->siling_price = '';
        $this->no_allocation_warrant = '';
        $this->agency_id = null;
        $this->subagency_id = null;
        $this->user_id = null;
        $this->is_required_kbp = false;
        $this->mof_required = false;
        $this->cidb_required = false;
        $this->committee_type = '';
    }
}
