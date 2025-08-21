@extends('layouts.admin')

@section('title', 'Manage Astrologers - Admin Panel')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manage Astrologers</h1>
            <p class="text-gray-600">Review and manage astrologer profiles and approvals</p>
        </div>
        <div class="flex space-x-2">
            <div class="bg-white px-3 py-2 rounded-lg border">
                <span class="text-sm text-gray-600">Total: {{ $astrologers->total() }}</span>
            </div>
        </div>
    </div>

    <!-- Astrologers Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Astrologer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Experience</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($astrologers as $astrologer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($astrologer->user->name) }}&background=7c3aed&color=fff&size=40" 
                                         alt="{{ $astrologer->user->name }}" class="w-10 h-10 rounded-full">
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $astrologer->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $astrologer->user->email }}</div>
                                        @if($astrologer->specialization)
                                            <div class="text-xs text-purple-600">{{ implode(', ', $astrologer->specialization) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $astrologer->experience }} years
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ₹{{ $astrologer->per_minute_rate }}/min
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        'inactive' => 'bg-gray-100 text-gray-800'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$astrologer->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($astrologer->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $astrologer->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button onclick="viewDetails({{ $astrologer->id }})" 
                                        class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </button>
                                
                                @if($astrologer->status === 'pending')
                                    <button onclick="approveAstrologer({{ $astrologer->id }})" 
                                            class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button onclick="rejectAstrologer({{ $astrologer->id }})" 
                                            class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @elseif($astrologer->status === 'approved')
                                    <button onclick="deactivateAstrologer({{ $astrologer->id }})" 
                                            class="text-orange-600 hover:text-orange-900">
                                        <i class="fas fa-pause"></i>
                                    </button>
                                @elseif($astrologer->status === 'inactive')
                                    <button onclick="reactivateAstrologer({{ $astrologer->id }})" 
                                            class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-play"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No astrologers found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($astrologers->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $astrologers->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Astrologer Details</h3>
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
async function viewDetails(id) {
    try {
        const response = await fetch(`/admin/astrologers/${id}/details`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            const astrologer = data.astrologer;
            const content = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <p class="text-sm text-gray-900">${astrologer.user.name}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="text-sm text-gray-900">${astrologer.user.email}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Experience</label>
                            <p class="text-sm text-gray-900">${astrologer.experience} years</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Rate</label>
                            <p class="text-sm text-gray-900">₹${astrologer.per_minute_rate}/min</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Education</label>
                            <p class="text-sm text-gray-900">${astrologer.education || 'Not specified'}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Languages</label>
                            <p class="text-sm text-gray-900">${astrologer.languages ? astrologer.languages.join(', ') : 'None'}</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Specialization</label>
                        <p class="text-sm text-gray-900">${astrologer.specialization ? astrologer.specialization.join(', ') : 'None'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Bio</label>
                        <p class="text-sm text-gray-900">${astrologer.bio || 'No bio provided'}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Certifications</label>
                        <p class="text-sm text-gray-900">${astrologer.certifications || 'No certifications listed'}</p>
                    </div>
                    ${astrologer.admin_notes ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Admin Notes</label>
                            <p class="text-sm text-gray-900">${astrologer.admin_notes}</p>
                        </div>
                    ` : ''}
                </div>
            `;
            
            document.getElementById('modalContent').innerHTML = content;
            document.getElementById('detailsModal').classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Failed to load details', 'error');
    }
}

function closeModal() {
    document.getElementById('detailsModal').classList.add('hidden');
}

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

async function deactivateAstrologer(id) {
    const notes = prompt('Reason for deactivation (optional):');
    if (notes === null) return;
    
    try {
        const response = await fetch(`/admin/astrologers/${id}/deactivate`, {
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

async function reactivateAstrologer(id) {
    if (!confirm('Are you sure you want to reactivate this astrologer?')) return;
    
    try {
        const response = await fetch(`/admin/astrologers/${id}/reactivate`, {
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

// Close modal when clicking outside
document.getElementById('detailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endsection
