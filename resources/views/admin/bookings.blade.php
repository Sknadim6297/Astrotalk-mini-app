@extends('layouts.admin')

@section('title', 'Booking Management - Admin Panel')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Booking Management</h1>
            <p class="text-gray-600">Monitor and manage all consultation sessions</p>
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
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active_sessions'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Completed Today</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['completed_today'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_bookings'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-rupee-sign text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Revenue Today</p>
                    <p class="text-2xl font-bold text-gray-900">₹{{ number_format($stats['total_revenue_today'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Session Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participants</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900">
                                        Session #{{ $booking->id }}
                                    </div>
                                    <div class="text-gray-500">
                                        {{ $booking->created_at->format('M d, Y g:i A') }}
                                    </div>
                                    @if($booking->notes)
                                        <div class="text-xs text-blue-600 mt-1">
                                            Has notes
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900">
                                        <i class="fas fa-user text-blue-600 mr-1"></i>{{ $booking->user->name ?? 'Unknown User' }}
                                    </div>
                                    <div class="text-gray-500">
                                        <i class="fas fa-star text-purple-600 mr-1"></i>{{ optional($booking->astrologer)->user->name ?? optional($booking->astrologer)->name ?? 'Unknown Astrologer' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($booking->duration_minutes)
                                    {{ floor($booking->duration_minutes / 60) }}h {{ $booking->duration_minutes % 60 }}m
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="font-medium text-gray-900">
                                        ₹{{ number_format($booking->total_amount, 2) }}
                                    </div>
                                    @if($booking->booking_fee && $booking->session_charges)
                                        <div class="text-xs text-gray-500">
                                            ₹{{ $booking->booking_fee }} + ₹{{ $booking->session_charges }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
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
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button onclick="viewDetails({{ $booking->id }})" 
                                        class="text-blue-600 hover:text-blue-900"
                                        title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                
                                <!-- Trace/View Conversation Button -->
                                <button onclick="viewConversation({{ $booking->id }})" 
                                        class="text-purple-600 hover:text-purple-900"
                                        title="View Conversation">
                                    <i class="fas fa-comments"></i>
                                </button>
                                
                                @if($booking->status === 'active')
                                    <button onclick="monitorSession({{ $booking->id }})" 
                                            class="text-green-600 hover:text-green-900" 
                                            title="Monitor Live Session">
                                        <i class="fas fa-broadcast-tower"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No bookings found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($bookings->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Booking Details</h3>
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
@endsection

@section('scripts')
<script>
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
                            <label class="block text-sm font-medium text-gray-700">Session ID</label>
                            <p class="text-sm text-gray-900">#${booking.id}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <p class="text-sm text-gray-900 capitalize">${booking.status}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Client</label>
                            <p class="text-sm text-gray-900">${booking.user?.name || 'Unknown User'} (${booking.user?.email || '—'})</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Astrologer</label>
                            <p class="text-sm text-gray-900">${(booking.astrologer && (booking.astrologer.user && booking.astrologer.user.name)) ? booking.astrologer.user.name : (booking.astrologer?.name || 'Unknown Astrologer')}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Duration</label>
                            <p class="text-sm text-gray-900">${data.data.duration_formatted}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Per Minute Rate</label>
                            <p class="text-sm text-gray-900">₹${booking.per_minute_rate}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Booking Fee</label>
                            <p class="text-sm text-gray-900">₹${booking.booking_fee || 0}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Session Charges</label>
                            <p class="text-sm text-gray-900">₹${booking.session_charges || 0}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Amount</label>
                            <p class="text-sm text-gray-900 font-semibold">₹${booking.total_amount}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Created At</label>
                            <p class="text-sm text-gray-900">${new Date(booking.created_at).toLocaleString()}</p>
                        </div>
                    </div>
                    
                    ${booking.notes ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Client Notes</label>
                            <div class="mt-1 p-3 bg-gray-50 rounded-md">
                                <p class="text-sm text-gray-900">${booking.notes}</p>
                            </div>
                        </div>
                    ` : ''}
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Started At</label>
                            <p class="text-sm text-gray-900">${booking.started_at ? new Date(booking.started_at).toLocaleString() : 'Not started'}</p>
                        </div>
                        ${booking.ended_at ? `
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Ended At</label>
                                <p class="text-sm text-gray-900">${new Date(booking.ended_at).toLocaleString()}</p>
                            </div>
                        ` : ''}
                    </div>
                    
                    ${booking.messages && booking.messages.length > 0 ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Messages Exchanged</label>
                            <p class="text-sm text-gray-900">${booking.messages.length} messages</p>
                        </div>
                    ` : ''}

                    ${ (booking.status === 'active' || (booking.messages && booking.messages.length > 0)) ? `
                        <div class="mt-4">
                            <button onclick="window.open('/chat/${booking.id}', '_blank')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">Open Chat</button>
                        </div>
                    ` : ''}
                </div>
            `;
            
            document.getElementById('modalContent').innerHTML = content;
            document.getElementById('detailsModal').classList.remove('hidden');
        } else {
            showToast('Failed to load booking details', 'error');
        }
    } catch (error) {
        console.error('Error loading details:', error);
        showToast('Failed to load booking details', 'error');
    }
}

function monitorSession(bookingId) {
    // Future: Implement real-time session monitoring
    showToast('Session monitoring feature coming soon!', 'info');
}

function viewConversation(bookingId) {
    // Open conversation in new window
    window.open(`/admin/conversations/${bookingId}`, '_blank');
}

function closeModal() {
    document.getElementById('detailsModal').classList.add('hidden');
}
</script>
@endsection
