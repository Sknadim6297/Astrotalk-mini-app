<?php
require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Create Laravel application instance
$app = require_once 'bootstrap/app.php';

// Boot the application
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing API Authentication...\n\n";

// Test 1: Check if API is accessible
echo "1. Testing API health endpoint...\n";
$healthResponse = file_get_contents('http://127.0.0.1:8000/api/health');
echo "Health Response: " . $healthResponse . "\n\n";

// Test 2: Check authentication debug endpoint
echo "2. Testing authentication debug...\n";
$debugResponse = file_get_contents('http://127.0.0.1:8000/api/debug/request');
echo "Debug Response: " . $debugResponse . "\n\n";

// Test 3: Try to create a test astrologer if none exists
echo "3. Checking if test astrologer exists...\n";

try {
    $testUser = \App\Models\User::where('email', 'test.astrologer@example.com')->first();
    
    if (!$testUser) {
        echo "Creating test astrologer...\n";
        
        $testUser = \App\Models\User::create([
            'name' => 'Test Astrologer',
            'email' => 'test.astrologer@example.com',
            'password' => bcrypt('password123'),
            'role' => 'astrologer',
            'email_verified_at' => now(),
        ]);
        
        $astrologer = \App\Models\Astrologer::create([
            'user_id' => $testUser->id,
            'name' => 'Test Astrologer',
            'languages' => ['English'],
            'specialization' => ['General Astrology'],
            'experience_years' => 5,
            'price_per_minute' => 50.00,
            'description' => 'Test astrologer for development',
            'status' => 'approved',
            'is_online' => false,
            'is_available_now' => false,
        ]);
        
        echo "Test astrologer created with ID: " . $astrologer->id . "\n";
    } else {
        echo "Test astrologer already exists: " . $testUser->email . "\n";
        $astrologer = $testUser->astrologerProfile;
        echo "Astrologer ID: " . ($astrologer ? $astrologer->id : 'No profile') . "\n";
    }
    
    // Generate API token for testing
    $token = $testUser->createToken('test-token')->plainTextToken;
    echo "Generated API token: " . $token . "\n\n";
    
    echo "4. Testing API with token...\n";
    
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'Authorization: Bearer ' . $token,
                'Accept: application/json',
                'Content-Type: application/json'
            ]
        ]
    ]);
    
    $apiResponse = file_get_contents('http://127.0.0.1:8000/api/astrologer/availability/status', false, $context);
    echo "API Response: " . $apiResponse . "\n\n";
    
    echo "=== SETUP COMPLETE ===\n";
    echo "You can now test the availability management page with:\n";
    echo "Email: test.astrologer@example.com\n";
    echo "Password: password123\n";
    echo "Or use this token in localStorage: " . $token . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
