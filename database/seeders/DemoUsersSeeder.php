<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Astrologer;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create demo admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@demo.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Create demo regular user
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'user@demo.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        // Create demo astrologer
        $astrologerUser = User::create([
            'name' => 'Priya Sharma',
            'email' => 'astrologer@demo.com',
            'password' => Hash::make('password123'),
            'role' => 'astrologer',
        ]);

        // Create astrologer profile
        Astrologer::create([
            'user_id' => $astrologerUser->id,
            'languages' => ['English', 'Hindi', 'Sanskrit'],
            'specialization' => ['Vedic Astrology', 'Tarot Reading', 'Numerology'],
            'experience' => 8,
            'per_minute_rate' => 2.50,
            'wallet_balance' => 150.00,
        ]);

        // Create more demo astrologers
        $astrologerData = [
            [
                'name' => 'Raj Kumar',
                'email' => 'raj@demo.com',
                'languages' => ['English', 'Tamil', 'Telugu'],
                'specialization' => ['Western Astrology', 'Palmistry'],
                'experience' => 12,
                'per_minute_rate' => 3.75,
                'wallet_balance' => 420.00,
            ],
            [
                'name' => 'Anita Gupta',
                'email' => 'anita@demo.com',
                'languages' => ['English', 'Bengali', 'Hindi'],
                'specialization' => ['Vedic Astrology', 'Vastu Shastra', 'Face Reading'],
                'experience' => 6,
                'per_minute_rate' => 1.99,
                'wallet_balance' => 89.50,
            ],
            [
                'name' => 'Vikram Singh',
                'email' => 'vikram@demo.com',
                'languages' => ['English', 'Punjabi', 'Urdu'],
                'specialization' => ['Tarot Reading', 'Crystal Healing', 'Numerology'],
                'experience' => 15,
                'per_minute_rate' => 4.25,
                'wallet_balance' => 680.75,
            ],
            [
                'name' => 'Meera Patel',
                'email' => 'meera@demo.com',
                'languages' => ['English', 'Gujarati', 'Marathi'],
                'specialization' => ['Vedic Astrology', 'Western Astrology'],
                'experience' => 4,
                'per_minute_rate' => 1.75,
                'wallet_balance' => 45.25,
            ],
        ];

        foreach ($astrologerData as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make('password123'),
                'role' => 'astrologer',
            ]);

            Astrologer::create([
                'user_id' => $user->id,
                'languages' => $data['languages'],
                'specialization' => $data['specialization'],
                'experience' => $data['experience'],
                'per_minute_rate' => $data['per_minute_rate'],
                'wallet_balance' => $data['wallet_balance'],
            ]);
        }

        $this->command->info('Demo users created successfully!');
        $this->command->info('Admin: admin@demo.com / password123');
        $this->command->info('User: user@demo.com / password123');
        $this->command->info('Astrologer: astrologer@demo.com / password123');
    }
}
