<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'astrologer_id', 
        'booking_id',
        'rating',
        'comment',
        'is_verified'
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user who wrote the review
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the astrologer being reviewed
     */
    public function astrologer()
    {
        return $this->belongsTo(Astrologer::class);
    }

    /**
     * Get the booking this review is for
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Scope for verified reviews only
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for reviews with specific rating
     */
    public function scopeWithRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }
}
