<?php

namespace App\Enums;

enum AcquisitionType: string
{
    case SEBUTHARGA = 'SEBUTHARGA';
    case LEMBAGA_TENDER = 'LEMBAGA TENDER';

    public function label(): string
    {
        return match ($this) {
            self::SEBUTHARGA => 'Sebutharga',
            self::LEMBAGA_TENDER => 'Lembaga Tender',
        };
    }

    // public function color(): string
    // {
    //     return match($this) {};
    // }

    // Static method: Returns an array of values for select dropdowns
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
