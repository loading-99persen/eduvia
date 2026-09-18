<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $akun = [
            ['admin@ruangbelajar.com', 'admin123', 1],
            ['user1@ruangbelajar.com', 'user123', 2],
            ['user2@ruangbelajar.com', 'user123', 2],
            ['user3@ruangbelajar.com', 'user123', 2],
        ];

        foreach ($akun as [$email, $password, $role]) {
            User::firstOrCreate(
                ['email' => $email],
                [
                    'password' => Hash::make($password),
                    'id_role'  => $role,
                    'status'   => 'aktif',
                ]
            );
        }
    }
}
