@extends('layouts.auth-guard')

@section('title', 'Transaction History')

@section('protected-content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-pink-50 py-8" x-data="transactionHistory()" x-init="loadTransactions()">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <a href="{{ route('wallet.index') }}" class="text-purple-600 hover:text-purple-700 mr-4">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-history text-purple-600 mr-3"></i>Transaction History
                    </h1>
                    <p class="text-gray-600 mt-1">View all your wallet transactions</p>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="text-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto mb-4"></div>
            <p class="text-gray-600">Loading transactions...</p>
        </div>

        <!-- Content -->
        <div x-show="!loading">
            <!-- Filters -->
            <div class="bg-white rounded-xl p-6 mb-8 shadow-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-filter text-gray-500 mr-2"></i>Filters
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Transaction Type</label>
                        <select id="type" x-model="filters.type" @change="loadTransactions()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">All Types</option>
                            <option value="credit">Credit (+)</option>
                            <option value="debit">Debit (-)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="transaction_type" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select id="transaction_type" x-model="filters.transaction_type" @change="loadTransactions()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">All Categories</option>
                            <option value="top_up">Top Up</option>
                            <option value="booking_deduction">Booking Deduction</option>
                            <option value="refund">Refund</option>
                            <option value="admin_adjustment">Admin Adjustment</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button @click="clearFilters()" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>Clear Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Transactions List -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-receipt text-gray-500 mr-2"></i>All Transactions
                        <span class="text-sm font-normal text-gray-600 ml-2" x-text="'(' + pagination.total + ' total)'"></span>
                    </h3>
                </div>

                <div x-show="transactions.length > 0">
                    <div class="divide-y divide-gray-200">
                        <template x-for="transaction in transactions" :key="transaction.id">
                            <div class="px-6 py-4 hover:bg-gray-50 transition-colors duration-150">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div :class="transaction.type === 'credit' ? 'bg-green-100' : 'bg-red-100'" class="w-12 h-12 rounded-full flex items-center justify-center">
                                                <i :class="transaction.type === 'credit' ? 'fas fa-plus text-green-600 text-lg' : 'fas fa-minus text-red-600 text-lg'"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-gray-900" x-text="transaction.description"></p>
                                            <div class="flex items-center mt-1">
                                                <p class="text-xs text-gray-500" x-text="formatDate(transaction.created_at)"></p>
                                                <span x-show="transaction.booking_id" class="mx-2 text-gray-300">•</span>
                                                <p x-show="transaction.booking_id" class="text-xs text-gray-500" x-text="'Booking #' + transaction.booking_id"></p>
                                            </div>
                                            <span class="inline-block mt-2 px-3 py-1 text-xs rounded-full font-medium"
                                                  :class="getTransactionTypeClass(transaction.transaction_type)"
                                                  x-text="formatTransactionType(transaction.transaction_type)">
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold"
                                           :class="transaction.type === 'credit' ? 'text-green-600' : 'text-red-600'"
                                           x-text="(transaction.type === 'credit' ? '+' : '-') + formatCurrency(transaction.amount)">
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1" x-text="'Balance: ' + formatCurrency(transaction.balance_after)"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Pagination (Simple) -->
                    <div x-show="pagination.has_more || pagination.current_page > 1" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <button @click="loadPreviousPage()" :disabled="pagination.current_page <= 1" 
                                    class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-chevron-left mr-2"></i>Previous
                            </button>
                            <span class="text-sm text-gray-600" x-text="'Page ' + pagination.current_page + ' of ' + pagination.last_page"></span>
                            <button @click="loadNextPage()" :disabled="!pagination.has_more" 
                                    class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                Next<i class="fas fa-chevron-right ml-2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div x-show="transactions.length === 0" class="px-6 py-12 text-center">
                    <i class="fas fa-receipt text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No transactions found</h3>
                    <template x-if="hasActiveFilters()">
                        <div>
                            <p class="text-gray-500 mb-4">No transactions match your current filters.</p>
                            <button @click="clearFilters()" class="text-purple-600 hover:text-purple-700 font-medium">
                                <i class="fas fa-times mr-1"></i>Clear Filters
                            </button>
                        </div>
                    </template>
                    <template x-if="!hasActiveFilters()">
                        <div>
                            <p class="text-gray-500 mb-4">You haven't made any transactions yet.</p>
                            <a href="{{ route('wallet.add-money') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors duration-200">
                                <i class="fas fa-plus mr-2"></i>Add Money to Wallet
                            </a>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function transactionHistory() {
    return {
        loading: true,
        transactions: [],
        pagination: {
            current_page: 1,
            last_page: 1,
            total: 0,
            has_more: false
        },
        filters: {
            type: '',
            transaction_type: ''
        },

        async loadTransactions(page = 1) {
            try {
                this.loading = true;
                
                const params = new URLSearchParams({
                    page: page,
                    per_page: 20
                });
                
                if (this.filters.type) params.append('type', this.filters.type);
                if (this.filters.transaction_type) params.append('transaction_type', this.filters.transaction_type);
                
                const response = await this.sessionCall('/wallet/transactions-data?' + params.toString());
                
                if (response.status === 'success') {
                    this.transactions = response.data.transactions;
                    this.pagination = response.data.pagination;
                }
            } catch (error) {
                console.error('Error loading transactions:', error);
            } finally {
                this.loading = false;
            }
        },

        async loadPreviousPage() {
            if (this.pagination.current_page > 1) {
                await this.loadTransactions(this.pagination.current_page - 1);
            }
        },

        async loadNextPage() {
            if (this.pagination.has_more) {
                await this.loadTransactions(this.pagination.current_page + 1);
            }
        },

        clearFilters() {
            this.filters.type = '';
            this.filters.transaction_type = '';
            this.loadTransactions(1);
        },

        hasActiveFilters() {
            return this.filters.type || this.filters.transaction_type;
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
        }
    }
}
</script>
@endsection
