<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Astrologer;
use App\Models\Booking;
use App\Models\WalletTransaction;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Get astrologer availability for booking popup
     */
    public function getAvailability($astrologerId)
    {
        try {
            $astrologer = Astrologer::with('user')
                ->where('user_id', $astrologerId)
                ->where('status', 'approved')
                ->first();

            if (!$astrologer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Astrologer not found'
                ], 404);
            }

            // Check if astrologer has active session
            $activeBooking = Booking::where('astrologer_id', $astrologerId)
                ->where('status', 'active')
                ->first();

            $availability = $astrologer->availability ?? [];
            $timezone = $astrologer->timezone ?? 'Asia/Kolkata';

            return response()->json([
                'success' => true,
                'data' => [
                    'astrologer' => [
                        'id' => $astrologer->user_id,
                        'name' => $astrologer->user->name,
                        'rate' => $astrologer->per_minute_rate,
                        'is_online' => $astrologer->is_online,
                        'specialization' => is_array($astrologer->specialization) 
                            ? implode(', ', $astrologer->specialization) 
                            : $astrologer->specialization
                    ],
                    'availability' => $availability,
                    'timezone' => $timezone,
                    'has_active_session' => !!$activeBooking,
                    'booking_fee' => 10.00
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load availability: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new booking
     */
    public function create(Request $request)
    {
        // Debug: Log authentication status
        Log::info('Booking create attempt', [
            'user_authenticated' => Auth::check(),
            'user_id' => Auth::id(),
            'user_role' => Auth::user()?->role ?? 'no user',
            'request_headers' => $request->headers->all(),
        ]);

        $request->validate([
            'astrologer_id' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:1000',
            'scheduled_at' => 'nullable|date|after:now'
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $user = Auth::user();
                
                // Check if user has the correct role
                if (!$user || $user->role !== 'user') {
                    throw new \Exception('Only users can create bookings');
                }
                
                $astrologerId = $request->astrologer_id;

                // Get astrologer
                $astrologer = Astrologer::where('user_id', $astrologerId)
                    ->where('status', 'approved')
                    ->first();

                if (!$astrologer) {
                    throw new \Exception('Astrologer not found or not approved');
                }

                // Check if astrologer is online
                if (!$astrologer->is_online) {
                    throw new \Exception('Astrologer is currently offline');
                }

                // Check for existing active booking
                $existingBooking = Booking::where('astrologer_id', $astrologerId)
                    ->where('status', 'active')
                    ->first();

                if ($existingBooking) {
                    throw new \Exception('Astrologer is currently in another session');
                }

                // Check user wallet balance
                $bookingFee = 10.00;
                if ($user->wallet_balance < $bookingFee) {
                    throw new \Exception('Insufficient wallet balance. Please add money to your wallet.');
                }

                // Deduct booking fee
                $user->wallet_balance -= $bookingFee;
                $user->save();

                // Create booking
                $booking = Booking::create([
                    'user_id' => $user->id,
                    'astrologer_id' => $astrologerId,
                    'status' => 'pending', // Start as pending, becomes active when user joins chat
                    'per_minute_rate' => $astrologer->per_minute_rate,
                    'booking_fee' => $bookingFee,
                    'total_amount' => $bookingFee,
                    'notes' => $request->notes,
                    'scheduled_at' => $request->scheduled_at,
                    // Don't set started_at yet - only when chat actually starts
                ]);

                // Record wallet transaction
                WalletTransaction::create([
                    'user_id' => $user->id,
                    'booking_id' => $booking->id,
                    'type' => 'debit',
                    'amount' => $bookingFee,
                    'balance_after' => $user->wallet_balance,
                    'description' => 'Booking fee for session with ' . $astrologer->user->name,
                    'transaction_type' => 'booking_deduction'
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Booking created successfully!',
                    'data' => [
                        'booking_id' => $booking->id,
                        'chat_url' => route('chat.interface', $booking->id)
                    ]
                ]);
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * End an active booking session
     */
    public function endSession(Request $request, $bookingId)
    {
        try {
            return DB::transaction(function () use ($bookingId) {
                $booking = Booking::with(['user', 'astrologer.user'])
                    ->where('id', $bookingId)
                    ->where('status', 'active')
                    ->first();

                if (!$booking) {
                    throw new \Exception('Active booking not found');
                }

                // Check permission - both user and astrologer can end session
                $user = Auth::user();
                if ($user->id !== $booking->astrologer_id && $user->id !== $booking->user_id) {
                    throw new \Exception('Unauthorized to end this session');
                }

                // Calculate session duration and charges
                $sessionMinutes = $booking->started_at ? $booking->started_at->diffInMinutes(now()) : 0;
                $sessionCharges = $sessionMinutes * $booking->per_minute_rate;

                // Determine who ended the session
                $endedBy = ($user->id === $booking->user_id) ? 'user' : 'astrologer';
                $endMessage = $endedBy === 'user' ? 'Session completed by user' : 'Session completed by astrologer';

                // Update booking
                $booking->update([
                    'status' => 'completed',
                    'ended_at' => now(),
                    'duration_minutes' => $sessionMinutes,
                    'session_charges' => $sessionCharges,
                    'total_amount' => ($booking->total_amount ?? 0) + $sessionCharges,
                    'ended_by' => $endedBy
                ]);

                // Deduct session charges if any
                if ($sessionCharges > 0) {
                    $userModel = User::find($booking->user_id);
                    if ($userModel && $userModel->wallet_balance >= $sessionCharges) {
                        $balanceBefore = $userModel->wallet_balance;
                        $userModel->wallet_balance -= $sessionCharges;
                        $userModel->save();

                        // Get astrologer name safely
                        $astrologerName = 'Unknown Astrologer';
                        if ($booking->astrologer && $booking->astrologer->user) {
                            $astrologerName = $booking->astrologer->user->name;
                        } elseif ($booking->astrologer) {
                            $astrologerName = $booking->astrologer->name ?? 'Unknown Astrologer';
                        }

                        // Record session charges transaction
                        WalletTransaction::create([
                            'user_id' => $booking->user_id,
                            'booking_id' => $booking->id,
                            'type' => 'debit',
                            'amount' => $sessionCharges,
                            'balance_before' => $balanceBefore,
                            'balance_after' => $userModel->wallet_balance,
                            'description' => "Session charges ({$sessionMinutes} min) with {$astrologerName}",
                            'transaction_type' => 'booking_deduction'
                        ]);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => $endMessage,
                    'data' => [
                        'session_summary' => [
                            'duration_minutes' => $sessionMinutes,
                            'session_charges' => $sessionCharges,
                            'total_amount' => $booking->total_amount,
                            'ended_by' => $endedBy
                        ]
                    ]
                ]);
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get user's bookings/appointments
     */
    public function userAppointments()
    {
        $user = Auth::user();
        
        $bookings = Booking::with(['astrologer.user'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.appointments', compact('bookings'));
    }

    /**
     * Get astrologer's bookings
     */
    public function astrologerBookings()
    {
        $user = Auth::user();
        
        if ($user->role !== 'astrologer') {
            return redirect('/')->with('error', 'Access denied');
        }

        $bookings = Booking::with(['user'])
            ->where('astrologer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('astrologer.bookings', compact('bookings'));
    }

    /**
     * Admin bookings overview
     */
    public function adminBookings()
    {
        $bookings = Booking::with(['user', 'astrologer.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_bookings' => Booking::count(),
            'active_sessions' => Booking::where('status', 'active')->count(),
            'completed_today' => Booking::where('status', 'completed')
                ->whereDate('ended_at', today())->count(),
            'total_revenue_today' => Booking::where('status', 'completed')
                ->whereDate('ended_at', today())->sum('total_amount')
        ];

        return view('admin.bookings', compact('bookings', 'stats'));
    }

    /**
     * Get booking details API
     */
    public function getBookingDetails($bookingId)
    {
        try {
            $booking = Booking::with(['user', 'astrologer.user', 'messages.sender', 'messages.receiver'])
                ->find($bookingId);

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            // Check permission
            $user = Auth::user();
            if ($user->role !== 'admin' && 
                $user->id !== $booking->user_id && 
                $user->id !== $booking->astrologer_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'booking' => $booking,
                    'duration_formatted' => $booking->duration_minutes ? 
                        floor($booking->duration_minutes / 60) . 'h ' . 
                        ($booking->duration_minutes % 60) . 'm' : 'N/A'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }
    }

    /**
     * Get user's bookings for web (session auth)
     */
    public function getUserBookingsWeb()
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
                        'booking_fee' => $b->booking_fee ?? 10.00,
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
                            'name' => optional($b->astrologer->user)->name ?? 'Unknown Astrologer',
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
                'message' => 'Failed to fetch bookings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Start session when user joins chat (web route)
     */
    public function startSessionWeb($bookingId)
    {
        try {
            return DB::transaction(function () use ($bookingId) {
                $booking = Booking::where('id', $bookingId)
                    ->where('status', 'pending')
                    ->first();

                if (!$booking) {
                    throw new \Exception('Booking not found or already started');
                }

                // Check permission
                $user = Auth::user();
                if ($user->id !== $booking->user_id && $user->id !== $booking->astrologer_id) {
                    throw new \Exception('Unauthorized to start this session');
                }

                // Check user has sufficient balance for at least 1 minute
                $userModel = User::find($booking->user_id);
                if ($userModel->wallet_balance < $booking->per_minute_rate) {
                    throw new \Exception('Insufficient wallet balance to start session');
                }

                // Start the session
                $booking->update([
                    'status' => 'active',
                    'started_at' => now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Session started successfully',
                    'data' => [
                        'booking' => $booking->fresh(['user', 'astrologer.user'])
                    ]
                ]);
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Cancel booking (web route)
     */
    public function cancelBookingWeb($bookingId)
    {
        try {
            return DB::transaction(function () use ($bookingId) {
                $booking = Booking::where('id', $bookingId)
                    ->where('status', 'pending')
                    ->first();

                if (!$booking) {
                    throw new \Exception('Booking not found or cannot be cancelled');
                }

                // Check permission
                $user = Auth::user();
                if ($user->id !== $booking->user_id) {
                    throw new \Exception('Only the user who made the booking can cancel it');
                }

                // Refund booking fee
                $bookingFee = $booking->booking_fee ?? 10.00;
                $userModel = User::find($booking->user_id);
                $userModel->wallet_balance += $bookingFee;
                $userModel->save();

                // Record refund transaction
                WalletTransaction::create([
                    'user_id' => $booking->user_id,
                    'booking_id' => $booking->id,
                    'type' => 'credit',
                    'amount' => $bookingFee,
                    'balance_after' => $userModel->wallet_balance,
                    'description' => 'Booking cancellation refund',
                    'transaction_type' => 'refund'
                ]);

                // Update booking status
                $booking->update(['status' => 'cancelled']);

                return response()->json([
                    'success' => true,
                    'message' => 'Booking cancelled and refund processed',
                    'data' => [
                        'refunded_amount' => $bookingFee,
                        'new_wallet_balance' => $userModel->wallet_balance
                    ]
                ]);
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
