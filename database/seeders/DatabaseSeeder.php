<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Database bersih: hanya 1 akun admin untuk login pertama.
        // Ubah email & password lewat menu Pengaturan setelah login.
        $email = env('ADMIN_EMAIL', 'admin@avatara.id');
        $password = env('ADMIN_PASSWORD', 'admin123');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin',
                'password' => Hash::make($password),
                'role' => Role::BOS,
                'is_active' => true,
            ],
        );
    }
}
