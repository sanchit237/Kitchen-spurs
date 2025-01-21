<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\RoleEnum;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            // Admin Users
            [
                'name' => 'Admin User 1',
                'email' => 'admin1@example.com',
                'password' => Hash::make('adminpassword1'),
                'role' => RoleEnum::Admin->value,
            ],
            [
                'name' => 'Admin User 2',
                'email' => 'admin2@example.com',
                'password' => Hash::make('adminpassword2'),
                'role' => RoleEnum::Admin->value,
            ],

            // Author Users
            [
                'name' => 'Author User 1',
                'email' => 'author1@example.com',
                'password' => Hash::make('authorpassword1'),
                'role' => RoleEnum::Author->value,
            ],
            [
                'name' => 'Author User 2',
                'email' => 'author2@example.com',
                'password' => Hash::make('authorpassword2'),
                'role' => RoleEnum::Author->value,
            ],
        ];

        // Create all users dynamically
        foreach ($users as $user) {
            User::create($user);
        }
    }
}
