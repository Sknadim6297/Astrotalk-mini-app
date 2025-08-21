<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin user with fixed credentials';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if admin user exists
        $admin = User::where('email', 'admin@gmail.com')->first();

        if ($admin) {
            $this->info("Admin user already exists:");
            $this->line("Email: " . $admin->email);
            $this->line("Name: " . $admin->name);
            $this->line("Role: " . $admin->role);
            return;
        }

        $this->info("Creating admin user...");
        
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);
        
        $this->info("Admin user created successfully!");
        $this->line("Email: admin@gmail.com");
        $this->line("Password: admin123");
        $this->line("Role: admin");
    }
}
