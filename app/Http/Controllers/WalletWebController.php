<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WalletWebController extends Controller
{
    /**
     * Show wallet dashboard
     */
    public function index()
    {
        // Return the view with required role for client-side auth
        return view('user.wallet.index', ['requiredRole' => 'user']);
    }
    
    /**
     * Show add money page
     */
    public function addMoney()
    {
        // Return the view with required role for client-side auth
        return view('user.wallet.add-money', ['requiredRole' => 'user']);
    }

    /**
     * Return wallet data (used by session-authenticated frontend)
     */
    public function data()
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'balance' => $user->wallet_balance,
            ]
        ]);
    }

    /**
     * Handle add money request from session-authenticated frontend
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $amount = (float) $request->input('amount');
        $description = $request->input('description', 'Wallet top-up');

        // Use model helper to add money
        $transaction = $user->addToWallet($amount, $description);

        return response()->json([
            'status' => 'success',
            'data' => [
                'new_balance' => $user->wallet_balance,
                'transaction_id' => $transaction->id ?? null,
            ]
        ]);
    }
    
    /**
     * Show transaction history page
     */
    public function transactions(Request $request)
    {
        // Return the view with required role for client-side auth
        return view('user.wallet.transactions', ['requiredRole' => 'user']);
    }

    /**
     * Return wallet stats (used by session-authenticated frontend)
     */
    public function stats()
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        // Calculate stats from wallet transactions
        $totalCredited = $user->walletTransactions()
            ->where('type', 'credit')
            ->sum('amount');

        $totalDebited = $user->walletTransactions()
            ->where('type', 'debit')
            ->sum('amount');

        $thisMonthSpent = $user->walletTransactions()
            ->where('type', 'debit')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('amount');

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_credited' => $totalCredited,
                'total_debited' => $totalDebited,
                'this_month_spent' => $thisMonthSpent,
            ]
        ]);
    }

    /**
     * Return wallet transactions data (used by session-authenticated frontend)
     */
    public function transactionsData(Request $request)
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        $query = $user->walletTransactions();

        // Apply filters
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('transaction_type') && $request->transaction_type) {
            $query->where('transaction_type', $request->transaction_type);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $transactions = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => [
                'transactions' => $transactions->items(),
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                ],
            ]
        ]);
    }
}
