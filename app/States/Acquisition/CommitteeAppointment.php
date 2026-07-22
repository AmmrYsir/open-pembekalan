<?php

namespace App\States\Acquisition;

use App\States\AcquisitionState;

class CommitteeAppointment extends AcquisitionState
{
    public static string $name = 'COMMITTEE APPOINTMENT';

    public function label(): string
    {
        return 'COMMITTEE APPOINTMENT';
    }

    public function color(): string
    {
        return 'indigo';
    }
}
