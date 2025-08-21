<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Check if admin already exists
        $admin = User::where('email', 'admin@test.com')->first();
        
        if (!$admin) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@test.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]);
            
            echo "Admin user created successfully!\n";
            echo "Email: admin@test.com\n";
            echo "Password: admin123\n";
        } else {
            echo "Admin user already exists: " . $admin->name . "\n";
        }
    }
}
