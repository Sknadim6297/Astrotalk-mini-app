<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel WebSocket Chat Demo</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: 300;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 1.1em;
        }
        .content {
            padding: 30px;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .feature {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }
        .feature h3 {
            color: #333;
            margin-top: 0;
            display: flex;
            align-items: center;
        }
        .feature h3 i {
            margin-right: 10px;
            color: #667eea;
        }
        .status-panel {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .status {
            padding: 15px;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }
        .connected { background: #d4edda; color: #155724; }
        .disconnected { background: #f8d7da; color: #721c24; }
        .connecting { background: #fff3cd; color: #856404; }
        .demo-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
            margin: 30px 0;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        .implementation-details {
            background: #e9ecef;
            padding: 25px;
            border-radius: 10px;
            margin: 30px 0;
        }
        .tech-stack {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        .tech-tag {
            background: #667eea;
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.9em;
        }
        .log {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            max-height: 200px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            margin-top: 15px;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-comments"></i> Laravel WebSocket Chat</h1>
            <p>Real-time messaging with Laravel Reverb & Database Storage</p>
        </div>
        
        <div class="content">
            <div class="status-panel">
                <div id="status" class="status connecting">
                    <i class="fas fa-spinner fa-spin"></i> Connecting to WebSocket Server...
                </div>
                <div id="log" class="log">Initializing WebSocket connection...<br></div>
            </div>

            <div class="feature-grid">
                <div class="feature">
                    <h3><i class="fas fa-bolt"></i> Real-time Messaging</h3>
                    <p>Instant message delivery using Laravel Reverb WebSocket server with automatic fallback to polling for maximum reliability.</p>
                </div>
                
                <div class="feature">
                    <h3><i class="fas fa-database"></i> Database Storage</h3>
                    <p>All chat messages are automatically saved to the database with complete conversation history and user tracking.</p>
                </div>
                
                <div class="feature">
                    <h3><i class="fas fa-shield-alt"></i> Secure Channels</h3>
                    <p>Private channel authentication ensures only authorized users can access their conversations with proper security.</p>
                </div>
                
                <div class="feature">
                    <h3><i class="fas fa-user-shield"></i> Admin Monitoring</h3>
                    <p>Complete admin interface for viewing all conversations with detailed trace functionality and booking information.</p>
                </div>
            </div>

            <div class="demo-buttons">
                <button class="btn btn-primary" onclick="testConnection()">
                    <i class="fas fa-wifi"></i> Test Connection
                </button>
                <button class="btn btn-primary" onclick="sendTestMessage()">
                    <i class="fas fa-paper-plane"></i> Send Test Message
                </button>
                <a href="/test-broadcast" class="btn btn-secondary" target="_blank">
                    <i class="fas fa-broadcast-tower"></i> Test Broadcast
                </a>
                <button class="btn btn-secondary" onclick="clearLog()">
                    <i class="fas fa-trash"></i> Clear Log
                </button>
            </div>

            <div class="implementation-details">
                <h3><i class="fas fa-code"></i> Implementation Details</h3>
                <p><strong>Server Configuration:</strong></p>
                <ul>
                    <li>Laravel Reverb WebSocket Server running on port 8080</li>
                    <li>Queue worker processing broadcast jobs</li>
                    <li>Private channel authentication with booking authorization</li>
                    <li>Automatic database storage for all messages</li>
                </ul>
                
                <p><strong>Technology Stack:</strong></p>
                <div class="tech-stack">
                    <span class="tech-tag">Laravel Reverb</span>
                    <span class="tech-tag">WebSockets</span>
                    <span class="tech-tag">Broadcasting</span>
                    <span class="tech-tag">Queue Jobs</span>
                    <span class="tech-tag">Private Channels</span>
                    <span class="tech-tag">Real-time Events</span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script>
        const statusEl = document.getElementById('status');
        const logEl = document.getElementById('log');
        
        function log(message) {
            const timestamp = new Date().toLocaleTimeString();
            logEl.innerHTML += `[${timestamp}] ${message}<br>`;
            logEl.scrollTop = logEl.scrollHeight;
        }
        
        function updateStatus(connected, message) {
            if (connected) {
                statusEl.innerHTML = '<i class="fas fa-check-circle"></i> ' + (message || 'Connected to WebSocket Server');
                statusEl.className = 'status connected';
            } else {
                statusEl.innerHTML = '<i class="fas fa-times-circle"></i> ' + (message || 'Disconnected from WebSocket Server');
                statusEl.className = 'status disconnected';
            }
        }
        
        function testConnection() {
            log('🔧 Testing WebSocket connection...');
            if (pusher && pusher.connection.state === 'connected') {
                log('✅ WebSocket is connected and ready!');
                log('📡 Socket ID: ' + pusher.connection.socket_id);
                updateStatus(true, 'Connection Test Successful');
            } else {
                log('❌ WebSocket is not connected. State: ' + (pusher ? pusher.connection.state : 'no pusher instance'));
                updateStatus(false, 'Connection Test Failed');
            }
        }
        
        function sendTestMessage() {
            if (testChannel) {
                log('📤 Triggering test event...');
                // This would normally be done server-side, but for demo purposes
                fetch('/test-broadcast')
                    .then(response => response.json())
                    .then(data => {
                        log('✅ Test broadcast triggered: ' + data.message);
                    })
                    .catch(error => {
                        log('❌ Failed to trigger test broadcast: ' + error.message);
                    });
            } else {
                log('❌ Test channel not available');
            }
        }
        
        function clearLog() {
            logEl.innerHTML = '';
        }

        // Initialize Pusher client for Reverb
        log('🚀 Initializing Laravel Reverb WebSocket client...');
        
        const pusher = new Pusher('{{ env("REVERB_APP_KEY") }}', {
            wsHost: '{{ env("REVERB_HOST") }}',
            wsPort: {{ env('REVERB_PORT', 8080) }},
            wssPort: {{ env('REVERB_PORT', 8080) }},
            forceTLS: false,
            encrypted: false,
            disableStats: true,
            enabledTransports: ['ws', 'wss'],
        });

        log('⚙️ WebSocket Configuration:');
        log('  - App Key: {{ env("REVERB_APP_KEY") }}');
        log('  - Host: {{ env("REVERB_HOST") }}');
        log('  - Port: {{ env("REVERB_PORT", 8080) }}');

        // Connection event listeners
        pusher.connection.bind('connecting', function() {
            log('🔄 Connecting to WebSocket server...');
            statusEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Connecting...';
            statusEl.className = 'status connecting';
        });

        pusher.connection.bind('connected', function() {
            log('✅ Successfully connected to Laravel Reverb!');
            log('🆔 Connection ID: ' + pusher.connection.socket_id);
            updateStatus(true, 'Connected to Laravel Reverb');
        });

        pusher.connection.bind('disconnected', function() {
            log('❌ Disconnected from WebSocket server');
            updateStatus(false, 'Connection Lost');
        });

        pusher.connection.bind('failed', function() {
            log('❌ Failed to connect to WebSocket server');
            updateStatus(false, 'Connection Failed');
        });

        pusher.connection.bind('error', function(err) {
            log('❌ WebSocket error: ' + JSON.stringify(err));
        });

        pusher.connection.bind('state_change', function(states) {
            log(`🔄 State transition: ${states.previous} → ${states.current}`);
        });

        // Subscribe to test channel
        let testChannel = null;
        try {
            testChannel = pusher.subscribe('test-channel');
            log('📡 Subscribed to test-channel for demonstrations');
            
            testChannel.bind('test-event', function(data) {
                log('📨 Received test event: ' + JSON.stringify(data));
                updateStatus(true, 'Test Message Received Successfully!');
            });
        } catch (error) {
            log('❌ Error subscribing to test channel: ' + error.message);
        }

        log('🎉 Laravel WebSocket Chat system initialized and ready!');
    </script>
</body>
</html>
