<?php

namespace App\Enums;

enum AcquisitionMethod: string
{
    case BEKALAN = 'BEKALAN';
    case PERKHIDMATAN = 'PERKHIDMATAN';
    case BEKALAN_DAN_PERKHIDMATAN = 'BEKALAN DAN PERKHIDMATAN';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
