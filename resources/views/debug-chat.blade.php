<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-time Chat Debug</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; }
        .debug-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .debug-section h3 { margin-top: 0; color: #333; }
        .log { background: #f8f9fa; border: 1px solid #dee2e6; padding: 10px; height: 200px; overflow-y: auto; font-family: monospace; font-size: 12px; }
        .status { padding: 10px; border-radius: 5px; margin: 10px 0; }
        .connected { background: #d4edda; color: #155724; }
        .disconnected { background: #f8d7da; color: #721c24; }
        .connecting { background: #fff3cd; color: #856404; }
        button { background: #007bff; color: white; border: none; padding: 10px 15px; margin: 5px; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
        input[type="text"] { width: 200px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .flex { display: flex; gap: 10px; align-items: center; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Real-time Chat Debug Tool</h1>
        
        <div class="debug-section">
            <h3>📡 WebSocket Connection Status</h3>
            <div id="connectionStatus" class="status connecting">Initializing...</div>
            <div class="flex">
                <button onclick="testConnection()">Test Connection</button>
                <button onclick="reconnect()">Reconnect</button>
                <button onclick="clearLog()">Clear Log</button>
            </div>
        </div>

        <div class="debug-section">
            <h3>💬 Chat Channel Test</h3>
            <div class="flex">
                <label>Booking ID:</label>
                <input type="text" id="bookingId" value="1" placeholder="Enter booking ID">
                <button onclick="subscribeToChannel()">Subscribe to Channel</button>
                <button onclick="unsubscribeFromChannel()">Unsubscribe</button>
            </div>
            <div class="flex">
                <input type="text" id="testMessage" placeholder="Type a test message" style="width: 300px;">
                <button onclick="sendTestMessage()">Send Test Message</button>
                <button onclick="triggerServerBroadcast()">Trigger Server Broadcast</button>
            </div>
        </div>

        <div class="debug-section">
            <h3>📊 Debug Log</h3>
            <div id="debugLog" class="log">Initializing debug tool...<br></div>
        </div>

        <div class="debug-section">
            <h3>🔐 Authentication Status</h3>
            <div id="authStatus">Checking...</div>
            <button onclick="checkAuth()">Refresh Auth Status</button>
        </div>
    </div>

    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script>
        let pusher = null;
        let channel = null;
        let currentBookingId = 1;

        function log(message) {
            const timestamp = new Date().toLocaleTimeString();
            const debugLog = document.getElementById('debugLog');
            debugLog.innerHTML += `[${timestamp}] ${message}<br>`;
            debugLog.scrollTop = debugLog.scrollHeight;
            console.log(`[${timestamp}] ${message}`);
        }

        function updateStatus(status, message) {
            const statusEl = document.getElementById('connectionStatus');
            statusEl.className = `status ${status}`;
            statusEl.textContent = message;
        }

        function clearLog() {
            document.getElementById('debugLog').innerHTML = '';
        }

        function initPusher() {
            log('🚀 Initializing Pusher client for Reverb...');
            
            pusher = new Pusher('{{ env("REVERB_APP_KEY") }}', {
                wsHost: '{{ env("REVERB_HOST") }}',
                wsPort: {{ env('REVERB_PORT', 8080) }},
                wssPort: {{ env('REVERB_PORT', 8080) }},
                forceTLS: false,
                encrypted: false,
                disableStats: true,
                enabledTransports: ['ws', 'wss'],
                authEndpoint: '/broadcasting/auth',
                authTransport: 'ajax',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                }
            });

            pusher.connection.bind('connecting', () => {
                log('🔄 Connecting to WebSocket server...');
                updateStatus('connecting', 'Connecting...');
            });

            pusher.connection.bind('connected', () => {
                log('✅ Connected to Reverb WebSocket server!');
                log(`🆔 Socket ID: ${pusher.connection.socket_id}`);
                updateStatus('connected', 'Connected to Reverb');
            });

            pusher.connection.bind('disconnected', () => {
                log('❌ Disconnected from WebSocket server');
                updateStatus('disconnected', 'Disconnected');
            });

            pusher.connection.bind('failed', () => {
                log('❌ Failed to connect to WebSocket server');
                updateStatus('disconnected', 'Connection Failed');
            });

            pusher.connection.bind('error', (error) => {
                log('❌ WebSocket error: ' + JSON.stringify(error));
            });

            pusher.connection.bind('state_change', (states) => {
                log(`🔄 State change: ${states.previous} → ${states.current}`);
            });
        }

        function subscribeToChannel() {
            const bookingId = document.getElementById('bookingId').value;
            currentBookingId = bookingId;
            
            if (channel) {
                pusher.unsubscribe(channel.name);
                log('📤 Unsubscribed from previous channel');
            }

            // Using public channel for testing
            const channelName = `chat.${bookingId}`;
            log(`📡 Subscribing to public channel: ${channelName}`);
            
            channel = pusher.subscribe(channelName);

            channel.bind('pusher:subscription_succeeded', () => {
                log(`✅ Successfully subscribed to ${channelName}`);
            });

            channel.bind('pusher:subscription_error', (error) => {
                log(`❌ Subscription error for ${channelName}: ${JSON.stringify(error)}`);
            });

            // Listen for message.sent events
            channel.bind('message.sent', (data) => {
                log(`📨 Received real-time message: ${JSON.stringify(data)}`);
                log(`   Message: "${data.message}" from sender ${data.sender_id}`);
            });

            // Listen for other events
            channel.bind_all((eventName, data) => {
                if (!eventName.startsWith('pusher:')) {
                    log(`📢 Event received: ${eventName} - ${JSON.stringify(data)}`);
                }
            });
        }

        function unsubscribeFromChannel() {
            if (channel) {
                pusher.unsubscribe(channel.name);
                log('📤 Unsubscribed from channel');
                channel = null;
            }
        }

        function testConnection() {
            if (pusher && pusher.connection.state === 'connected') {
                log('✅ WebSocket connection is active');
                log(`🆔 Socket ID: ${pusher.connection.socket_id}`);
                updateStatus('connected', 'Connection Test Passed');
            } else {
                log(`❌ WebSocket not connected. Current state: ${pusher ? pusher.connection.state : 'no pusher'}`);
                updateStatus('disconnected', 'Connection Test Failed');
            }
        }

        function reconnect() {
            log('🔄 Attempting to reconnect...');
            if (pusher) {
                pusher.disconnect();
            }
            setTimeout(() => {
                initPusher();
                setTimeout(() => {
                    if (currentBookingId) {
                        document.getElementById('bookingId').value = currentBookingId;
                        subscribeToChannel();
                    }
                }, 1000);
            }, 500);
        }

        function sendTestMessage() {
            const message = document.getElementById('testMessage').value;
            const bookingId = document.getElementById('bookingId').value;
            
            if (!message) {
                log('❌ Please enter a test message');
                return;
            }

            log(`📤 Sending test message: "${message}" to booking ${bookingId}`);
            
            fetch(`/chat/${bookingId}/send`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin',
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    log(`✅ Message sent successfully`);
                    document.getElementById('testMessage').value = '';
                } else {
                    log(`❌ Failed to send message: ${data.message}`);
                }
            })
            .catch(error => {
                log(`❌ Error sending message: ${error.message}`);
            });
        }

        function triggerServerBroadcast() {
            const bookingId = document.getElementById('bookingId').value;
            log(`🎯 Triggering server broadcast for booking ${bookingId}`);
            
            fetch(`/test-message-broadcast/${bookingId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        log(`✅ Server broadcast triggered: ${data.message}`);
                    } else {
                        log(`❌ Server broadcast failed: ${data.error || 'Unknown error'}`);
                    }
                })
                .catch(error => {
                    log(`❌ Error triggering broadcast: ${error.message}`);
                });
        }

        function checkAuth() {
            log('🔐 Checking authentication status...');
            fetch('/test-auth')
                .then(response => response.json())
                .then(data => {
                    const authStatus = document.getElementById('authStatus');
                    authStatus.innerHTML = `
                        <strong>Authenticated:</strong> ${data.authenticated ? '✅ Yes' : '❌ No'}<br>
                        <strong>User ID:</strong> ${data.user_id || 'N/A'}<br>
                        <strong>Role:</strong> ${data.user_role || 'N/A'}<br>
                        <strong>CSRF Token:</strong> ${data.csrf_token ? '✅ Present' : '❌ Missing'}<br>
                        <strong>Session ID:</strong> ${data.session_id || 'N/A'}
                    `;
                    
                    if (data.authenticated) {
                        log('✅ User is authenticated');
                    } else {
                        log('❌ User is not authenticated - this may cause private channel subscription to fail');
                    }
                })
                .catch(error => {
                    log(`❌ Error checking auth: ${error.message}`);
                });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            log('🎉 Debug tool loaded');
            checkAuth();
            initPusher();
            
            // Auto-subscribe to default channel after connection
            setTimeout(() => {
                subscribeToChannel();
            }, 2000);
        });
    </script>
</body>
</html>
