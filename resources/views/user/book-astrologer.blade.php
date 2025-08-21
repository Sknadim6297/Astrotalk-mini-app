@extends('layouts.auth-guard')

@section('title', 'Book Astrologer - {{ $astrologer->name }}')

@section('protected-content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-pink-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <a href="/astrologers" class="text-purple-600 hover:text-purple-700 mr-4">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-comments text-purple-600 mr-3"></i>Start Chat
                    </h1>
                    <p class="text-gray-600 mt-1">Book a session with {{ $astrologer->name }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Astrologer Info -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-purple-600 text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <h2 class="text-2xl font-bold text-gray-900">{{ $astrologer->name }}</h2>
                                <p class="text-purple-600 font-medium">
                                    @if(is_array($astrologer->specialization))
                                        {{ implode(', ', $astrologer->specialization) }}
                                    @else
                                        {{ $astrologer->specialization }}
                                    @endif
                                </p>
                                <div class="flex items-center mt-2 space-x-4">
                                    <span class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $astrologer->experience_years }} years experience
                                    </span>
                                    <span class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-star mr-1 text-yellow-500"></i>
                                        {{ $astrologer->rating ?? '4.5' }}/5
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-2">About</h3>
                            <p class="text-gray-600 text-sm">
                                {{ $astrologer->description ?? 'Experienced astrologer providing accurate predictions and guidance.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Booking Form -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden" x-data="bookingForm()">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-calendar-check text-gray-500 mr-2"></i>Book Your Session
                        </h3>
                    </div>

                    <div class="p-6">
                        <!-- Booking Fee Notice -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                                <div>
                                    <h4 class="font-medium text-blue-800">Booking Information</h4>
                                    <p class="text-sm text-blue-600 mt-1">
                                        A booking fee of ₹10 will be charged to secure your session. 
                                        The session will be charged at ₹{{ $astrologer->rate_per_minute }}/minute after it starts.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Current Balance -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                            <div class="flex items-center justify-between">
                                <span class="text-green-800 font-medium">Your Wallet Balance:</span>
                                <span class="text-xl font-bold text-green-600" x-text="formatCurrency(walletBalance)">₹0.00</span>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="mb-6">
                            <label for="booking-notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Special Notes (Optional)
                            </label>
                            <textarea id="booking-notes" 
                                      x-model="notes"
                                      rows="3" 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                      placeholder="Any specific questions or topics you'd like to discuss..."></textarea>
                        </div>

                        <!-- Chat Button -->
                        <button @click="bookSession()" 
                                :disabled="loading || walletBalance < 10"
                                :class="walletBalance >= 10 ? 'bg-purple-600 hover:bg-purple-700' : 'bg-gray-400 cursor-not-allowed'"
                                class="w-full text-white py-4 rounded-lg font-semibold text-lg transition-all duration-200">
                            <span x-show="!loading">
                                <i class="fas fa-comments mr-2"></i>
                                <span x-show="walletBalance >= 10">Chat Now (₹10 booking fee)</span>
                                <span x-show="walletBalance < 10">Insufficient Balance</span>
                            </span>
                            <span x-show="loading">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Processing...
                            </span>
                        </button>

                        <div x-show="walletBalance < 10" class="mt-4 text-center">
                            <a href="/wallet/add-money" class="text-purple-600 hover:text-purple-700 font-medium">
                                Add money to wallet
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing Info -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden sticky top-8">
                    <div class="px-6 py-4 bg-purple-50 border-b border-purple-200">
                        <h3 class="text-lg font-semibold text-purple-900">
                            <i class="fas fa-rupee-sign text-purple-600 mr-2"></i>Pricing
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Booking Fee:</span>
                                <span class="font-semibold text-gray-900">₹10</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Per Minute Rate:</span>
                                <span class="font-semibold text-gray-900">₹{{ $astrologer->rate_per_minute }}</span>
                            </div>
                            <hr class="border-gray-200">
                            <div class="bg-yellow-50 p-3 rounded-lg">
                                <p class="text-xs text-yellow-800">
                                    <i class="fas fa-lightbulb mr-1"></i>
                                    <strong>How it works:</strong> Pay ₹10 to book. Session charges apply only during active chat.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-8 max-w-md mx-4 text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check text-green-600 text-2xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Booking Confirmed!</h3>
        <p class="text-gray-600 mb-6" id="success-message"></p>
        <div class="space-y-3">
            <button onclick="window.location.href='/appointments'" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors duration-200">
                View My Bookings
            </button>
            <button onclick="closeSuccessModal()" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-lg font-semibold transition-colors duration-200">
                Book Another Session
            </button>
        </div>
    </div>
</div>

<script>
function bookingForm() {
    return {
        loading: false,
        walletBalance: 0,
        notes: '',
        
        init() {
            this.loadWalletBalance();
        },
        
        async loadWalletBalance() {
            try {
                const response = await fetch('/wallet/data', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                });
                
                const data = await response.json();
                // Support both web endpoint shape { status: 'success', data: { balance } }
                // and API shape { success: true, data: { balance } }
                if ((data.status && data.status === 'success') || data.success) {
                    this.walletBalance = parseFloat(data.data?.balance ?? data.balance ?? 0);
                }
            } catch (error) {
                console.error('Error loading wallet balance:', error);
            }
        },
        
        async bookSession() {
            if (this.walletBalance < 10) {
                alert('Insufficient wallet balance. Please add money to your wallet.');
                return;
            }
            
            this.loading = true;
            
            try {
                const response = await fetch('/api/bookings/book', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        astrologer_id: {{ $astrologer->id }},
                        notes: this.notes
                    })
                });
                
                const data = await response.json();
                console.log('Booking response:', data);
                
                if (response.ok && data.success) {
                    // Update wallet balance
                    this.walletBalance = data.data.new_wallet_balance;
                    
                    // Show success modal
                    document.getElementById('success-message').textContent = 
                        `Your booking with ${data.data.booking.astrologer.name} has been confirmed! The astrologer will be notified and will start the session shortly.`;
                    document.getElementById('success-modal').classList.remove('hidden');
                    document.getElementById('success-modal').classList.add('flex');
                    
                    // Reset form
                    this.notes = '';
                } else {
                    throw new Error(data.message || 'Failed to create booking');
                }
            } catch (error) {
                console.error('Booking error:', error);
                alert('Error: ' + error.message);
            } finally {
                this.loading = false;
            }
        },
        
        formatCurrency(amount) {
            return '₹' + parseFloat(amount || 0).toLocaleString('en-IN', { 
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2 
            });
        }
    }
}

function closeSuccessModal() {
    document.getElementById('success-modal').classList.add('hidden');
    document.getElementById('success-modal').classList.remove('flex');
}
</script>
@endsection
