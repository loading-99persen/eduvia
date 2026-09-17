<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Minat;

class MinatSeeder extends Seeder
{
    public function run(): void
    {
        $minat = [
            'Teknologi',
            'Pemrograman',
            'Desain',
            'Bahasa',
            'Matematika',
            'Sains',
            'Bisnis',
            'Musik',
            'Olahraga',
            'Seni'
        ];

        foreach ($minat as $nama) {
            Minat::create([
                'nama_minat' => $nama
            ]);
        }
    }
}