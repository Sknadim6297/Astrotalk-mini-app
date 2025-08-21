@extends('layouts.app')

@section('title', 'Book Astrologer - {{ $astrologer->user->name }}')

@section('content')
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
                        <i class="fas fa-calendar-plus text-purple-600 mr-3"></i>Book Session
                    </h1>
                    <p class="text-gray-600 mt-1">Book a consultation with {{ $astrologer->user->name }}</p>
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
                                <h2 class="text-2xl font-bold text-gray-900">{{ $astrologer->user->name }}</h2>
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
                                        {{ $astrologer->experience }} years experience
                                    </span>
                                    <span class="flex items-center text-sm text-gray-600">
                                        <i class="fas fa-star mr-1 text-yellow-500"></i>
                                        4.5/5 (120 reviews)
                                    </span>
                                    <span class="flex items-center text-sm">
                                        @if($astrologer->is_online)
                                            <div class="w-2 h-2 bg-green-500 rounded-full mr-1"></div>
                                            <span class="text-green-600 font-medium">Online</span>
                                        @else
                                            <div class="w-2 h-2 bg-gray-400 rounded-full mr-1"></div>
                                            <span class="text-gray-500">Offline</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-2">About</h3>
                            <p class="text-gray-600 text-sm">
                                {{ $astrologer->bio ?? 'Experienced astrologer providing accurate predictions and guidance for all life matters.' }}
                            </p>
                        </div>

                        <!-- Languages -->
                        @if($astrologer->languages)
                        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-2">
                                <i class="fas fa-language text-blue-600 mr-2"></i>Languages
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @php
                                    $languages = is_array($astrologer->languages) ? $astrologer->languages : explode(',', $astrologer->languages);
                                @endphp
                                @foreach($languages as $language)
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">{{ trim($language) }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Book Section -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden" x-data="bookingManager()">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-calendar-check text-gray-500 mr-2"></i>Book Your Session
                        </h3>
                    </div>

                    <div class="p-6">
                        <!-- Current Balance -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                            <div class="flex items-center justify-between">
                                <span class="text-green-800 font-medium">Your Wallet Balance:</span>
                                <span class="text-xl font-bold text-green-600" x-text="formatCurrency(walletBalance)">₹0.00</span>
                            </div>
                        </div>

                        <!-- Booking Fee Notice -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                                <div>
                                    <h4 class="font-medium text-blue-800">How It Works</h4>
                                    <ul class="text-sm text-blue-600 mt-1 space-y-1">
                                        <li>• Booking fee: ₹10 (charged immediately)</li>
                                        <li>• Session rate: ₹{{ $astrologer->per_minute_rate }}/minute (charged during active chat)</li>
                                        <li>• You can end the session anytime</li>
                                    </ul>
                                </div>
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

                        <!-- Book Button -->
                        <button @click="bookNow()" 
                                :disabled="loading || walletBalance < 10"
                                :class="(walletBalance >= 10) ? 'bg-purple-600 hover:bg-purple-700' : 'bg-gray-400 cursor-not-allowed'"
                                class="w-full text-white py-4 rounded-lg font-semibold text-lg transition-all duration-200">
                            <span x-show="!loading">
                                <i class="fas fa-calendar-plus mr-2"></i>
                                <span x-show="walletBalance >= 10 && astrologerOnline">Start Chat Now (₹10 booking fee)</span>
                                <span x-show="walletBalance >= 10 && !astrologerOnline">Book Session - Start Soon (₹10 booking fee)</span>
                                <span x-show="walletBalance < 10">Insufficient Balance</span>
                            </span>
                            <span x-show="loading">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Processing...
                            </span>
                        </button>

                        <div x-show="walletBalance < 10" class="mt-4 text-center">
                            <a href="/wallet" class="text-purple-600 hover:text-purple-700 font-medium">
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
                                <span class="font-semibold text-gray-900">₹{{ $astrologer->per_minute_rate }}</span>
                            </div>
                            <hr class="border-gray-200">
                            <div class="bg-yellow-50 p-3 rounded-lg">
                                <p class="text-xs text-yellow-800">
                                    <i class="fas fa-lightbulb mr-1"></i>
                                    <strong>Example:</strong> A 15-minute session costs ₹10 (booking) + ₹{{ $astrologer->per_minute_rate * 15 }} (session) = ₹{{ 10 + ($astrologer->per_minute_rate * 15) }} total.
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
            <button onclick="startChat()" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors duration-200">
                Start Chat Now
            </button>
            <button onclick="window.location.href='/appointments'" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-lg font-semibold transition-colors duration-200">
                View My Appointments
            </button>
        </div>
    </div>
</div>

<script>
function bookingManager() {
    return {
        loading: false,
        walletBalance: 0,
        notes: '',
        astrologerOnline: {{ $astrologer->is_online ? 'true' : 'false' }},
        bookingResponse: null,
        
        init() {
            this.loadWalletBalance();
        },
        
        async loadWalletBalance() {
            try {
                @auth
                // Use the session-authenticated web endpoint which returns JSON for session users.
                // This avoids issues where API routes use sanctum auth and the browser session isn't recognised.
                const response = await fetch('/wallet/data', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                });

                const data = await response.json();
                // Support both API style { success: true, data: { balance: ... } }
                // and web style { status: 'success', data: { balance: ... } }
                const balance = (data && data.data && (data.data.balance ?? data.data.balance) ) ? (data.data.balance) : 0;
                // Fallback: older shape might put balance at data.balance
                this.walletBalance = parseFloat(data.data?.balance ?? data.balance ?? 0);
                @else
                window.location.href = '/login';
                @endauth
            } catch (error) {
                console.error('Error loading wallet balance:', error);
                this.showToast('Failed to load wallet balance', 'error');
            }
        },

        async bookNow() {
            if (this.loading) return;
            
            this.loading = true;

            try {
                const response = await fetch('/book-astrologer', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        astrologer_id: {{ $astrologer->user_id }},
                        notes: this.notes
                    })
                });

                const data = await response.json();

                // BookingController::create returns { success: true, message, data: { booking_id, chat_url }}
                if (data.success) {
                    this.bookingResponse = data.data;
                    this.showSuccessModal(data.message || 'Booking created successfully');
                    await this.loadWalletBalance(); // Refresh balance
                } else {
                    this.showToast(data.message || 'Booking failed', 'error');
                }
            } catch (error) {
                console.error('Booking error:', error);
                this.showToast('Failed to create booking. Please try again.', 'error');
            } finally {
                this.loading = false;
            }
        },

        formatCurrency(amount) {
            return '₹' + parseFloat(amount || 0).toFixed(2);
        },

        showSuccessModal(message) {
            document.getElementById('success-message').textContent = message;
            document.getElementById('success-modal').classList.remove('hidden');
            document.getElementById('success-modal').classList.add('flex');
        },

        showToast(message, type = 'success') {
            // Use existing toast function
            if (typeof showToast === 'function') {
                showToast(message, type);
            } else {
                alert(message);
            }
        }
    }
}

function startChat() {
    // Get booking response and redirect to chat
    const booking = window.bookingManager?.bookingResponse;
    if (booking && booking.chat_url) {
        window.location.href = booking.chat_url;
    } else {
        window.location.href = '/appointments';
    }
}

function closeSuccessModal() {
    document.getElementById('success-modal').classList.add('hidden');
    document.getElementById('success-modal').classList.remove('flex');
}
</script>
@endsection
