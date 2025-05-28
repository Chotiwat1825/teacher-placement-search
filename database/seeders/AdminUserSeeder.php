<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'ผู้ดูแลระบบหลัก',
                'password' => Hash::make('password123'), // เปลี่ยนรหัสผ่านนี้ในระบบจริง
                'is_admin' => true,
                'email_verified_at' => now(),
                'profile_image' => null, // หรือใส่ path รูปตัวอย่างถ้ามี
            ],
        );
        $this->command->info('Admin user seeded successfully.');
    }
}
