<?php

namespace Database\Seeders;

use App\Models\Chatroom;
use App\Models\Komunitas;
use App\Models\MemberKomunitas;
use App\Models\Post;
use App\Models\User;
use App\Models\Webinar;
use Illuminate\Database\Seeder;

/**
 * Data contoh supaya semua halaman user langsung terisi saat pertama dibuka.
 * Jalankan terpisah: php artisan db:seed --class=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $alya  = User::where('email', 'user1@ruangbelajar.com')->first();
        $raka  = User::where('email', 'user2@ruangbelajar.com')->first();
        $dimas = User::where('email', 'user3@ruangbelajar.com')->first();

        if (!$alya || !$raka || !$dimas) {
            return;
        }

        $daftar = [
            ['Matematika', 'Matematika', 'Diskusi soal, berbagi cara pengerjaan, dan belajar bersama dari aljabar sampai kalkulus.', $raka],
            ['Database', 'Database', 'Tempat membahas perancangan tabel, relasi, normalisasi, dan latihan query SQL.', $raka],
            ['Artificial Intelligence', 'AI', 'Mulai dari dasar machine learning sampai membedah paper terbaru, dibahas pelan-pelan.', $dimas],
            ['Web Development', 'Programming', 'Laravel, React, dan kawan-kawannya. Bawa error-mu ke sini, dibedah bareng.', $dimas],
            ['Bahasa Inggris', 'Bahasa', 'Latihan speaking mingguan, koreksi writing, dan persiapan TOEFL bareng.', $alya],
        ];

        foreach ($daftar as [$nama, $kategori, $deskripsi, $leader]) {
            $komunitas = Komunitas::firstOrCreate(
                ['nama_komunitas' => $nama],
                [
                    'id_leader'   => $leader->id_user,
                    'deskripsi'   => $deskripsi,
                    'kategori'    => $kategori,
                    'status'      => 'aktif',
                    'dibuat_pada' => now(),
                ]
            );

            Chatroom::firstOrCreate(
                ['id_komunitas' => $komunitas->id_komunitas],
                [
                    'nama'        => 'Chatroom ' . $komunitas->nama_komunitas,
                    'dibuat_pada' => now(),
                ]
            );

            MemberKomunitas::firstOrCreate(
                ['id_komunitas' => $komunitas->id_komunitas, 'id_user' => $leader->id_user],
                ['role' => 'leader', 'bergabung_pada' => now()]
            );

            foreach ([$alya, $raka, $dimas] as $u) {
                MemberKomunitas::firstOrCreate(
                    ['id_komunitas' => $komunitas->id_komunitas, 'id_user' => $u->id_user],
                    ['role' => 'member', 'bergabung_pada' => now()]
                );
            }
        }

        $webdev = Komunitas::where('nama_komunitas', 'Web Development')->first();
        $ai     = Komunitas::where('nama_komunitas', 'Artificial Intelligence')->first();
        $db     = Komunitas::where('nama_komunitas', 'Database')->first();

        $posts = [
            [$alya, $db, 'Rangkuman normalisasi database 1NF sampai 3NF yang aku pakai buat UAS kemarin. Silakan dipakai, kalau ada yang keliru tolong dikoreksi ya.'],
            [$dimas, $ai, 'Baru selesai bikin model klasifikasi pertama pakai scikit-learn. Ternyata bagian paling lama bukan modelnya, tapi bersih-bersih datanya.'],
            [$raka, $webdev, 'Kalau route Laravel sudah benar tapi tetap 404, biasanya cache route belum di-clear. Coba jalankan php artisan route:clear dulu sebelum debugging lebih jauh.'],
        ];

        foreach ($posts as [$user, $komunitas, $konten]) {
            if (!$komunitas) {
                continue;
            }

            Post::firstOrCreate(
                ['id_user' => $user->id_user, 'id_komunitas' => $komunitas->id_komunitas, 'konten' => $konten],
                ['dibuat_pada' => now()]
            );
        }

        if ($ai) {
            Webinar::firstOrCreate(
                ['judul' => 'Introduction to AI'],
                [
                    'id_leader'    => $dimas->id_user,
                    'id_komunitas' => $ai->id_komunitas,
                    'deskripsi'    => "Sesi pengantar untuk siapa pun yang baru mulai. Kita bahas istilah yang sering muncul, perbedaan machine learning dan deep learning, lalu melihat satu contoh kasus sederhana dari awal sampai selesai.",
                    'pembicara'    => 'Dimas Prakoso',
                    'tanggal'      => now()->addDays(4)->toDateString(),
                    'waktu'        => '19:00:00',
                    'kategori'     => 'AI',
                    'link_meeting' => 'https://meet.google.com/abc-defg-hij',
                    'status'       => 'akan_datang',
                    'dibuat_pada'  => now(),
                ]
            );
        }

        if ($webdev) {
            Webinar::firstOrCreate(
                ['judul' => 'Deploy Laravel ke VPS'],
                [
                    'id_leader'    => $dimas->id_user,
                    'id_komunitas' => $webdev->id_komunitas,
                    'deskripsi'    => 'Dari menyiapkan server sampai aplikasi bisa diakses lewat domain sendiri.',
                    'pembicara'    => 'Dimas Prakoso',
                    'tanggal'      => now()->addDays(9)->toDateString(),
                    'waktu'        => '19:30:00',
                    'kategori'     => 'Programming',
                    'link_meeting' => 'https://meet.google.com/xyz-1234-abc',
                    'status'       => 'akan_datang',
                    'dibuat_pada'  => now(),
                ]
            );
        }
    }
}