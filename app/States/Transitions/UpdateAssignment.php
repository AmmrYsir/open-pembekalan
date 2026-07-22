<?php

namespace App\States\Transitions;

use App\Models\Acquisition;
use App\Models\Assignment;
use Spatie\ModelStates\DefaultTransition;

class UpdateAssignment extends DefaultTransition
{
    public function handle(): Acquisition
    {
        /** @var Acquisition $acquisition */
        $acquisition = parent::handle();
        dd($acquisition);

        $assignment = Assignment::where('acquisition_id', $acquisition->id)->first();

        return $acquisition;
    }
}
