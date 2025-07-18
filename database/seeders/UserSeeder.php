<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Membuat pengguna dengan role 'admin'
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'), // Pastikan mengganti password ini
            'role' => 'admin',
        ]);

        // Membuat pengguna dengan role 'user'
        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'), // Pastikan mengganti password ini
            'role' => 'user',
        ]);
    }
}
