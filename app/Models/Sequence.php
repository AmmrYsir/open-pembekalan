<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Sequence extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'format',
        'value',
        'total',
        'daily_reset',
        'monthly_reset',
        'yearly_reset',
        'is_active',
    ];

    protected $casts = [
        'daily_reset' => 'boolean',
        'monthly_reset' => 'boolean',
        'yearly_reset' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'current_sequence',
        'next_sequence',
    ];

    public function getCurrentSequenceAttribute(): string
    {
        return $this->formatSequence($this->value);
    }

    public function getNextSequenceAttribute(): string
    {
        return $this->formatSequence($this->value + 1);
    }

    protected function formatSequence(int $value): string
    {
        return Str::of($this->format)
            ->replace('{y}', now()->format('y'))
            ->replace('{Y}', now()->format('Y'))
            ->replace('{m}', str_pad((string) now()->month, 2, '0', STR_PAD_LEFT))
            ->replaceMatches('/X+/', fn ($matches) => str_pad((string) $value, strlen($matches[0]), '0', STR_PAD_LEFT))
            ->__toString();
    }
}
