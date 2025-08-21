<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessBookingDeductions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wallet:process-deductions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process automatic wallet deductions for active bookings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting wallet deduction processing...');
        
        // Get all active bookings
        $activeBookings = Booking::active()
            ->with(['user', 'astrologer'])
            ->get();
            
        $processedCount = 0;
        $endedCount = 0;
        
        foreach ($activeBookings as $booking) {
            try {
                $user = $booking->user;
                $rate = $booking->per_minute_rate;
                
                // Check if user has sufficient balance for per-minute rate
                if (!$user->hasSufficientBalance($rate)) {
                    // End the booking due to insufficient funds
                    $booking->end();
                    $endedCount++;
                    
                    $this->warn("Booking #{$booking->id} ended due to insufficient funds. User: {$user->name}");
                    
                    Log::info("Booking ended due to insufficient funds", [
                        'booking_id' => $booking->id,
                        'user_id' => $user->id,
                        'user_balance' => $user->wallet_balance,
                        'required_rate' => $rate
                    ]);
                    
                    continue;
                }
                
                // Deduct per-minute rate from user's wallet
                $transaction = $user->deductFromWallet(
                    $rate,
                    "Per-minute deduction for booking #{$booking->id}",
                    'booking_deduction',
                    $booking->id
                );
                
                if ($transaction) {
                    $processedCount++;
                    $this->line("Deducted ₹{$rate} from {$user->name} for booking #{$booking->id}");
                } else {
                    // This shouldn't happen as we checked balance above, but safety check
                    $booking->end();
                    $endedCount++;
                    $this->error("Failed to deduct from {$user->name} for booking #{$booking->id}");
                }
                
            } catch (\Exception $e) {
                $this->error("Error processing booking #{$booking->id}: " . $e->getMessage());
                Log::error("Wallet deduction processing error", [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        $this->info("Processing completed!");
        $this->info("Processed deductions: {$processedCount}");
        $this->info("Bookings ended: {$endedCount}");
        $this->info("Total active bookings: " . $activeBookings->count());
        
        Log::info("Wallet deduction processing completed", [
            'processed_count' => $processedCount,
            'ended_count' => $endedCount,
            'total_active' => $activeBookings->count()
        ]);
        
        return Command::SUCCESS;
    }
}
