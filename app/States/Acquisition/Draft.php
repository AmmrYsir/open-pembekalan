<?php

namespace App\States\Acquisition;

use App\States\AcquisitionState;

class Draft extends AcquisitionState
{
    public static string $name = 'DRAFT';

    public function label(): string
    {
        return 'DRAF';
    }

    public function color(): string
    {
        return 'slate';
    }
}
