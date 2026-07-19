<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Sequence;

class SequenceSeeder extends Seeder
{
	private array $sequences = [
		[
			'slug' => 'acquisition-number',
			'name' => 'Acquisition Number',
			'format' => 'A{YEAR}XXXXXX'
		]
	];	

    public function run(): void
    {
		Sequence::upsert($this->sequences, ['slug'], ['name', 'format']);
    }
}
