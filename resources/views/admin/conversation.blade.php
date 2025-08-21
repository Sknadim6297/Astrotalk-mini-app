@extends('layouts.admin')

@section('title', 'Conversation Details - Admin Panel')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-comments mr-2 text-blue-600"></i>
                    Conversation Details
                </h1>
                <nav class="text-sm text-gray-600">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.bookings') }}" class="hover:text-blue-600">Bookings</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-900">Conversation #{{ $booking->id }}</span>
                </nav>
            </div>
            <a href="{{ route('admin.bookings') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-arrow-left mr-1"></i>Back to Bookings
            </a>
        </div>
    </div>

    <!-- Booking Information -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Booking Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-500">Booking ID</label>
                <p class="text-gray-900">#{{ $booking->id }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">Status</label>
                <p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $booking->status === 'completed' ? 'bg-green-100 text-green-800' : 
                           ($booking->status === 'active' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">User</label>
                <p class="text-gray-900">{{ $booking->user->name ?? 'Unknown' }}</p>
                <p class="text-sm text-gray-500">{{ $booking->user->email ?? '' }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">Astrologer</label>
                <p class="text-gray-900">{{ $booking->astrologer->user->name ?? 'Unknown' }}</p>
                <p class="text-sm text-gray-500">{{ $booking->astrologer->user->email ?? '' }}</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div>
                <label class="text-sm font-medium text-gray-500">Rate</label>
                <p class="text-gray-900">₹{{ $booking->per_minute_rate }}/minute</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">Started At</label>
                <p class="text-gray-900">{{ $booking->start_time ? $booking->start_time->format('M j, Y g:i A') : 'N/A' }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">Ended At</label>
                <p class="text-gray-900">{{ $booking->end_time ? $booking->end_time->format('M j, Y g:i A') : 'Ongoing' }}</p>
            </div>
        </div>
    </div>

    <!-- Chat Messages -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold">Chat Messages</h2>
            <p class="text-sm text-gray-600 mt-1">Complete conversation between user and astrologer</p>
        </div>
        
        <div class="h-96 overflow-y-auto p-6">
            @if($messages->count() > 0)
                <div class="space-y-4">
                    @foreach($messages as $message)
                        @php
                            $isFromUser = $message->sender_id === $booking->user_id;
                            $senderName = $isFromUser ? ($booking->user->name ?? 'User') : ($booking->astrologer->user->name ?? 'Astrologer');
                        @endphp
                        
                        <div class="flex {{ $isFromUser ? 'justify-start' : 'justify-end' }}">
                            <div class="max-w-xs lg:max-w-md">
                                <div class="flex items-center mb-1">
                                    <span class="text-xs text-gray-500">{{ $senderName }}</span>
                                    <span class="text-xs text-gray-400 ml-2">{{ $message->sent_at->format('M j, g:i A') }}</span>
                                </div>
                                <div class="px-4 py-2 rounded-lg {{ $isFromUser ? 'bg-gray-200 text-gray-900' : 'bg-blue-600 text-white' }}">
                                    <p class="text-sm">{{ $message->message }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex justify-center items-center h-full">
                    <div class="text-center">
                        <i class="fas fa-comments text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">No messages in this conversation</p>
                    </div>
                </div>
            @endif
        </div>
        
        @if($messages->count() > 0)
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-between items-center text-sm text-gray-600">
                    <span>Total Messages: {{ $messages->count() }}</span>
                    <span>Last Message: {{ $messages->last()->sent_at->diffForHumans() }}</span>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
