<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@test.com',
                'role' => UserRole::Admin,
            ],
            [
                'name' => 'Manager User',
                'email' => 'manager@test.com',
                'role' => UserRole::Manager,
            ],
            [
                'name' => 'Warehouse User',
                'email' => 'warehouse@test.com',
                'role' => UserRole::Warehouse,
            ],
            [
                'name' => 'Customer User',
                'email' => 'customer@test.com',
                'role' => UserRole::Customer,
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => $data['role'],
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}

