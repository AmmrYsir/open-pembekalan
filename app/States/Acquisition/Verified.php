<?php

namespace App\States\Acquisition;

use App\States\AcquisitionState;

class Verified extends AcquisitionState
{
    public static string $name = 'VERIFIED';

    public function label(): string
    {
        return 'VERIFIED';
    }

    public function color(): string
    {
        return 'amber';
    }
}
