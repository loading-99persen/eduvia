<?php

namespace Database\Seeders;

use App\Models\Minat;
use Illuminate\Database\Seeder;

class MinatSeeder extends Seeder
{
    public function run(): void
    {
        $minat = [
            'Matematika',
            'Pemrograman',
            'Database',
            'Artificial Intelligence',
            'Bahasa Inggris',
            'Sains',
            'Desain',
            'Psikologi',
            'Bisnis',
            'Pengembangan diri',
            'Statistika',
            'Sejarah',
            'Teknologi',
            'Seni',
        ];

        foreach ($minat as $nama) {
            Minat::firstOrCreate(['nama_minat' => $nama]);
        }
    }
}
