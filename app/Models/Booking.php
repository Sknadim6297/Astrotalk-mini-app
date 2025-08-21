<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Astrologer;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'astrologer_id',
        'status',
        'ended_by',
        'per_minute_rate',
        'started_at',
        'ended_at',
        'duration_minutes',
        'total_amount',
        'last_deduction_at',
        'notes'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'last_deduction_at' => 'datetime',
        'per_minute_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'duration_minutes' => 'integer'
    ];

    /**
     * Get the user that made the booking
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the astrologer for this booking
     */
    public function astrologer(): BelongsTo
    {
    return $this->belongsTo(Astrologer::class, 'astrologer_id');
    }

    /**
     * Get wallet transactions related to this booking
     */
    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Get chat messages for this booking
     */
    public function messages(): HasMany
    {
        return $this->hasMany(\App\Models\ChatMessage::class);
    }

    /**
     * Scope for active bookings
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for pending bookings
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for completed bookings
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Check if booking is currently active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Start the booking session
     */
    public function start(): void
    {
        $this->update([
            'status' => 'active',
            'started_at' => now()
        ]);
    }

    /**
     * End the booking session
     */
    public function end(): void
    {
        $endTime = now();
        $startTime = $this->started_at;
        
        if ($startTime) {
            $durationMinutes = $startTime->diffInMinutes($endTime);
            $totalAmount = $durationMinutes * $this->per_minute_rate;
            
            $this->update([
                'status' => 'completed',
                'ended_at' => $endTime,
                'duration_minutes' => $durationMinutes,
                'total_amount' => $totalAmount
            ]);
        }
    }

    /**
     * Calculate current session cost
     */
    public function getCurrentCost(): float
    {
        if (!$this->isActive() || !$this->started_at) {
            return 0.00;
        }

        $minutesElapsed = $this->started_at->diffInMinutes(now());
        return $minutesElapsed * $this->per_minute_rate;
    }
}
