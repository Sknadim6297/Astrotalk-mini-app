@extends('layouts.admin')

@section('title', 'Manage Users - Admin Panel')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manage Users</h1>
            <p class="text-gray-600">View and manage all registered users</p>
        </div>
        <div class="flex space-x-2">
            <div class="bg-white px-3 py-2 rounded-lg border">
                <span class="text-sm text-gray-600">Total: {{ $users->total() }}</span>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wallet Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=dc2626&color=fff&size=40" 
                                         alt="{{ $user->name }}" class="w-10 h-10 rounded-full">
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        @if($user->role === 'astrologer' && $user->astrologer)
                                            <div class="text-xs text-purple-600">
                                                {{ $user->astrologer->experience }} years exp • ₹{{ $user->astrologer->per_minute_rate }}/min
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $roleColors = [
                                        'user' => 'bg-blue-100 text-blue-800',
                                        'astrologer' => 'bg-purple-100 text-purple-800',
                                        'admin' => 'bg-red-100 text-red-800'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->role === 'astrologer' && $user->astrologer)
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'inactive' => 'bg-gray-100 text-gray-800'
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$user->astrologer->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($user->astrologer->status) }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($user->role === 'astrologer' && $user->astrologer)
                                    ₹{{ number_format($user->astrologer->wallet_balance, 2) }}
                                @else
                                    ₹{{ number_format($user->wallet_balance ?? 0, 2) }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button onclick="viewUserDetails({{ $user->id }})" 
                                        class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </button>
                                
                                @if($user->role !== 'admin')
                                    <button onclick="deleteUser({{ $user->id }})" 
                                            class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No users found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- User Details Modal -->
<div id="userDetailsModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex items-start justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">User Details</h3>
            <button onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div id="userDetailsContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
async function viewUserDetails(id) {
    try {
        document.getElementById('userDetailsModal').classList.remove('hidden');
        document.getElementById('userDetailsContent').innerHTML = '<div class="flex justify-center py-4"><i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i></div>';
        
        const response = await fetch(`/admin/users/${id}/details`);
        const data = await response.json();
        
        if (data.success) {
            const user = data.user;
            const transactionSummary = data.transaction_summary;
            
            let content = `
                <div class="space-y-6">
                    <!-- Basic Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="flex items-center space-x-3 mb-4">
                                <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=dc2626&color=fff&size=60" 
                                     alt="${user.name}" class="w-15 h-15 rounded-full">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">${user.name}</h4>
                                    <p class="text-gray-600">${user.email}</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getRoleColor(user.role)}">
                                        ${user.role.charAt(0).toUpperCase() + user.role.slice(1)}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <p><span class="font-medium">Registered:</span> ${new Date(user.created_at).toLocaleDateString()}</p>
                            <p><span class="font-medium">Phone:</span> ${user.phone || 'Not provided'}</p>
                            <p><span class="font-medium">Date of Birth:</span> ${user.date_of_birth ? new Date(user.date_of_birth).toLocaleDateString() : 'Not provided'}</p>
                        </div>
                    </div>
            `;
            
            // Astrologer specific details
            if (user.role === 'astrologer' && user.astrologer) {
                const astrologer = user.astrologer;
                content += `
                    <div class="border-t pt-6">
                        <h5 class="text-md font-semibold mb-3 text-purple-800">Astrologer Profile</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <p><span class="font-medium">Status:</span> 
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(astrologer.status)}">
                                        ${astrologer.status.charAt(0).toUpperCase() + astrologer.status.slice(1)}
                                    </span>
                                </p>
                                <p><span class="font-medium">Experience:</span> ${astrologer.experience} years</p>
                                <p><span class="font-medium">Rate:</span> ₹${astrologer.per_minute_rate}/minute</p>
                                <p><span class="font-medium">Wallet Balance:</span> ₹${parseFloat(astrologer.wallet_balance).toFixed(2)}</p>
                            </div>
                            <div class="space-y-2">
                                <p><span class="font-medium">Education:</span> ${astrologer.education || 'Not provided'}</p>
                                <p><span class="font-medium">Specialization:</span> ${astrologer.specialization || 'Not provided'}</p>
                                <p><span class="font-medium">Languages:</span> ${astrologer.languages || 'Not provided'}</p>
                                ${astrologer.approved_at ? `<p><span class="font-medium">Approved:</span> ${new Date(astrologer.approved_at).toLocaleDateString()}</p>` : ''}
                            </div>
                        </div>
                        ${astrologer.bio ? `
                            <div class="mt-4">
                                <p class="font-medium">Bio:</p>
                                <p class="text-gray-600 text-sm mt-1">${astrologer.bio}</p>
                            </div>
                        ` : ''}
                        ${astrologer.certifications ? `
                            <div class="mt-4">
                                <p class="font-medium">Certifications:</p>
                                <p class="text-gray-600 text-sm mt-1">${astrologer.certifications}</p>
                            </div>
                        ` : ''}
                        ${astrologer.admin_notes ? `
                            <div class="mt-4">
                                <p class="font-medium">Admin Notes:</p>
                                <p class="text-gray-600 text-sm mt-1">${astrologer.admin_notes}</p>
                            </div>
                        ` : ''}
                    </div>
                `;
            }
            
            // Wallet transaction summary
            if (transactionSummary && (transactionSummary.transaction_count > 0 || user.wallet_balance > 0)) {
                content += `
                    <div class="border-t pt-6">
                        <h5 class="text-md font-semibold mb-3 text-green-800">Wallet Summary</h5>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-green-50 p-3 rounded-lg">
                                <p class="text-sm text-green-600">Total Credits</p>
                                <p class="text-lg font-semibold text-green-800">₹${parseFloat(transactionSummary.total_credit || 0).toFixed(2)}</p>
                            </div>
                            <div class="bg-red-50 p-3 rounded-lg">
                                <p class="text-sm text-red-600">Total Debits</p>
                                <p class="text-lg font-semibold text-red-800">₹${parseFloat(transactionSummary.total_debit || 0).toFixed(2)}</p>
                            </div>
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <p class="text-sm text-blue-600">Total Transactions</p>
                                <p class="text-lg font-semibold text-blue-800">${transactionSummary.transaction_count || 0}</p>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            content += `</div>`;
            document.getElementById('userDetailsContent').innerHTML = content;
        } else {
            document.getElementById('userDetailsContent').innerHTML = `<div class="text-center py-4 text-red-600">${data.message}</div>`;
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('userDetailsContent').innerHTML = '<div class="text-center py-4 text-red-600">Failed to load user details</div>';
    }
}

function closeUserModal() {
    document.getElementById('userDetailsModal').classList.add('hidden');
}

function getRoleColor(role) {
    const colors = {
        'user': 'bg-blue-100 text-blue-800',
        'astrologer': 'bg-purple-100 text-purple-800',
        'admin': 'bg-red-100 text-red-800'
    };
    return colors[role] || 'bg-gray-100 text-gray-800';
}

function getStatusColor(status) {
    const colors = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'approved': 'bg-green-100 text-green-800',
        'rejected': 'bg-red-100 text-red-800',
        'inactive': 'bg-gray-100 text-gray-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
}

async function deleteUser(id) {
    if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) return;
    
    try {
        const response = await fetch(`/admin/users/${id}`, {
            method: 'DELETE',
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

// Close modal when clicking outside
document.getElementById('userDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeUserModal();
    }
});
</script>
@endsection
