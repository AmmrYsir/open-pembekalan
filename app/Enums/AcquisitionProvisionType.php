<?php

namespace App\Enums;

enum AcquisitionProvisionType: string
{
	case LOCAL = "LOCAL";
	case STATE = "STATE";
	case FEDERAL = "FEDERAL";

	public function label(): string
    {
        return match ($this) {
            self::ONE_OFF => 'ONE_OFF',
            self::BERJADUAL => 'BERJADUAL',
        };
    }

    // public function color(): string
    // {
    //     return match($this) {};
    // }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
