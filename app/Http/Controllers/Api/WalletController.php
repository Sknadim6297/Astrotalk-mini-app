<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    /**
     * Get user's wallet data (for authenticated users)
     */
    public function getUserWallet(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'balance' => (float) $user->wallet_balance,
                    'formatted_balance' => '₹' . number_format($user->wallet_balance, 2)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch wallet data'
            ], 500);
        }
    }

    /**
     * Get user's wallet balance
     */
    public function getBalance(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'balance' => (float) $user->wallet_balance,
                    'formatted_balance' => '₹' . number_format($user->wallet_balance, 2)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch balance'
            ], 500);
        }
    }

    /**
     * Add money to wallet
     */
    public function addBalance(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:1|max:50000',
                'description' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            if ($user->role !== 'user') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only users can access wallet features'
                ], 403);
            }
            
            $amount = (float) $request->amount;
            $description = $request->description ?? 'Wallet top-up';

            // Add money to wallet
            $transaction = $user->addToWallet($amount, $description, 'top_up');

            return response()->json([
                'success' => true,
                'message' => 'Money added successfully',
                'data' => [
                    'transaction' => [
                        'id' => $transaction->id,
                        'amount' => $transaction->amount,
                        'type' => $transaction->type,
                        'description' => $transaction->description,
                        'balance_after' => $transaction->balance_after,
                        'created_at' => $transaction->created_at->format('Y-m-d H:i:s')
                    ],
                    'new_balance' => (float) $user->fresh()->wallet_balance
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Wallet add balance error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'amount' => $request->amount ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add money to wallet: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get wallet transaction history
     */
    public function getTransactions(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $perPage = $request->get('per_page', 10);
            $perPage = min($perPage, 50); // Max 50 per page

            $transactions = $user->walletTransactions()
                ->with('booking:id,user_id,astrologer_id,status')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => [
                    'transactions' => $transactions->items(),
                    'pagination' => [
                        'current_page' => $transactions->currentPage(),
                        'last_page' => $transactions->lastPage(),
                        'per_page' => $transactions->perPage(),
                        'total' => $transactions->total(),
                        'has_more' => $transactions->hasMorePages()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch transactions'
            ], 500);
        }
    }

    /**
     * Get wallet statistics
     */
    public function getStats(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $stats = [
                'total_credited' => $user->walletTransactions()
                    ->where('type', 'credit')
                    ->sum('amount'),
                'total_debited' => $user->walletTransactions()
                    ->where('type', 'debit')
                    ->sum('amount'),
                'current_balance' => (float) $user->wallet_balance,
                'total_transactions' => $user->walletTransactions()->count(),
                'this_month_spent' => $user->walletTransactions()
                    ->where('type', 'debit')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('amount')
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch wallet statistics'
            ], 500);
        }
    }
}
