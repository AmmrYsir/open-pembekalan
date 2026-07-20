<?php

namespace App\Enums;

enum AcquisitionMethod: string
{
    case BEKALAN = 'BEKALAN';
    case PERKHIDMATAN = 'PERKHIDMATAN';
    case BEKALAN_DAN_PERKHIDMATAN = 'BEKALAN DAN PERKHIDMATAN';

    public function label(): string
    {
        return match ($this) {
            self::BEKALAN => 'Bekalan',
            self::PERKHIDMATAN => 'Perkhidmatan',
            self::BEKALAN_DAN_PERKHIDMATAN => 'Bekalan dan Perkhidmatan',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
