<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Astrologer;
use Illuminate\Support\Facades\Hash;

class TestAstrologerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test astrologer user
        $user = User::firstOrCreate(
            ['email' => 'astrologer@test.com'],
            [
                'name' => 'Test Astrologer',
                'password' => Hash::make('password'),
                'role' => 'astrologer',
                'email_verified_at' => now()
            ]
        );

        // Create astrologer profile if it doesn't exist
        $astrologer = Astrologer::firstOrCreate(
            ['user_id' => $user->id],
            [
                'specialization' => ['Vedic Astrology', 'Numerology', 'Tarot Reading'],
                'languages' => ['Hindi', 'English'],
                'experience' => 5,
                'per_minute_rate' => 25.00
            ]
        );

        $this->command->info('Test astrologer created:');
        $this->command->info('Email: astrologer@test.com');
        $this->command->info('Password: password');
    }
}
