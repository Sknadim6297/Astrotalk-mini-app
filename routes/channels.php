<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Booking;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Chat channel authorization
Broadcast::channel('chat.{bookingId}', function ($user, $bookingId) {
    $booking = Booking::with('astrologer')->find($bookingId);
    
    if (!$booking) {
        return false;
    }
    
    // Allow access if user is part of the booking or is admin
    return $user->id === $booking->user_id || 
           ($booking->astrologer && $user->id === $booking->astrologer->user_id) || 
           $user->role === 'admin';
});

// Astrologer availability channel (public channel for availability status)
Broadcast::channel('astrologer-availability', function () {
    return true; // Public channel - anyone can listen to availability updates
});

// Individual astrologer availability channel (private)
Broadcast::channel('astrologer.{astrologerId}.availability', function ($user, $astrologerId) {
    // Only the astrologer themselves or admins can listen to their private availability updates
    return ($user->role === 'astrologer' && $user->astrologer && $user->astrologer->id == $astrologerId) || 
           $user->role === 'admin';
});

// Bookings channel for general booking events
Broadcast::channel('bookings', function () {
    return true; // Public channel for booking notifications
});
