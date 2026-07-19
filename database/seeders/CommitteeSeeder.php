<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;

use App\Models\Committee;
use Illuminate\Database\Seeder;

class CommitteeSeeder extends Seeder
{
    private array $committees = [
        [
            'slug' => 'jk-pendaftaran',
            'name' => 'AHLIJAWATANKUASA PENDAFTARAN',
            'position' => ['SETIAUSAHA', 'PENGERUSI', 'TIMBALAN PENGERUSI', 'AHLI'],
        ],
        [
            'slug' => 'jk-pengurusan',
            'name' => 'AHLIJAWATANKUASA PENGURUSAN',
            'position' => ['SETIAUSAHA', 'PENGERUSI', 'TIMBALAN PENGERUSI', 'AHLI'],
        ],
        [
            'slug' => 'jk-teknikal',
            'name' => 'AHLIJAWATANKUASA TEKNIKAL',
            'position' => ['SETIAUSAHA', 'PENGERUSI', 'TIMBALAN PENGERUSI', 'AHLI'],
        ],
        [
            'slug' => 'jk-kewangan',
            'name' => 'AHLIJAWATANKUASA KEWANGAN',
            'position' => ['SETIAUSAHA', 'PENGERUSI', 'TIMBALAN PENGERUSI', 'AHLI'],
        ],
        [
            'slug' => 'jk-keputusan',
            'name' => 'AHLIJAWATANKUASA KEPUTUSAN',
            'position' => ['SETIAUSAHA', 'PENGERUSI', 'TIMBALAN PENGERUSI', 'AHLI'],
        ],
    ];

    public function run(): void
    {
        DB::transaction(function () {   
            foreach ($this->committees as $committee) {
                Committee::create($committee);
            }
        }, attempts: 1);
    }
}
