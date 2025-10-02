<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@beautysalon.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '+1234567890',
                'address' => '123 Main St, City, State',
                'date_of_birth' => '1990-01-01',
                'gender' => 'other',
                'is_active' => true,
            ]
        );

        // Create a sample staff user
        User::updateOrCreate(
            ['email' => 'staff@beautysalon.com'],
            [
                'name' => 'Staff Member',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'phone' => '+1234567891',
                'address' => '456 Oak Ave, City, State',
                'date_of_birth' => '1992-05-15',
                'gender' => 'female',
                'is_active' => true,
            ]
        );

        // Create a sample client user
        User::updateOrCreate(
            ['email' => 'client@beautysalon.com'],
            [
                'name' => 'Client User',
                'password' => Hash::make('password'),
                'role' => 'client',
                'phone' => '+1234567892',
                'address' => '789 Pine St, City, State',
                'date_of_birth' => '1988-12-10',
                'gender' => 'female',
                'is_active' => true,
            ]
        );
    }
}
