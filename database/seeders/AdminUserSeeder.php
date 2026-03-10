<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        AdminUser::updateOrCreate(
            ['email' => 'admin@invoicehero.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
            ]
        );

        // Default expense categories (global)
        $categories = [
            'Equipment & Software', 'Camera & Gear', 'Editing Software',
            'Travel & Transport', 'Internet & Phone', 'Studio Rent',
            'Props & Materials', 'Marketing & Ads', 'Professional Services',
            'Insurance', 'Education & Training', 'Miscellaneous',
        ];

        foreach ($categories as $name) {
            ExpenseCategory::updateOrCreate(
                ['name' => $name, 'tenant_id' => null],
                ['description' => $name . ' expenses']
            );
        }
    }
}