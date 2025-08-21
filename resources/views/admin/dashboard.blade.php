@extends('layouts.admin')

@section('title', 'Admin Dashboard - AstroConnect')

@section('content')
<div class="p-6">
    <!-- Welcome Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-2">
                        <i class="fas fa-bolt mr-2"></i>Welcome to Admin Dashboard
                    </h1>
                    <p class="text-red-200">Manage your AstroConnect platform efficiently</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold" id="current-time"></div>
                    <div class="text-red-200 text-sm" id="current-date"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Users</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-star text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Astrologers</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_astrologers'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Pending Approval</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_astrologers'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Approved Astrologers</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['approved_astrologers'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Registrations -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Recent Registrations</h2>
                <a href="{{ route('admin.users') }}" class="text-sm text-red-600 hover:text-red-500">View all →</a>
            </div>
            <div class="space-y-3">
                @forelse($recent_registrations as $user)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=dc2626&color=fff&size=40" 
                                 alt="{{ $user->name }}" class="w-10 h-10 rounded-full">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ ucfirst($user->role) }} • {{ $user->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                   {{ $user->role === 'astrologer' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No recent registrations</p>
                @endforelse
            </div>
        </div>

        <!-- Pending Astrologers -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Pending Astrologer Approvals</h2>
                <a href="{{ route('admin.astrologers') }}" class="text-sm text-red-600 hover:text-red-500">View all →</a>
            </div>
            <div class="space-y-3">
                @forelse($pending_astrologers as $astrologer)
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                        <div class="flex items-center">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($astrologer->user->name) }}&background=7c3aed&color=fff&size=40" 
                                 alt="{{ $astrologer->user->name }}" class="w-10 h-10 rounded-full">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $astrologer->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $astrologer->experience }} years exp • ₹{{ $astrologer->per_minute_rate }}/min</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="approveAstrologer({{ $astrologer->id }})" 
                                    class="text-green-600 hover:text-green-500">
                                <i class="fas fa-check"></i>
                            </button>
                            <button onclick="rejectAstrologer({{ $astrologer->id }})" 
                                    class="text-red-600 hover:text-red-500">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No pending approvals</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function updateDateTime() {
    const now = new Date();
    document.getElementById('current-time').textContent = now.toLocaleTimeString();
    document.getElementById('current-date').textContent = now.toLocaleDateString();
}

// Update time every second
setInterval(updateDateTime, 1000);
updateDateTime(); // Initial call

async function approveAstrologer(id) {
    if (!confirm('Are you sure you want to approve this astrologer?')) return;
    
    try {
        const response = await fetch(`/admin/astrologers/${id}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast(data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('An error occurred', 'error');
    }
}

async function rejectAstrologer(id) {
    const notes = prompt('Reason for rejection (optional):');
    if (notes === null) return;
    
    try {
        const response = await fetch(`/admin/astrologers/${id}/reject`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ notes })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast(data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('An error occurred', 'error');
    }
}
</script>
@endsection
