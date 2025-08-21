<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Astrologer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'languages',
        'specialization',
        'experience',
        'per_minute_rate',
        'wallet_balance',
        'bio',
        'education',
        'certifications',
        'availability',
        'weekly_availability',
        'today_availability',
        'timezone',
        'is_online',
        'is_available_now',
        'last_seen_at',
        'status',
        'approved_at',
        'approved_by',
        'admin_notes',
    ];

    protected $casts = [
        'languages' => 'array',
        'specialization' => 'array',
        'availability' => 'array',
        'weekly_availability' => 'array',
        'today_availability' => 'array',
        'experience' => 'integer',
        'per_minute_rate' => 'decimal:2',
        'wallet_balance' => 'decimal:2',
        'is_online' => 'boolean',
        'is_available_now' => 'boolean',
        'approved_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    /**
     * Get the user that owns the astrologer profile
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get reviews for this astrologer
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get bookings for this astrologer
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get verified reviews only
     */
    public function verifiedReviews()
    {
        return $this->reviews()->verified();
    }

    /**
     * Get average rating
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get total reviews count
     */
    public function getTotalReviewsAttribute()
    {
        return $this->reviews()->count();
    }

    /**
     * Get approved by admin relationship
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope for approved astrologers
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for pending astrologers
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Check if astrologer is approved and active
     */
    public function isActive()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if astrologer is currently available for chat
     */
    public function isAvailableNow()
    {
        if (!$this->isActive() || !$this->is_online) {
            return false;
        }

        // Check if manually set as available now
        if ($this->is_available_now) {
            return true;
        }

        // Check today's availability schedule
        return $this->isAvailableAtCurrentTime();
    }

    /**
     * Check if astrologer is available at current time based on schedule
     */
    public function isAvailableAtCurrentTime()
    {
        $now = now($this->timezone ?? 'Asia/Kolkata');
        $currentDay = strtolower($now->format('l')); // monday, tuesday, etc.
        $currentTime = $now->format('H:i');

        // Check today's availability
        if ($this->today_availability && is_array($this->today_availability)) {
            foreach ($this->today_availability as $slot) {
                if (isset($slot['start_time']) && isset($slot['end_time'])) {
                    if ($currentTime >= $slot['start_time'] && $currentTime <= $slot['end_time']) {
                        return true;
                    }
                }
            }
        }

        // Check weekly availability
        if ($this->weekly_availability && is_array($this->weekly_availability)) {
            $daySchedule = $this->weekly_availability[$currentDay] ?? null;
            if ($daySchedule && isset($daySchedule['slots']) && is_array($daySchedule['slots'])) {
                foreach ($daySchedule['slots'] as $slot) {
                    if (isset($slot['start_time']) && isset($slot['end_time'])) {
                        if ($currentTime >= $slot['start_time'] && $currentTime <= $slot['end_time']) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get today's availability slots
     */
    public function getTodayAvailability()
    {
        $today = strtolower(now($this->timezone ?? 'Asia/Kolkata')->format('l'));

        // Priority: today_availability over weekly_availability
        if ($this->today_availability && is_array($this->today_availability)) {
            return $this->today_availability;
        }

        if ($this->weekly_availability && is_array($this->weekly_availability)) {
            $daySchedule = $this->weekly_availability[$today] ?? null;
            return $daySchedule['slots'] ?? [];
        }

        return [];
    }

    /**
     * Get formatted availability status
     */
    public function getAvailabilityStatus()
    {
        if (!$this->isActive()) {
            return [
                'status' => 'inactive',
                'message' => 'Astrologer is not available',
                'can_chat' => false
            ];
        }

        if (!$this->is_online) {
            return [
                'status' => 'offline',
                'message' => 'Astrologer is offline',
                'can_chat' => false,
                'today_slots' => $this->getTodayAvailability()
            ];
        }

        if ($this->isAvailableNow()) {
            return [
                'status' => 'available',
                'message' => 'Available for chat now',
                'can_chat' => true
            ];
        }

        return [
            'status' => 'busy',
            'message' => 'Currently busy',
            'can_chat' => false,
            'today_slots' => $this->getTodayAvailability()
        ];
    }

    /**
     * Update last seen timestamp
     */
    public function updateLastSeen()
    {
        $this->update(['last_seen_at' => now()]);
    }

    /**
     * Set availability for today
     */
    public function setTodayAvailability(array $slots)
    {
        $this->update(['today_availability' => $slots]);
    }

    /**
     * Toggle online status
     */
    public function toggleOnlineStatus($isOnline = null)
    {
        $this->update([
            'is_online' => $isOnline ?? !$this->is_online,
            'last_seen_at' => now()
        ]);
    }

    /**
     * Scope for currently available astrologers
     */
    public function scopeAvailableNow($query)
    {
        return $query->where('status', 'approved')
                    ->where('is_online', true)
                    ->where(function ($q) {
                        $q->where('is_available_now', true)
                          ->orWhereRaw('JSON_LENGTH(today_availability) > 0')
                          ->orWhereRaw('JSON_LENGTH(weekly_availability) > 0');
                    });
    }
}
