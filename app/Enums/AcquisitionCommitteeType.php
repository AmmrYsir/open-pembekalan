<?php

namespace App\Enums;

enum AcquisitionCommitteeType: string
{
    case PENILAIAN_SATU_PERINGKAT = 'PENILAIAN SATU PERINGKAT';
    case PENILAIAN_DUA_PERINGKAT = 'PENILAIAN DUA PERINGKAT';

	/**
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::PENILAIAN_SATU_PERINGKAT => 'PENILAIAN SATU PERINGKAT',
            self::PENILAIAN_DUA_PERINGKAT => 'PENILAIAN DUA PERINGKAT',
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
