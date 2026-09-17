<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // AKUN ADMIN
        User::create([
            'email' => 'admin@ruangbelajar.com',
            'password' => Hash::make('admin123'),
            'id_role' => 1,
            'status' => 'aktif'
        ]);

        // AKUN USER 1
        User::create([
            'email' => 'user1@ruangbelajar.com',
            'password' => Hash::make('user123'),
            'id_role' => 2,
            'status' => 'aktif'
        ]);

        // AKUN USER 2
        User::create([
            'email' => 'user2@ruangbelajar.com',
            'password' => Hash::make('user123'),
            'id_role' => 2,
            'status' => 'aktif'
        ]);
    }
}