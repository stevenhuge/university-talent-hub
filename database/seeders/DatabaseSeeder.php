<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Student
        \App\Models\User::create([
            'name' => 'John Doe',
            'email' => 'student@example.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'points' => 15,
            'major' => 'Informatics Engineering'
        ]);

        \App\Models\User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'points' => 50,
            'major' => 'Information Systems'
        ]);

        // Rewards
        \App\Models\Reward::create([
            'name' => 'Campus Canteen Voucher 50k',
            'description' => 'A voucher worth 50,000 IDR to be used at the main campus canteen.',
            'points_required' => 10,
            'stock' => 100
        ]);

        \App\Models\Reward::create([
            'name' => 'Exclusive University Hoodie',
            'description' => 'A premium hoodie with the university logo.',
            'points_required' => 50,
            'stock' => 20
        ]);
        
        \App\Models\Reward::create([
            'name' => 'Free 1-on-1 Mentoring Session',
            'description' => 'Get a 1-on-1 mentoring session with an industry professional.',
            'points_required' => 100,
            'stock' => 5
        ]);
    }
}
