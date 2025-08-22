<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Astrologer;

class CreateTestAstrologer extends Command
{
    protected $signature = 'create:test-astrologer';
    protected $description = 'Create a test astrologer for development';

    public function handle()
    {
        $this->info('Creating test astrologer...');

        $user = User::updateOrCreate(
            ['email' => 'test@astrologer.com'],
            [
                'name' => 'Test Astrologer',
                'email' => 'test@astrologer.com',
                'password' => bcrypt('password123'),
                'role' => 'astrologer',
                'email_verified_at' => now(),
            ]
        );

        $astrologer = Astrologer::updateOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'name' => 'Test Astrologer',
                'languages' => ['English'],
                'specialization' => ['General'],
                'experience_years' => 5,
                'price_per_minute' => 50.00,
                'description' => 'Test astrologer for development',
                'status' => 'approved',
                'is_online' => false,
                'is_available_now' => false,
            ]
        );

        $token = $user->createToken('test-token')->plainTextToken;

        $this->info('Test astrologer created successfully!');
        $this->info('Email: ' . $user->email);
        $this->info('Password: password123');
        $this->info('Astrologer ID: ' . $astrologer->id);
        $this->info('API Token: ' . $token);
        
        $this->info('');
        $this->info('You can now:');
        $this->info('1. Login at http://127.0.0.1:8000/auth/login');
        $this->info('2. Or use the token in localStorage for API access');

        return 0;
    }
}
