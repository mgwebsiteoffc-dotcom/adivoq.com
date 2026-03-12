<?php
// database/seeders/AdminUserSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@adivoq.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'is_system_admin' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Admin user created: admin@adivoq.com / password');
    }
}