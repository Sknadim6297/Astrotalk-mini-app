@extends('layouts.auth-guard')

@section('title', 'My Wallet')

@section('protected-content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-pink-50 py-8" x-data="walletDashboard()" x-init="loadData()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="fas fa-wallet text-purple-600 mr-3"></i>My Wallet
            </h1>
            <p class="text-gray-600">Manage your wallet balance and view transaction history</p>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="text-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto mb-4"></div>
            <p class="text-gray-600">Loading wallet data...</p>
        </div>

        <!-- Content -->
        <div x-show="!loading">
            <!-- Wallet Balance Card -->
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl p-8 mb-8 text-white shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-semibold opacity-90">Current Balance</h2>
                        <div class="text-4xl font-bold mt-2" x-text="formatCurrency(balance)">
                            ₹0.00
                        </div>
                    </div>
                    <div class="text-6xl opacity-30">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
                
                <div class="flex space-x-4">
                    <a href="{{ route('wallet.add-money') }}" 
                       class="bg-white bg-opacity-20 hover:bg-opacity-30 px-6 py-3 rounded-lg font-semibold transition-all duration-200 flex items-center">
                        <i class="fas fa-plus mr-2"></i>Add Money
                    </a>
                    <a href="{{ route('wallet.transactions') }}" 
                       class="bg-white bg-opacity-20 hover:bg-opacity-30 px-6 py-3 rounded-lg font-semibold transition-all duration-200 flex items-center">
                        <i class="fas fa-history mr-2"></i>View History
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl p-6 shadow-lg">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-lg">
                            <i class="fas fa-arrow-up text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-600 text-sm">Total Credited</p>
                            <p class="text-2xl font-bold text-gray-900" x-text="formatCurrency(stats.total_credited)">₹0.00</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-lg">
                    <div class="flex items-center">
                        <div class="bg-red-100 p-3 rounded-lg">
                            <i class="fas fa-arrow-down text-red-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-600 text-sm">Total Spent</p>
                            <p class="text-2xl font-bold text-gray-900" x-text="formatCurrency(stats.total_debited)">₹0.00</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-lg">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-lg">
                            <i class="fas fa-calendar-month text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-600 text-sm">This Month</p>
                            <p class="text-2xl font-bold text-gray-900" x-text="formatCurrency(stats.this_month_spent)">₹0.00</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-clock text-gray-500 mr-2"></i>Recent Transactions
                        </h3>
                        <a href="{{ route('wallet.transactions') }}" class="text-purple-600 hover:text-purple-700 font-medium">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>

                <div class="divide-y divide-gray-200">
                    <template x-for="transaction in recentTransactions" :key="transaction.id">
                        <div class="px-6 py-4 hover:bg-gray-50 transition-colors duration-150">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div :class="transaction.type === 'credit' ? 'bg-green-100' : 'bg-red-100'" class="w-10 h-10 rounded-full flex items-center justify-center">
                                            <i :class="transaction.type === 'credit' ? 'fas fa-plus text-green-600' : 'fas fa-minus text-red-600'"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900" x-text="transaction.description"></p>
                                        <p class="text-xs text-gray-500" x-text="formatDate(transaction.created_at)"></p>
                                        <span class="inline-block mt-1 px-2 py-1 text-xs rounded-full"
                                              :class="getTransactionTypeClass(transaction.transaction_type)"
                                              x-text="formatTransactionType(transaction.transaction_type)">
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold"
                                       :class="transaction.type === 'credit' ? 'text-green-600' : 'text-red-600'"
                                       x-text="(transaction.type === 'credit' ? '+' : '-') + formatCurrency(transaction.amount)">
                                    </p>
                                    <p class="text-xs text-gray-500" x-text="'Balance: ' + formatCurrency(transaction.balance_after)"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    <!-- Empty State -->
                    <div x-show="recentTransactions.length === 0" class="px-6 py-8 text-center">
                        <i class="fas fa-receipt text-gray-300 text-4xl mb-4"></i>
                        <p class="text-gray-500">No transactions yet</p>
                        <p class="text-sm text-gray-400 mt-1">Your transaction history will appear here</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function walletDashboard() {
    return {
        loading: true,
        balance: 0,
        stats: {
            total_credited: 0,
            total_debited: 0,
            this_month_spent: 0
        },
        recentTransactions: [],

        async loadData() {
            try {
                this.loading = true;
                
                // Load balance and stats using session auth
                const [balanceResponse, statsResponse, transactionsResponse] = await Promise.all([
                    this.sessionCall('/wallet/data'),
                    this.sessionCall('/wallet/stats'),
                    this.sessionCall('/wallet/transactions-data?per_page=10')
                ]);

                if (balanceResponse.status === 'success') {
                    this.balance = balanceResponse.data.balance;
                }

                if (statsResponse.status === 'success') {
                    this.stats = statsResponse.data;
                }

                if (transactionsResponse.status === 'success') {
                    this.recentTransactions = transactionsResponse.data.transactions;
                }

            } catch (error) {
                console.error('Error loading wallet data:', error);
                this.showError('Failed to load wallet data');
            } finally {
                this.loading = false;
            }
        },

        async sessionCall(endpoint) {
            const response = await fetch(endpoint, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin'
            });
            return await response.json();
        },

        formatCurrency(amount) {
            return '₹' + parseFloat(amount || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-IN', {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        formatTransactionType(type) {
            return type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        },

        getTransactionTypeClass(type) {
            const classes = {
                'top_up': 'bg-green-100 text-green-800',
                'booking_deduction': 'bg-blue-100 text-blue-800',
                'refund': 'bg-purple-100 text-purple-800',
                'admin_adjustment': 'bg-gray-100 text-gray-800'
            };
            return classes[type] || 'bg-gray-100 text-gray-800';
        },

        showError(message) {
            // You can implement a toast notification here
            console.error(message);
        }
    }
}
</script>
@endsection
