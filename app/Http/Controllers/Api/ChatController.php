<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Booking;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Get chat messages for a booking
     */
    public function getMessages(Request $request, $bookingId): JsonResponse
    {
        try {
            $booking = Booking::with(['user', 'astrologer'])->findOrFail($bookingId);
            $user = Auth::user();

            // Check if user is part of this booking or an admin (admins can view chats)
            if ($user->role !== 'admin' && $booking->user_id !== $user->id && $booking->astrologer_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to access this chat'
                ], 403);
            }

            // Only allow chat if booking is active
            if ($booking->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat is only available for active bookings',
                    'booking_status' => $booking->status
                ], 400);
            }

            $messages = ChatMessage::with(['sender', 'receiver'])
                ->where('booking_id', $bookingId)
                ->orderBy('sent_at', 'asc')
                ->get();

            // Mark messages as read if user is the receiver
            ChatMessage::where('booking_id', $bookingId)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'data' => [
                    'messages' => $messages,
                    'booking' => $booking
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch messages'
            ], 500);
        }
    }

    /**
     * Send a chat message
     */
    public function sendMessage(Request $request, $bookingId): JsonResponse
    {
        try {
            $request->validate([
                'message' => 'required|string|max:1000'
            ]);

            $booking = Booking::with(['user', 'astrologer'])->findOrFail($bookingId);
            $user = Auth::user();

            // Check if user is part of this booking (admins cannot send messages)
            if ($user->role === 'admin' || ($booking->user_id !== $user->id && $booking->astrologer_id !== $user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to send message in this chat'
                ], 403);
            }

            // Only allow chat if booking is active
            if ($booking->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chat is only available for active bookings',
                    'booking_status' => $booking->status
                ], 400);
            }

            // Determine receiver
            $receiverId = ($user->id === $booking->user_id) ? $booking->astrologer_id : $booking->user_id;

            $message = ChatMessage::create([
                'booking_id' => $bookingId,
                'sender_id' => $user->id,
                'receiver_id' => $receiverId,
                'message' => $request->message,
                'sent_at' => now()
            ]);

            $message->load(['sender', 'receiver']);

            // Broadcast the message in real-time
            broadcast(new MessageSent($message))->toOthers();

            return response()->json([
                'success' => true,
                'data' => [
                    'message' => $message
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message'
            ], 500);
        }
    }

    /**
     * Get chat interface for a booking
     */
    public function getChatInterface($bookingId)
    {
        try {
            $booking = Booking::with(['user', 'astrologer'])->findOrFail($bookingId);
            $user = Auth::user();

            // Check if user is part of this booking or an admin
            if ($user->role !== 'admin' && $booking->user_id !== $user->id && $booking->astrologer_id !== $user->id) {
                abort(403, 'Unauthorized to access this chat');
            }

            // Only allow chat if booking is active
            if ($booking->status !== 'active') {
                return redirect()->back()->with('error', 'Chat is only available for active bookings');
            }

            return view('chat.interface', compact('booking'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load chat interface');
        }
    }
}
