<?php

namespace Database\Seeders;

use App\Models\Minat;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProfilSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'admin@ruangbelajar.com' => ['Administrator RuangBelajar', 'Umum', 'RuangBelajar', []],
            'user1@ruangbelajar.com' => ['Alya Putri', 'Mahasiswa', 'Universitas XYZ', ['Pemrograman', 'Database', 'Artificial Intelligence']],
            'user2@ruangbelajar.com' => ['Raka Mahendra', 'SMA/SMK', 'SMK Negeri 2', ['Pemrograman', 'Matematika']],
            'user3@ruangbelajar.com' => ['Dimas Prakoso', 'Mahasiswa', 'Universitas ABC', ['Artificial Intelligence', 'Sains', 'Statistika']],
        ];

        foreach ($data as $email => [$nama, $jenjang, $institusi, $minat]) {
            $user = User::where('email', $email)->first();

            if (!$user) {
                continue;
            }

            $user->profil()->updateOrCreate(
                ['id_user' => $user->id_user],
                [
                    'nama_lengkap'       => $nama,
                    'tingkat_pendidikan' => $jenjang,
                    'institusi'          => $institusi,
                    'bio'                => 'Sedang belajar dan senang berdiskusi di RuangBelajar.',
                    'preferensi_belajar' => 'diskusi',
                ]
            );

            if ($minat) {
                $ids = Minat::whereIn('nama_minat', $minat)->pluck('id_minat')->all();
                $user->minat()->sync($ids);
            }
        }
    }
}
