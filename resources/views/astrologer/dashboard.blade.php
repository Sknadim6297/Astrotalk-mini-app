@extends('layouts.astrologer-auth-guard')

@section('title', 'Astrologer Dashboard - AstroConnect')
@section('page-title', 'Astrologer Dashboard')

@section('astrologer-content')
<div class="p-6">
    <!-- Welcome Banner -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-2" x-data x-text="'Welcome back, ' + (user ? user.name : 'Astrologer')">
                        <i class="fas fa-star mr-2"></i>Welcome back, Astrologer
                    </h1>
                    <p class="text-purple-200">Manage your sessions and help clients find their cosmic path</p>
                </div>
                <div class="text-center">
                    <div class="bg-white/20 rounded-lg p-3">
                        <div class="text-2xl font-bold">⭐ 4.8</div>
                        <div class="text-purple-200 text-sm">Your Rating</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8" x-data="dashboardStats()" x-init="loadStats()">
        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.pending">0</p>
                    <p class="text-sm text-gray-600">Pending Requests</p>
                    <div class="flex items-center mt-1">
                        <span class="text-yellow-500 text-xs font-medium" x-show="stats.pending > 0">⏳ Needs attention</span>
                        <span class="text-gray-400 text-xs" x-show="stats.pending === 0">All caught up!</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-play text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.active">0</p>
                    <p class="text-sm text-gray-600">Active Sessions</p>
                    <div class="flex items-center mt-1">
                        <span class="text-green-500 text-xs font-medium" x-show="stats.active > 0">🟢 Live now</span>
                        <span class="text-gray-400 text-xs" x-show="stats.active === 0">No active sessions</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.completed">0</p>
                    <p class="text-sm text-gray-600">Completed Today</p>
                    <div class="flex items-center mt-1">
                        <span class="text-blue-500 text-xs font-medium">✅ Sessions done</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-rupee-sign text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-2xl font-bold text-gray-900" x-text="'₹' + stats.earnings">₹0</p>
                    <p class="text-sm text-gray-600">Today's Earnings</p>
                    <div class="flex items-center mt-1">
                        <span class="text-purple-500 text-xs font-medium">💰 Revenue earned</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bookings Management -->
    <div x-data="bookingsManager()" x-init="loadBookings()">
        <!-- Enhanced Tabs -->
        <div class="bg-white rounded-xl shadow-lg mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button @click="activeTab = 'pending'" 
                            :class="activeTab === 'pending' ? 'border-purple-500 text-purple-600 bg-purple-50' : 'border-transparent text-gray-500'"
                            class="py-4 px-4 border-b-2 font-medium text-sm rounded-t-lg transition-colors">
                        <i class="fas fa-clock mr-2"></i>Pending Requests
                        <span x-show="pendingBookings.length > 0" 
                              class="ml-2 bg-red-100 text-red-600 py-0.5 px-2 rounded-full text-xs font-medium"
                              x-text="pendingBookings.length"></span>
                    </button>
                    <button @click="activeTab = 'active'" 
                            :class="activeTab === 'active' ? 'border-purple-500 text-purple-600 bg-purple-50' : 'border-transparent text-gray-500'"
                            class="py-4 px-4 border-b-2 font-medium text-sm rounded-t-lg transition-colors">
                        <i class="fas fa-comments mr-2"></i>Active Sessions
                        <span x-show="activeBookings.length > 0" 
                              class="ml-2 bg-green-100 text-green-600 py-0.5 px-2 rounded-full text-xs font-medium"
                              x-text="activeBookings.length"></span>
                    </button>
                    <button @click="activeTab = 'completed'" 
                            :class="activeTab === 'completed' ? 'border-purple-500 text-purple-600 bg-purple-50' : 'border-transparent text-gray-500'"
                            class="py-4 px-4 border-b-2 font-medium text-sm rounded-t-lg transition-colors">
                        <i class="fas fa-history mr-2"></i>Recent History
                    </button>
                </nav>
            </div>
                        </button>
                    </nav>
            <!-- Loading State -->
            <div x-show="loading" class="flex justify-center items-center py-12">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin text-3xl text-purple-600 mb-4"></i>
                    <p class="text-gray-600">Loading your bookings...</p>
                </div>
            </div>

            <!-- Pending Bookings Tab -->
            <div x-show="activeTab === 'pending' && !loading" class="p-6">
                <div x-show="pendingBookings.length === 0" class="text-center py-12">
                    <div class="mb-4">
                        <i class="fas fa-calendar-check text-gray-400 text-5xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">All Caught Up!</h3>
                    <p class="text-gray-600">No pending booking requests at the moment.</p>
                    <p class="text-gray-500 text-sm mt-2">New client requests will appear here when they book a session with you.</p>
                </div>

                <div x-show="pendingBookings.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="booking in pendingBookings" :key="booking.id">
                        <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl p-6 border border-yellow-200 hover:shadow-lg transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1 rounded-full">
                                    ⏰ Pending Request
                                </span>
                                <span class="text-sm text-gray-500 font-mono" x-text="'#' + booking.id"></span>
                            </div>

                            <div class="flex items-center space-x-3 mb-4">
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-purple-600 text-lg"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-900" x-text="booking.user.name"></h3>
                                    <p class="text-sm text-gray-600" x-text="booking.user.email"></p>
                                </div>
                            </div>

                            <div class="bg-white rounded-lg p-3 mb-4 space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">💰 Rate per minute:</span>
                                    <span class="font-semibold text-green-600" x-text="'₹' + booking.per_minute_rate"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">🎯 Booking fee:</span>
                                    <span class="font-semibold text-green-600">₹10 (paid)</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">⏰ Requested:</span>
                                    <span class="font-semibold" x-text="formatDateTime(booking.created_at)"></span>
                                </div>
                            </div>

                            <div x-show="booking.notes" class="mb-4 p-3 bg-white rounded-lg border border-gray-200">
                                <p class="text-xs text-gray-600 font-medium mb-1">📝 Client's Message:</p>
                                <p class="text-sm text-gray-700 italic" x-text="booking.notes"></p>
                            </div>

                            <button @click="startSession(booking)" 
                                    class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white py-3 px-4 rounded-lg font-semibold transition-all duration-200 transform hover:scale-105">
                                <i class="fas fa-play mr-2"></i>Accept & Start Session
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Active Sessions Tab -->
            <div x-show="activeTab === 'active' && !loading" class="p-6">
                <div x-show="activeBookings.length === 0" class="text-center py-12">
                    <div class="mb-4">
                        <i class="fas fa-comments text-gray-400 text-5xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Active Sessions</h3>
                    <p class="text-gray-600">Start a session from pending requests to begin chatting with clients.</p>
                </div>

                <div x-show="activeBookings.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="booking in activeBookings" :key="booking.id">
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200 hover:shadow-lg transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                                    🟢 Live Session
                                </span>
                                <span class="text-sm text-gray-500 font-mono" x-text="'#' + booking.id"></span>
                            </div>

                            <div class="flex items-center space-x-3 mb-4">
                                <div class="relative">
                                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-purple-600 text-lg"></i>
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-900" x-text="booking.user.name"></h3>
                                    <p class="text-sm text-gray-600" x-text="booking.user.email"></p>
                                </div>
                            </div>

                            <div class="bg-white rounded-lg p-3 mb-4 space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">🕒 Session started:</span>
                                    <span class="font-semibold" x-text="formatDateTime(booking.started_at)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">⏱️ Duration:</span>
                                    <span class="font-semibold text-blue-600" x-text="getSessionDuration(booking.started_at)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">💰 Earning rate:</span>
                                    <span class="font-semibold text-green-600" x-text="'₹' + booking.per_minute_rate + '/min'"></span>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <button @click="openChat(booking)" 
                                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-2.5 px-4 rounded-lg font-semibold transition-all duration-200">
                                    <i class="fas fa-comments mr-2"></i>Continue Chat
                                </button>
                                <button @click="endSession(booking)" 
                                        class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white py-2.5 px-4 rounded-lg font-semibold transition-all duration-200">
                                    <i class="fas fa-stop mr-2"></i>End Session
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Completed Sessions Tab -->
            <div x-show="activeTab === 'completed' && !loading" class="p-6">
                <div x-show="completedBookings.length === 0" class="text-center py-12">
                    <div class="mb-4">
                        <i class="fas fa-history text-gray-400 text-5xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Recent Sessions</h3>
                    <p class="text-gray-600">Your completed sessions will appear here.</p>
                </div>

                <div x-show="completedBookings.length > 0" class="space-y-4">
                    <template x-for="booking in completedBookings" :key="booking.id">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200 hover:shadow-lg transition-shadow">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-purple-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900" x-text="booking.user.name"></h3>
                                        <p class="text-sm text-gray-600" x-text="formatDateTime(booking.created_at)"></p>
                                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded-full mt-1 inline-block">
                                            ✅ Completed
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-green-600" x-text="'₹' + booking.total_amount"></p>
                                    <p class="text-sm text-gray-600" x-text="booking.duration_minutes + ' minutes'"></p>
                                    <p class="text-xs text-gray-500">Total earned</p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Footer -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6 text-center">
            <div class="text-3xl font-bold text-purple-600 mb-2">⭐ 4.8</div>
            <p class="text-gray-600 text-sm">Average Rating</p>
            <p class="text-xs text-gray-500 mt-1">Based on 127 reviews</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 text-center">
            <div class="text-3xl font-bold text-green-600 mb-2">₹2,450</div>
            <p class="text-gray-600 text-sm">This Week's Earnings</p>
            <p class="text-xs text-gray-500 mt-1">+15% from last week</p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">45</div>
            <p class="text-gray-600 text-sm">Sessions This Month</p>
            <p class="text-xs text-gray-500 mt-1">Above average activity</p>
        </div>
    </div>
</div>

<script>
function dashboardStats() {
    return {
        stats: {
            pending: 0,
            active: 0,
            completed: 0,
            earnings: 0
        },
        
        async loadStats() {
            setTimeout(() => {
                this.stats = {
                    pending: 2,
                    active: 1,
                    completed: 5,
                    earnings: 450
                };
            }, 500);
        }
    }
}

function bookingsManager() {
    return {
        loading: true,
        activeTab: 'pending',
        allBookings: [],
        
        get pendingBookings() {
            return this.allBookings.filter(b => b.status === 'pending');
        },
        
        get activeBookings() {
            return this.allBookings.filter(b => b.status === 'active');
        },
        
        get completedBookings() {
            return this.allBookings.filter(b => b.status === 'completed');
        },
        
        async loadBookings() {
            try {
                const token = localStorage.getItem('api_token');
                const response = await fetch('/api/bookings/astrologer-bookings', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    this.allBookings = data.data.bookings;
                }
            } catch (error) {
                console.error('Error loading bookings:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async startSession(booking) {
            if (!confirm('Start chat session with ' + booking.user.name + '?')) {
                return;
            }
            
            try {
                const token = localStorage.getItem('api_token');
                const response = await fetch(`/api/bookings/${booking.id}/start`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    const index = this.allBookings.findIndex(b => b.id === booking.id);
                    if (index !== -1) {
                        this.allBookings[index] = data.data.booking;
                    }
                    
                    this.activeTab = 'active';
                    alert('Session started! You can now chat with the client.');
                } else {
                    alert(data.message || 'Failed to start session');
                }
            } catch (error) {
                console.error('Error starting session:', error);
                alert('Error starting session');
            }
        },
        
        async endSession(booking) {
            if (!confirm('End chat session? This will calculate the final amount and complete the booking.')) {
                return;
            }
            
            try {
                const token = localStorage.getItem('api_token');
                const response = await fetch(`/api/bookings/${booking.id}/end`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    const index = this.allBookings.findIndex(b => b.id === booking.id);
                    if (index !== -1) {
                        this.allBookings[index] = data.data.booking;
                    }
                    
                    this.activeTab = 'completed';
                    
                    const summary = data.data.session_summary;
                    alert(`Session completed!\nDuration: ${summary.duration_minutes} minutes\nEarning: ₹${summary.session_cost}`);
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
        
        formatDateTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-IN', {
                day: 'numeric',
                month: 'short',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        
        getSessionDuration(startTime) {
            const start = new Date(startTime);
            const now = new Date();
            const diffMinutes = Math.floor((now - start) / (1000 * 60));
            return diffMinutes + ' min';
        }
    }
}
</script>
@endsection
