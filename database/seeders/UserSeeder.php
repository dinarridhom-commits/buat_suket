<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat akun admin default jika belum ada
        User::firstOrCreate(
            ['email' => 'admin@pu.go.id'],
            [
                'name' => 'Administrator TU',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        // Akun cadangan staff tata usaha
        User::firstOrCreate(
            ['email' => 'staff.tu@pu.go.id'],
            [
                'name' => 'Staf Tata Usaha',
                'password' => Hash::make('staff123'),
                'email_verified_at' => now(),
            ]
        );
    }
}
