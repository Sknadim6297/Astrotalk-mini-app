@extends('layouts.auth-guard')

@section('title', 'Chat - {{ optional($booking->astrologer)->name ?? optional($booking->user)->name ?? "Unknown" }}')

@section('protected-content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50">
    <div class="max-w-4xl mx-auto px-4 py-6">
        <!-- Chat Header -->
        <div class="bg-white rounded-t-xl shadow-lg border-b">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <a href="{{ auth()->user()->role === 'user' ? '/appointments' : '/astrologer/dashboard' }}" 
                           class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-arrow-left text-xl"></i>
                        </a>
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-indigo-600"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-gray-900">
                                @if($booking->user_id === auth()->id())
                                    {{ optional($booking->astrologer)->name ?? 'Unknown Astrologer' }}
                                @else
                                    {{ optional($booking->user)->name ?? 'Unknown User' }}
                                @endif
                            </h2>
                            <p class="text-sm text-gray-600">
                                @if($booking->user_id === auth()->id())
                                    @php
                                        $spec = optional($booking->astrologer)->specialization ?? null;
                                    @endphp
                                    @if(is_array($spec))
                                        {{ implode(', ', $spec) }}
                                    @elseif($spec)
                                        {{ $spec }}
                                    @else
                                        <span class="text-gray-500">No specialization listed</span>
                                    @endif
                                @else
                                    Client
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 rounded-full" id="online-indicator"></div>
                                <span class="text-sm font-medium" id="online-status">Checking status...</span>
                            </div>
                            <p class="text-xs text-gray-500">₹{{ $booking->per_minute_rate }}/min</p>
                        </div>
                        @if(auth()->user()->role === 'astrologer' || auth()->user()->role === 'user')
                        <button id="endSessionBtn" 
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            <i class="fas fa-stop mr-1"></i>End Session
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Messages Container -->
        <div class="bg-white shadow-lg" style="height: 60vh;">
            <div id="chatMessages" class="h-full overflow-y-auto p-4 space-y-4">
                <!-- Messages will be loaded here -->
                <div id="loadingMessages" class="flex justify-center items-center h-full">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin text-3xl text-indigo-600 mb-4"></i>
                        <p class="text-gray-600">Loading chat messages...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message Input -->
        <div class="bg-white rounded-b-xl shadow-lg border-t p-4">
            <form id="messageForm" class="flex space-x-3">
                <input type="text" 
                       id="messageInput" 
                       placeholder="Type your message..." 
                       class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       maxlength="1000">
                <button type="submit" 
                        id="sendButton"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-paper-plane mr-1"></i>Send
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Include Pusher JS -->
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>

<script>
class ChatInterface {
    constructor() {
        this.bookingId = {{ $booking->id }};
        this.currentUserId = {{ auth()->id() }};
        this.isAstrologer = {{ auth()->user()->role === 'astrologer' ? 'true' : 'false' }};
        this.messages = [];
        this.lastMessageId = 0;
        this.pollInterval = null;
        this.pusher = null;
        this.channel = null;
        
        this.init();
    }
    
    init() {
        this.initReverb();
        this.bindEvents();
        this.loadMessages();
    }
    
    initReverb() {
        // Initialize Pusher client for Reverb
        this.pusher = new Pusher('{{ env("REVERB_APP_KEY") }}', {
            wsHost: '{{ env("REVERB_HOST") }}',
            wsPort: {{ env('REVERB_PORT', 8080) }},
            wssPort: {{ env('REVERB_PORT', 8080) }},
            forceTLS: false,
            encrypted: false,
            disableStats: true,
            enabledTransports: ['ws', 'wss'],
            authEndpoint: '/broadcasting/auth',
            authTransport: 'ajax', // ensure auth uses XHR so cookies and headers can be sent
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            }
        });

        // Subscribe to the public chat channel (temporarily for testing)
        this.channel = this.pusher.subscribe(`chat.${this.bookingId}`);

        // Listen for new messages - Laravel broadcasts with broadcastAs() => 'message.sent'
        // Payload is a flat object (id, booking_id, sender_id, receiver_id, message, sent_at, sender_name)
        this.channel.bind('message.sent', (data) => {
            console.log('Real-time broadcast received:', data);

            // Normalize payload into the local message shape expected by addMessage()
            const incoming = {
                id: data.id,
                booking_id: data.booking_id,
                sender_id: data.sender_id,
                receiver_id: data.receiver_id,
                message: data.message,
                sent_at: data.sent_at || new Date().toISOString(),
                sender_name: data.sender_name || 'Unknown'
            };

            // Only add if it's not from current user (we already pushed local messages)
            if (incoming.sender_id !== this.currentUserId) {
                this.addMessage(incoming);
                this.scrollToBottom();
            }
        });

        // Handle subscription success
        this.channel.bind('pusher:subscription_succeeded', () => {
            console.log('✅ Successfully subscribed to chat channel', this.channel);
        });

        // Handle subscription error
        this.channel.bind('pusher:subscription_error', (error) => {
            console.error('❌ Subscription error:', error);
            // Fallback to polling if WebSocket fails
            this.startPolling();
        });

        // Handle connection state changes
        this.pusher.connection.bind('connected', () => {
            console.log('🔌 Reverb connected');
        });

        this.pusher.connection.bind('disconnected', () => {
            console.log('🔌 Reverb disconnected');
        });

        this.pusher.connection.bind('failed', () => {
            console.log('❌ Reverb connection failed');
        });

        this.pusher.connection.bind('error', (error) => {
            console.log('❌ Reverb connection error:', error);
        });
    }
    
    bindEvents() {
        const messageForm = document.getElementById('messageForm');
        const messageInput = document.getElementById('messageInput');
        const endSessionBtn = document.getElementById('endSessionBtn');
        
        messageForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.sendMessage();
        });
        
        messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });
        
        if (endSessionBtn) {
            endSessionBtn.addEventListener('click', () => {
                this.endSession();
            });
        }
    }
    async loadMessages() {
        try {
            const response = await fetch(`/chat/${this.bookingId}/messages`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                credentials: 'same-origin'
            });
            
            const data = await response.json();
            if (data.success) {
                this.messages = data.data.messages;
                this.renderMessages();
                this.scrollToBottom();
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            console.error('Error loading messages:', error);
            this.showError('Failed to load messages');
        }
    }
    
    async sendMessage() {
        const messageInput = document.getElementById('messageInput');
        const message = messageInput.value.trim();
        
        if (!message) return;
        
        const sendButton = document.getElementById('sendButton');
        sendButton.disabled = true;
        sendButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Sending...';
        
        try {
            const response = await fetch(`/chat/${this.bookingId}/send`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                credentials: 'same-origin',
                body: JSON.stringify({ message })
            });
            
            const data = await response.json();
            if (data.success) {
                messageInput.value = '';
                // Add message to UI immediately (will be confirmed via WebSocket)
                this.addMessage(data.data.message);
                this.scrollToBottom();
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            console.error('Error sending message:', error);
            this.showError('Failed to send message');
        } finally {
            sendButton.disabled = false;
            sendButton.innerHTML = '<i class="fas fa-paper-plane mr-1"></i>Send';
        }
    }
    
    addMessage(message) {
        // Check if message already exists to avoid duplicates
        const exists = this.messages.find(m => m.id === message.id);
        if (!exists) {
            this.messages.push(message);
            this.renderMessages();
        }
    }
    
    renderMessages() {
        const chatMessages = document.getElementById('chatMessages');
        const loadingMessages = document.getElementById('loadingMessages');
        
        if (loadingMessages) {
            loadingMessages.remove();
        }
        
        chatMessages.innerHTML = '';
        
        if (this.messages.length === 0) {
            chatMessages.innerHTML = `
                <div class="flex justify-center items-center h-full">
                    <div class="text-center">
                        <i class="fas fa-comments text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">No messages yet. Start the conversation!</p>
                    </div>
                </div>
            `;
            return;
        }
        
        this.messages.forEach(message => {
            const messageElement = this.createMessageElement(message);
            chatMessages.appendChild(messageElement);
        });
    }
    
    createMessageElement(message) {
        const isOwnMessage = message.sender_id === this.currentUserId;
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${isOwnMessage ? 'justify-end' : 'justify-start'}`;
        
        const time = new Date(message.sent_at).toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit'
        });
        
        messageDiv.innerHTML = `
            <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${isOwnMessage ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-900'}">
                <p class="text-sm">${this.escapeHtml(message.message)}</p>
                <p class="text-xs mt-1 ${isOwnMessage ? 'text-indigo-200' : 'text-gray-500'}">${time}</p>
            </div>
        `;
        
        return messageDiv;
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    scrollToBottom() {
        const chatMessages = document.getElementById('chatMessages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    startPolling() {
        // Fallback polling for when WebSocket fails
        if (!this.pollInterval) {
            this.pollInterval = setInterval(() => {
                this.loadMessages();
            }, 3000);
        }
    }
    
    stopPolling() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
            this.pollInterval = null;
        }
    }
    
    async endSession() {
        if (!confirm('End this chat session? This will calculate the final amount and complete the booking.')) {
            return;
        }
        
        try {
            const response = await fetch(`/bookings/${this.bookingId}/end`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                credentials: 'same-origin'
            });
            
            const data = await response.json();
            if (data.success) {
                this.cleanup();
                const summary = data.data.session_summary;
                alert(`Session completed!\nDuration: ${summary.duration_minutes} minutes\nTotal Amount: ₹${summary.total_amount}`);
                window.location.href = '/astrologer/dashboard';
            } else {
                this.showError(data.message);
            }
        } catch (error) {
            console.error('Error ending session:', error);
            this.showError('Failed to end session');
        }
    }
    
    cleanup() {
        this.stopPolling();
        if (this.pusher) {
            if (this.channel) {
                this.pusher.unsubscribe(`private-chat.${this.bookingId}`);
            }
            this.pusher.disconnect();
        }
    }
    
    showError(message) {
        // Simple error display - you can enhance this
        alert('Error: ' + message);
    }
}

// Initialize chat when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.chatInterface = new ChatInterface();
    
    // Initialize online status checking
    checkAstrologerOnlineStatus();
    setInterval(checkAstrologerOnlineStatus, 15000); // Check every 15 seconds
});

// Clean up when page is unloaded
window.addEventListener('beforeunload', () => {
    if (window.chatInterface) {
        window.chatInterface.cleanup();
    }
});

// Check astrologer online status
async function checkAstrologerOnlineStatus() {
    try {
        @if($booking->user_id === auth()->id())
            // User checking astrologer status
            const astrologerId = {{ $booking->astrologer_id }};
            const response = await fetch(`/for_testing/public/api/astrologers/${astrologerId}/availability-status`);
        @else
            // Astrologer - update own last seen
            const response = await fetch('/for_testing/public/api/astrologer/availability/status', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`,
                    'Accept': 'application/json'
                }
            });
        @endif
        
        if (response.ok) {
            const data = await response.json();
            updateOnlineStatusUI(data.data);
        }
    } catch (error) {
        console.error('Error checking online status:', error);
    }
}

// Update online status UI
function updateOnlineStatusUI(data) {
    const indicator = document.getElementById('online-indicator');
    const statusText = document.getElementById('online-status');
    
    if (!indicator || !statusText) return;
    
    if (data.is_online && data.is_available_now) {
        indicator.className = 'w-2 h-2 bg-green-500 rounded-full';
        statusText.className = 'text-sm text-green-600 font-medium';
        statusText.textContent = 'Online & Available';
    } else if (data.is_online) {
        indicator.className = 'w-2 h-2 bg-yellow-500 rounded-full';
        statusText.className = 'text-sm text-yellow-600 font-medium';
        statusText.textContent = 'Online but Busy';
    } else {
        indicator.className = 'w-2 h-2 bg-gray-400 rounded-full';
        statusText.className = 'text-sm text-gray-500 font-medium';
        statusText.textContent = 'Offline';
        
        if (data.last_seen_at) {
            statusText.textContent = `Last seen ${data.last_seen_at}`;
        }
    }
}
</script>
@endsection
