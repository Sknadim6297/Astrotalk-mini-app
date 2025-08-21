<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'wallet_balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'wallet_balance' => 'decimal:2',
        ];
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is an astrologer
     */
    public function isAstrologer(): bool
    {
        return $this->role === 'astrologer';
    }

    /**
     * Check if user is a regular user
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Get the astrologer profile for this user
     */
    public function astrologerProfile()
    {
        return $this->hasOne(Astrologer::class);
    }

    /**
     * Compatibility alias for older code that expects $user->astrologer
     */
    public function astrologer()
    {
        return $this->hasOne(Astrologer::class);
    }

    /**
     * Get all wallet transactions for this user
     */
    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get user bookings
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get astrologer bookings (when user is an astrologer)
     */
    public function astrologerBookings()
    {
        return $this->hasMany(Booking::class, 'astrologer_id')->orderBy('created_at', 'desc');
    }

    /**
     * Add money to wallet
     */
    public function addToWallet(float $amount, string $description = 'Top up', string $transactionType = 'top_up'): WalletTransaction
    {
        // Add to wallet balance
        $this->increment('wallet_balance', $amount);
        $this->refresh();

        // Create transaction record
        return $this->walletTransactions()->create([
            'type' => 'credit',
            'amount' => $amount,
            'balance_after' => $this->wallet_balance,
            'description' => $description,
            'transaction_type' => $transactionType,
        ]);
    }

    /**
     * Deduct money from wallet
     */
    public function deductFromWallet(float $amount, string $description = 'Booking deduction', string $transactionType = 'booking_deduction', ?int $bookingId = null): ?WalletTransaction
    {
        // Check if user has sufficient balance
        if ($this->wallet_balance < $amount) {
            return null;
        }

        // Deduct from wallet balance
        $this->decrement('wallet_balance', $amount);
        $this->refresh();

        // Create transaction record
        return $this->walletTransactions()->create([
            'type' => 'debit',
            'amount' => $amount,
            'balance_after' => $this->wallet_balance,
            'description' => $description,
            'transaction_type' => $transactionType,
            'booking_id' => $bookingId,
        ]);
    }

    /**
     * Check if user has sufficient wallet balance
     */
    public function hasSufficientBalance(float $amount): bool
    {
        return $this->wallet_balance >= $amount;
    }
}
