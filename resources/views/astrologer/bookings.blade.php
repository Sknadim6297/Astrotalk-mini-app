@extends('layouts.astrologer-auth-guard')

@section('title', 'My Bookings - AstroConnect')

@section('astrologer-content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-green-600 to-blue-700 rounded-xl p-6 text-white">
            <div class="flex items-center">
                <div class="w-16 h-16 bg-white/20 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-calendar-check text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold mb-2">My Bookings</h1>
                    <p class="text-green-200">Manage your consultation sessions</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-play text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Sessions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $bookings->where('status', 'active')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Completed</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $bookings->where('status', 'completed')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Sessions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $bookings->total() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-rupee-sign text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Earnings</p>
                    <p class="text-2xl font-bold text-gray-900">₹{{ number_format($bookings->sum('session_charges'), 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bookings List -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Sessions</h3>
        </div>

        @if($bookings->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($bookings as $booking)
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">
                                        {{ $booking->user->name ?? 'Unknown User' }}
                                    </h4>
                                    <p class="text-sm text-gray-600">{{ $booking->user->email ?? '—' }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $booking->created_at->format('M d, Y g:i A') }}
                                        @if($booking->duration_minutes)
                                            • Duration: {{ floor($booking->duration_minutes / 60) }}h {{ $booking->duration_minutes % 60 }}m
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-4">
                                <!-- Status -->
                                <div class="text-right">
                                    @php
                                        $statusColors = [
                                            'active' => 'bg-green-100 text-green-800',
                                            'completed' => 'bg-blue-100 text-blue-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            'scheduled' => 'bg-yellow-100 text-yellow-800'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($booking->status) }}
                                        @if($booking->status === 'completed' && $booking->ended_by)
                                            • {{ $booking->ended_by === 'user' ? 'User ended' : 'You ended' }}
                                        @endif
                                    </span>
                                    
                                    <div class="text-sm font-semibold text-gray-900 mt-1">
                                        ₹{{ number_format($booking->session_charges ?? 0, 2) }}
                                        @if($booking->duration_minutes)
                                            <span class="text-xs text-gray-500">({{ $booking->duration_minutes }} min)</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex space-x-2">
                                    @if($booking->status === 'active')
                                        <a href="{{ route('chat.interface', $booking->id) }}" 
                                           class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-sm font-medium">
                                            <i class="fas fa-comments mr-1"></i>Continue Chat
                                        </a>
                                        <button onclick="endSession({{ $booking->id }})" 
                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-sm font-medium">
                                            <i class="fas fa-stop mr-1"></i>End Session
                                        </button>
                                    @elseif($booking->status === 'completed')
                                        <button onclick="viewDetails({{ $booking->id }})" 
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-sm font-medium">
                                            <i class="fas fa-eye mr-1"></i>View Details
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($booking->notes)
                            <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                                <p class="text-sm text-blue-700">
                                    <strong>Client Notes:</strong> {{ $booking->notes }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($bookings->hasPages())
                <div class="px-6 py-3 border-t border-gray-200">
                    {{ $bookings->links() }}
                </div>
            @endif
        @else
            <div class="p-12 text-center">
                <i class="fas fa-calendar-times text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No bookings yet</h3>
                <p class="text-gray-600 mb-6">Your consultation sessions will appear here</p>
                <a href="/astrologer/availability" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold">
                    <i class="fas fa-clock mr-2"></i>Manage Availability
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Session Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="modalContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
async function endSession(bookingId) {
    if (!confirm('Are you sure you want to end this session? This action cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch(`/bookings/${bookingId}/end`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Session ended successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Failed to end session', 'error');
        }
    } catch (error) {
        console.error('Error ending session:', error);
        showToast('Failed to end session', 'error');
    }
}

async function viewDetails(bookingId) {
    try {
        const response = await fetch(`/bookings/${bookingId}/details`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        });
        
        const data = await response.json();
        
        if (data.success) {
            const booking = data.data.booking;
            const content = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Client</label>
                            <p class="text-sm text-gray-900">${booking.user?.name || 'Unknown User'}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <p class="text-sm text-gray-900 capitalize">${booking.status}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Duration</label>
                            <p class="text-sm text-gray-900">${data.data.duration_formatted}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Session Earnings</label>
                            <p class="text-sm text-gray-900">₹${booking.session_charges || 0}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Booking Fee</label>
                            <p class="text-sm text-gray-900">₹${booking.booking_fee || 0}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Amount</label>
                            <p class="text-sm text-gray-900">₹${booking.total_amount || 0}</p>
                        </div>
                    </div>
                    ${booking.notes ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Client Notes</label>
                            <p class="text-sm text-gray-900">${booking.notes}</p>
                        </div>
                    ` : ''}
                    <div class="grid grid-cols-2 gap-4">
                        ${booking.started_at ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Started At</label>
                            <p class="text-sm text-gray-900">${new Date(booking.started_at).toLocaleString()}</p>
                        </div>
                        ` : ''}
                        ${booking.ended_at ? `
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Ended At</label>
                                <p class="text-sm text-gray-900">${new Date(booking.ended_at).toLocaleString()}</p>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
            
            document.getElementById('modalContent').innerHTML = content;
            document.getElementById('detailsModal').classList.remove('hidden');
        } else {
            showToast('Failed to load session details', 'error');
        }
    } catch (error) {
        console.error('Error loading details:', error);
        showToast('Failed to load session details', 'error');
    }
}

function closeModal() {
    document.getElementById('detailsModal').classList.add('hidden');
}

function showToast(message, type = 'success') {
    // Simple toast implementation
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        document.body.removeChild(toast);
    }, 3000);
}
</script>
@endsection
