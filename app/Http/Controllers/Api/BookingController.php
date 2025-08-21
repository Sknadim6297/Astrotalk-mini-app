<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Astrologer;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Book an astrologer session
     */
    public function book(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'astrologer_id' => 'required|exists:astrologers,id',
                'notes' => 'nullable|string|max:500'
            ]);

            $user = Auth::user();
            $astrologer = Astrologer::with('user')->findOrFail($request->astrologer_id);
            
            // Check if user has enough balance for booking fee
            $bookingFee = 10.00;
            if ($user->wallet_balance < $bookingFee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient wallet balance. Minimum ₹10 required for booking.',
                    'data' => [
                        'required_amount' => $bookingFee,
                        'current_balance' => $user->wallet_balance
                    ]
                ], 400);
            }

            // Start database transaction
            DB::beginTransaction();

            try {
                // Deduct booking fee from user wallet
                $user->wallet_balance -= $bookingFee;
                $user->save();

                // Create wallet transaction
                WalletTransaction::create([
                    'user_id' => $user->id,
                    'booking_id' => null,
                    'type' => 'debit',
                    'amount' => $bookingFee,
                    'balance_after' => $user->wallet_balance,
                    'description' => "Booking fee for astrologer: {$astrologer->user->name}",
                    'transaction_type' => 'booking_deduction'
                ]);

                // Create booking
                $booking = Booking::create([
                    'user_id' => $user->id,
                    'astrologer_id' => $astrologer->id,
                    'status' => 'pending',
                    'per_minute_rate' => $astrologer->per_minute_rate,
                    'total_amount' => $bookingFee, // Initially just the booking fee
                    'notes' => $request->notes
                ]);

                DB::commit();

                Log::info('Booking created successfully', [
                    'booking_id' => $booking->id,
                    'user_id' => $user->id,
                    'astrologer_id' => $astrologer->id,
                    'booking_fee' => $bookingFee
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Booking created successfully! Astrologer will be notified.',
                    'data' => [
                        'booking' => [
                            'id' => $booking->id,
                            'status' => $booking->status,
                            'booking_fee' => $bookingFee,
                            'rate_per_minute' => $booking->per_minute_rate,
                            'astrologer' => [
                                'name' => $astrologer->name,
                                'specialization' => $astrologer->specialization,
                                'experience_years' => $astrologer->experience_years
                            ],
                            'created_at' => $booking->created_at
                        ],
                        'new_wallet_balance' => $user->wallet_balance
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Booking creation failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'astrologer_id' => $request->astrologer_id ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Start a booking session (change status to active)
     */
    public function startSession(Request $request, $bookingId): JsonResponse
    {
        try {
            $booking = Booking::findOrFail($bookingId);
            $user = Auth::user();

            // Check authorization (either user or astrologer can start)
            if ($booking->user_id !== $user->id && $booking->astrologer_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to access this booking'
                ], 403);
            }

            if ($booking->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking is not in pending status'
                ], 400);
            }

            $booking->update([
                'status' => 'active',
                'started_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Session started successfully',
                'data' => [
                    'booking' => $booking->load(['user', 'astrologer'])
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to start session', [
                'error' => $e->getMessage(),
                'booking_id' => $bookingId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to start session'
            ], 500);
        }
    }

    /**
     * Get user's bookings
     */
    public function getUserBookings(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $bookings = Booking::where('user_id', $user->id)
                ->with(['astrologer.user'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($b) {
                    return [
                        'id' => $b->id,
                        'status' => $b->status,
                        'ended_by' => $b->ended_by,
                        'per_minute_rate' => $b->per_minute_rate,
                        'booking_fee' => $b->booking_fee ?? 0,
                        'session_charges' => $b->session_charges ?? 0,
                        'total_amount' => $b->total_amount ?? 0,
                        'created_at' => $b->created_at,
                        'started_at' => $b->started_at,
                        'ended_at' => $b->ended_at,
                        'notes' => $b->notes,
                        'duration_minutes' => $b->duration_minutes ?? 0,
                        'astrologer' => $b->astrologer ? [
                            'id' => $b->astrologer->id,
                            'user_id' => $b->astrologer->user_id,
                            'name' => $b->astrologer->user->name ?? null,
                            'specialization' => $b->astrologer->specialization,
                            'per_minute_rate' => $b->astrologer->per_minute_rate,
                            'is_online' => $b->astrologer->is_online ?? false,
                        ] : null
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'bookings' => $bookings
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch bookings'
            ], 500);
        }
    }

    /**
     * Get astrologer's bookings
     */
    public function getAstrologerBookings(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $bookings = Booking::where('astrologer_id', $user->id)
                ->with(['user'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($b) {
                    return [
                        'id' => $b->id,
                        'status' => $b->status,
                        'ended_by' => $b->ended_by,
                        'per_minute_rate' => $b->per_minute_rate,
                        'booking_fee' => $b->booking_fee ?? 0,
                        'session_charges' => $b->session_charges ?? 0,
                        'total_amount' => $b->total_amount ?? 0,
                        'created_at' => $b->created_at,
                        'started_at' => $b->started_at,
                        'ended_at' => $b->ended_at,
                        'notes' => $b->notes,
                        'duration_minutes' => $b->duration_minutes ?? 0,
                        'user' => $b->user ? [
                            'id' => $b->user->id,
                            'name' => $b->user->name,
                            'email' => $b->user->email,
                        ] : null
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'bookings' => $bookings
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch bookings'
            ], 500);
        }
    }

    /**
     * End a booking session (change status to completed and calculate final amount)
     */
    public function endSession(Request $request, $bookingId): JsonResponse
    {
        try {
            $booking = Booking::with(['user', 'astrologer.user'])->findOrFail($bookingId);
            $user = Auth::user();

            // Check authorization - both user and astrologer can end session
            if ($booking->astrologer_id !== $user->id && $booking->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to end this session'
                ], 403);
            }

            if ($booking->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Session is not active'
                ], 400);
            }

            if (!$booking->started_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session start time not found'
                ], 400);
            }

            DB::beginTransaction();

            $endTime = now();
            $startTime = Carbon::parse($booking->started_at);
            $durationMinutes = $startTime->diffInMinutes($endTime);
            
            // Calculate session cost (rate per minute * duration)
            $sessionCost = $booking->per_minute_rate * $durationMinutes;
            $totalAmount = ($booking->total_amount ?? 0) + $sessionCost; // Add to existing booking fee

            // Determine who ended the session
            $endedBy = ($user->id === $booking->user_id) ? 'user' : 'astrologer';
            $endMessage = $endedBy === 'user' ? 'Session completed by user' : 'Session completed by astrologer';

            // Update booking
            $booking->update([
                'status' => 'completed',
                'ended_at' => $endTime,
                'duration_minutes' => $durationMinutes,
                'total_amount' => $totalAmount,
                'ended_by' => $endedBy
            ]);

            // Deduct session cost from user's wallet
            $bookingUser = $booking->user;
            if ($bookingUser && $sessionCost > 0) {
                if ($bookingUser->wallet_balance >= $sessionCost) {
                    $balanceBefore = $bookingUser->wallet_balance;
                    $bookingUser->wallet_balance -= $sessionCost;
                    $bookingUser->save();

                    // Get astrologer name safely
                    $astrologerName = 'Unknown Astrologer';
                    if ($booking->astrologer && $booking->astrologer->user) {
                        $astrologerName = $booking->astrologer->user->name;
                    } elseif ($booking->astrologer) {
                        $astrologerName = $booking->astrologer->name ?? 'Unknown Astrologer';
                    }

                    // Record wallet transaction for session cost
                    WalletTransaction::create([
                        'user_id' => $bookingUser->id,
                        'booking_id' => $booking->id,
                        'type' => 'debit',
                        'amount' => $sessionCost,
                        'balance_before' => $balanceBefore,
                        'balance_after' => $bookingUser->wallet_balance,
                        'description' => "Chat with {$astrologerName} - {$durationMinutes} min",
                        'transaction_type' => 'booking_deduction'
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $endMessage,
                'data' => [
                    'booking' => $booking->fresh(),
                    'session_summary' => [
                        'duration_minutes' => $durationMinutes,
                        'session_cost' => $sessionCost,
                        'total_amount' => $totalAmount,
                        'ended_by' => $endedBy
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Failed to end session', [
                'error' => $e->getMessage(),
                'booking_id' => $bookingId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to end session'
            ], 500);
        }
    }

    /**
     * Cancel a booking
     */
    public function cancelBooking(Request $request, $bookingId): JsonResponse
    {
        try {
            $booking = Booking::with(['user'])->findOrFail($bookingId);
            $user = Auth::user();

            // Check authorization (only user can cancel their own booking)
            if ($booking->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to cancel this booking'
                ], 403);
            }

            if ($booking->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending bookings can be cancelled'
                ], 400);
            }

            DB::beginTransaction();

            // Update booking status
            $booking->update(['status' => 'cancelled']);

            // Refund booking fee to user's wallet
            $bookingFee = 10.00; // Standard booking fee
            $booking->user->wallet_balance += $bookingFee;
            $booking->user->save();

            // Record wallet transaction for refund
            WalletTransaction::create([
                'user_id' => $booking->user->id,
                'booking_id' => $booking->id,
                'type' => 'credit',
                'amount' => $bookingFee,
                'balance_before' => $booking->user->wallet_balance - $bookingFee,
                'balance_after' => $booking->user->wallet_balance,
                'description' => 'Booking cancellation refund',
                'transaction_type' => 'refund'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully and booking fee refunded',
                'data' => [
                    'booking' => $booking->fresh(),
                    'refund_amount' => $bookingFee,
                    'new_wallet_balance' => $booking->user->wallet_balance
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Failed to cancel booking', [
                'error' => $e->getMessage(),
                'booking_id' => $bookingId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel booking'
            ], 500);
        }
    }
}
