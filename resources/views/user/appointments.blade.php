@extends('layouts.auth-guard')

@section('title', 'My Appointments')

@section('protected-content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-calendar-alt text-blue-600 mr-3"></i>My Appointments
                    </h1>
                    <p class="text-gray-600 mt-1">View and manage your astrology consultations</p>
                </div>
                <a href="/astrologers" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
                    <i class="fas fa-plus mr-2"></i>Book New Session
                </a>
            </div>
        </div>

        <!-- Bookings List -->
        <div x-data="appointmentsManager()" x-init="loadBookings()">
            <!-- Loading State -->
            <div x-show="loading" class="flex justify-center items-center py-12">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin text-3xl text-purple-600 mb-4"></i>
                    <p class="text-gray-600">Loading your appointments...</p>
                </div>
            </div>

            <!-- No Bookings -->
            <div x-show="!loading && bookings.length === 0" class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar-times text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Appointments Yet</h3>
                <p class="text-gray-600 mb-6">You haven't booked any astrology sessions yet.</p>
                <a href="/astrologers" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>Book Your First Session
                </a>
            </div>

            <!-- Bookings Grid -->
            <div x-show="!loading && bookings.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="booking in bookings" :key="booking.id">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <!-- Status Badge -->
                        <div class="px-6 pt-4">
                            <div class="flex justify-between items-center">
                                <span x-text="'#' + booking.id" class="text-sm font-mono text-gray-500"></span>
                                <span :class="getStatusBadgeClass(booking.status)" 
                                      class="px-3 py-1 rounded-full text-xs font-semibold"
                                      x-text="booking.status.charAt(0).toUpperCase() + booking.status.slice(1)"></span>
                            </div>
                        </div>

                        <!-- Astrologer Info -->
                        <div class="px-6 py-4">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-purple-600"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900" x-text="booking.astrologer ? booking.astrologer.name : 'Astrologer'"></h3>
                                    <p class="text-sm text-purple-600" x-text="booking.astrologer ? (booking.astrologer.specialization || '') : 'Astrology'"></p>
                                </div>
                            </div>

                            <!-- Booking Details -->
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Rate:</span>
                                    <span class="font-semibold" x-text="'₹' + (booking.per_minute_rate ?? booking.astrologer?.per_minute_rate || 0) + '/min'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Booking Fee:</span>
                                    <span class="font-semibold" x-text="'₹' + booking.booking_fee"></span>
                                </div>
                                <div x-show="booking.duration_minutes > 0" class="flex justify-between">
                                    <span class="text-gray-600">Duration:</span>
                                    <span class="font-semibold" x-text="booking.duration_minutes + ' min'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Paid:</span>
                                    <span class="font-semibold text-green-600" x-text="'₹' + booking.total_amount"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Booked:</span>
                                    <span class="font-semibold" x-text="formatDate(booking.created_at)"></span>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div x-show="booking.notes" class="mt-4 p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-600 font-medium mb-1">Notes:</p>
                                <p class="text-sm text-gray-700" x-text="booking.notes"></p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="px-6 pb-4">
                            <div class="flex space-x-2">
                                <!-- Pending: Can start or cancel -->
                                <template x-if="booking.status === 'pending'">
                                    <div class="flex space-x-2 w-full">
                                        <button @click="startSession(booking)" 
                                                class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg font-semibold text-sm transition-colors duration-200">
                                            <i class="fas fa-play mr-1"></i>Start Chat
                                        </button>
                                        <button @click="cancelBooking(booking)" 
                                                class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg font-semibold text-sm transition-colors duration-200">
                                            <i class="fas fa-times mr-1"></i>Cancel
                                        </button>
                                    </div>
                                </template>

                                <!-- Active: Show chat interface -->
                                <template x-if="booking.status === 'active'">
                                    <div class="w-full">
                                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-green-800 font-medium">Session Active</span>
                                                <span class="text-green-600 text-sm" x-text="'Started: ' + formatTime(booking.started_at)"></span>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button @click="openChat(booking)" 
                                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg font-semibold text-sm transition-colors duration-200">
                                                <i class="fas fa-comments mr-1"></i>Continue Chat
                                            </button>
                                            <button @click="endSession(booking)" 
                                                    class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg font-semibold text-sm transition-colors duration-200">
                                                <i class="fas fa-stop mr-1"></i>End Session
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <!-- Completed: Show summary -->
                                <template x-if="booking.status === 'completed'">
                                    <div class="w-full">
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                            <div class="text-center">
                                                <i class="fas fa-check-circle text-blue-600 mb-2"></i>
                                                <p class="text-blue-800 font-medium text-sm">Session Completed</p>
                                                <p class="text-blue-600 text-xs" x-text="formatDate(booking.ended_at)"></p>
                                                <p class="text-blue-500 text-xs mt-1" x-text="booking.ended_by ? 'Ended by: ' + (booking.ended_by === 'user' ? 'You' : 'Astrologer') : ''"></p>
                                                <div class="mt-2 text-xs text-blue-700">
                                                    <span x-text="booking.duration_minutes ? 'Duration: ' + booking.duration_minutes + ' min' : ''"></span>
                                                    <span x-show="booking.total_amount" x-text="booking.total_amount ? ' • Total: ₹' + booking.total_amount : ''"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Cancelled: Show refund info -->
                                <template x-if="booking.status === 'cancelled'">
                                    <div class="w-full">
                                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                            <div class="text-center">
                                                <i class="fas fa-ban text-gray-600 mb-2"></i>
                                                <p class="text-gray-800 font-medium text-sm">Booking Cancelled</p>
                                                <p class="text-gray-600 text-xs">Refund processed</p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function appointmentsManager() {
    return {
        loading: true,
        bookings: [],
        
        async loadBookings() {
            try {
                const response = await fetch('/my-bookings', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                });
                
                const data = await response.json();
                if (data.success) {
                    this.bookings = data.data.bookings;
                }
            } catch (error) {
                console.error('Error loading bookings:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async startSession(booking) {
            try {
                const response = await fetch(`/bookings/${booking.id}/start`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                });
                
                const data = await response.json();
                if (data.success) {
                    // Update booking status
                    const index = this.bookings.findIndex(b => b.id === booking.id);
                    if (index !== -1) {
                        this.bookings[index].status = 'active';
                        this.bookings[index].started_at = new Date().toISOString();
                    }
                    alert('Session started! You can now chat with the astrologer.');
                } else {
                    alert(data.message || 'Failed to start session');
                }
            } catch (error) {
                console.error('Error starting session:', error);
                alert('Error starting session');
            }
        },
        
        async cancelBooking(booking) {
            if (!confirm('Are you sure you want to cancel this booking? Your booking fee will be refunded.')) {
                return;
            }
            
            try {
                const response = await fetch(`/bookings/${booking.id}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                });
                
                const data = await response.json();
                if (data.success) {
                    // Update booking status
                    const index = this.bookings.findIndex(b => b.id === booking.id);
                    if (index !== -1) {
                        this.bookings[index].status = 'cancelled';
                    }
                    alert(`Booking cancelled. ₹${data.data.refunded_amount} has been refunded to your wallet.`);
                } else {
                    alert(data.message || 'Failed to cancel booking');
                }
            } catch (error) {
                console.error('Error cancelling booking:', error);
                alert('Error cancelling booking');
            }
        },

        async endSession(booking) {
            if (!confirm('End this chat session? This will calculate the final amount and complete the booking.')) {
                return;
            }
            
            try {
                const response = await fetch(`/bookings/${booking.id}/end`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                });
                
                const data = await response.json();
                if (data.success) {
                    // Update booking status
                    const index = this.bookings.findIndex(b => b.id === booking.id);
                    if (index !== -1) {
                        this.bookings[index].status = 'completed';
                        this.bookings[index].ended_at = new Date().toISOString();
                    }
                    const summary = data.data.session_summary;
                    alert(`Session completed by user!\nDuration: ${summary.duration_minutes} minutes\nSession Charges: ₹${summary.session_charges}\nTotal Amount: ₹${summary.total_amount}`);
                } else {
                    alert(data.message || 'Failed to end session');
                }
            } catch (error) {
                console.error('Error ending session:', error);
                alert('Error ending session');
            }
        },

        openChat(booking) {
            // Navigate to chat interface for active booking
            if (booking.status === 'active') {
                window.location.href = `/chat/${booking.id}`;
            } else {
                alert('Chat is only available for active sessions');
            }
        },
        
        getStatusBadgeClass(status) {
            const classes = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'active': 'bg-green-100 text-green-800',
                'completed': 'bg-blue-100 text-blue-800',
                'cancelled': 'bg-gray-100 text-gray-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
        
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-IN', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
        },
        
        formatTime(dateString) {
            return new Date(dateString).toLocaleTimeString('en-IN', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }
}
</script>
@endsection
