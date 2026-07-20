<?php

namespace Database\Seeders;

use App\Models\Sequence;
use Illuminate\Database\Seeder;

class SequenceSeeder extends Seeder
{
    /** @var list<array{slug: string, name: string, format: string}> */
    private array $sequences = [
        [
            'slug' => 'project-number',
            'name' => 'Project Number',
            'format' => 'PN{Y}XXXXXX',
        ],
    ];

    public function run(): void
    {
        Sequence::upsert($this->sequences, ['slug'], ['name', 'format']);
    }
}
