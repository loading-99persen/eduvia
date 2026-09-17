<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create([
            'nama_role' => 'admin'
        ]);

        Role::create([
            'nama_role' => 'user'
        ]);
    }
}