<?php

namespace App\States\Transitions;

use App\Models\Acquisition;
use App\Models\Assignment;
use Illuminate\Support\Str;
use Spatie\ModelStates\DefaultTransition;

class UpdateAssignment extends DefaultTransition
{
    public function handle(): Acquisition
    {
        /** @var Acquisition $acquisition */
        $acquisition = parent::handle();

        Assignment::updateOrCreate(
            ['acquisition_id' => $acquisition->id],
            [
                'uuid' => (string) Str::uuid(),
                'title' => $acquisition->status?->label() ?? '',
                'status' => $acquisition->status?->getValue(),
                'reference_no' => $acquisition->project_number,
                'assignable_type' => Acquisition::class,
                'assignable_id' => $acquisition->id,
            ]
        );

        return $acquisition;
    }
}
