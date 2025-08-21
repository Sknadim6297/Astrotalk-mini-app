<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Astrologer;
use App\Models\Booking;
use App\Models\ChatMessage;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class AdminController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Admin Dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_astrologers' => Astrologer::count(),
            'pending_astrologers' => Astrologer::where('status', 'pending')->count(),
            'approved_astrologers' => Astrologer::where('status', 'approved')->count(),
            'total_bookings' => Booking::count(),
            'total_revenue' => WalletTransaction::where('type', 'debit')->sum('amount'),
        ];

        $recent_registrations = User::latest()->take(5)->get();
        $pending_astrologers = Astrologer::with('user')->where('status', 'pending')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recent_registrations', 'pending_astrologers'));
    }

    /**
     * Manage Users
     */
    public function users()
    {
        $users = User::where('role', '!=', 'admin')->with('astrologer')->latest()->paginate(15);
        return view('admin.users', compact('users'));
    }

    /**
     * Manage Astrologers
     */
    public function astrologers()
    {
        $astrologers = Astrologer::with(['user', 'approvedBy'])->latest()->paginate(15);
        return view('admin.astrologers', compact('astrologers'));
    }

    /**
     * Manage Bookings
     */
    public function bookings()
    {
        $bookings = Booking::with(['user', 'astrologer.user'])
            ->latest()
            ->paginate(15);
            
        // Get stats for the view
        $stats = [
            'active_sessions' => Booking::where('status', 'active')->count(),
            'completed_today' => Booking::where('status', 'completed')
                ->whereDate('created_at', today())
                ->count(),
            'total_bookings' => Booking::count(),
            'total_revenue_today' => Booking::where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('total_amount')
        ];
        
        return view('admin.bookings', compact('bookings', 'stats'));
    }

    /**
     * Approve Astrologer
     */
    public function approveAstrologer(Request $request, $id)
    {
        try {
            $astrologer = Astrologer::findOrFail($id);
            
            $astrologer->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'admin_notes' => $request->notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Astrologer approved successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve astrologer.'
            ], 500);
        }
    }

    /**
     * Reject Astrologer
     */
    public function rejectAstrologer(Request $request, $id)
    {
        try {
            $astrologer = Astrologer::findOrFail($id);
            
            $astrologer->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'admin_notes' => $request->notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Astrologer rejected successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject astrologer.'
            ], 500);
        }
    }

    /**
     * Deactivate Astrologer
     */
    public function deactivateAstrologer(Request $request, $id)
    {
        try {
            $astrologer = Astrologer::findOrFail($id);
            
            $astrologer->update([
                'status' => 'inactive',
                'admin_notes' => $request->notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Astrologer deactivated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to deactivate astrologer.'
            ], 500);
        }
    }

    /**
     * Reactivate Astrologer
     */
    public function reactivateAstrologer(Request $request, $id)
    {
        try {
            $astrologer = Astrologer::findOrFail($id);
            
            $astrologer->update([
                'status' => 'approved',
                'admin_notes' => $request->notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Astrologer reactivated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reactivate astrologer.'
            ], 500);
        }
    }

    /**
     * Get Astrologer Details (AJAX)
     */
    public function getAstrologerDetails($id)
    {
        try {
            $astrologer = Astrologer::with(['user', 'approvedBy'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'astrologer' => $astrologer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Astrologer not found.'
            ], 404);
        }
    }

    /**
     * Delete User (Admin only)
     */
    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Don't allow deleting admins
            if ($user->role === 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete admin users.'
                ], 403);
            }

            // Delete associated astrologer profile if exists
            if ($user->astrologer) {
                $user->astrologer->delete();
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user.'
            ], 500);
        }
    }

    /**
     * Get User Details for Modal
     */
    public function getUserDetails($id)
    {
        try {
            $user = User::with(['astrologer', 'walletTransactions'])->findOrFail($id);
            
            // Get transaction summary
            $transactionSummary = [];
            if ($user->role === 'astrologer' || $user->walletTransactions->count() > 0) {
                $transactionSummary = [
                    'total_credit' => $user->walletTransactions()->where('type', 'credit')->sum('amount'),
                    'total_debit' => $user->walletTransactions()->where('type', 'debit')->sum('amount'),
                    'transaction_count' => $user->walletTransactions()->count(),
                ];
            }

            return response()->json([
                'success' => true,
                'user' => $user,
                'transaction_summary' => $transactionSummary
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }
    }

    /**
     * View Chat Conversations (Admin)
     */
    public function viewConversation($bookingId)
    {
        try {
            $booking = Booking::with(['user', 'astrologer.user'])->findOrFail($bookingId);
            
            $messages = ChatMessage::with(['sender', 'receiver'])
                ->where('booking_id', $bookingId)
                ->orderBy('sent_at', 'asc')
                ->get();

            return view('admin.conversation', compact('booking', 'messages'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Conversation not found.');
        }
    }

    /**
     * Get Chat Messages for AJAX (Admin)
     */
    public function getConversationMessages($bookingId)
    {
        try {
            $booking = Booking::with(['user', 'astrologer.user'])->findOrFail($bookingId);
            
            $messages = ChatMessage::with(['sender', 'receiver'])
                ->where('booking_id', $bookingId)
                ->orderBy('sent_at', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'booking' => $booking,
                    'messages' => $messages
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation not found.'
            ], 404);
        }
    }
}
