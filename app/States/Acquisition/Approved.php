<?php

namespace App\States\Acquisition;

use App\States\AcquisitionState;

class Approved extends AcquisitionState
{
    public static string $name = 'APPROVED';

    public function label(): string
    {
        return 'APPROVED';
    }

    public function color(): string
    {
        return 'blue';
    }
}
