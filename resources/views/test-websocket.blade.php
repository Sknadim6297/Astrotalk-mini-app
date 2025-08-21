<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebSocket Connection Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .status {
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            font-weight: bold;
        }
        .connected { background: #d4edda; color: #155724; }
        .disconnected { background: #f8d7da; color: #721c24; }
        .connecting { background: #fff3cd; color: #856404; }
        .log {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            height: 300px;
            overflow-y: auto;
            font-family: monospace;
            margin: 15px 0;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        button:hover { background: #0056b3; }
        button:disabled { background: #6c757d; cursor: not-allowed; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Laravel Reverb WebSocket Test</h1>
        
        <div id="status" class="status connecting">Connecting...</div>
        
        <div>
            <button onclick="testConnection()">Test Connection</button>
            <button onclick="clearLog()">Clear Log</button>
            <button onclick="location.reload()">Reload Page</button>
        </div>
        
        <div id="log" class="log">Initializing WebSocket connection...<br></div>
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
        
        function updateStatus(connected) {
            if (connected) {
                statusEl.textContent = '✅ Connected to Reverb WebSocket Server';
                statusEl.className = 'status connected';
            } else {
                statusEl.textContent = '❌ Disconnected from WebSocket Server';
                statusEl.className = 'status disconnected';
            }
        }
        
        function testConnection() {
            log('Testing WebSocket connection...');
            if (pusher && pusher.connection.state === 'connected') {
                log('✅ WebSocket is connected and ready!');
            } else {
                log('❌ WebSocket is not connected. State: ' + (pusher ? pusher.connection.state : 'no pusher instance'));
            }
        }
        
        function clearLog() {
            logEl.innerHTML = '';
        }

        // Initialize Pusher client for Reverb
        log('Initializing Pusher client for Reverb...');
        
        const pusher = new Pusher('{{ env("REVERB_APP_KEY") }}', {
            wsHost: '{{ env("REVERB_HOST") }}',
            wsPort: {{ env('REVERB_PORT', 8080) }},
            wssPort: {{ env('REVERB_PORT', 8080) }},
            forceTLS: false,
            encrypted: false,
            disableStats: true,
            enabledTransports: ['ws', 'wss'],
        });

        log('Pusher client created with config:');
        log(`- Key: {{ env("REVERB_APP_KEY") }}`);
        log(`- Host: {{ env("REVERB_HOST") }}`);
        log(`- Port: {{ env("REVERB_PORT", 8080) }}`);

        // Connection event listeners
        pusher.connection.bind('connecting', function() {
            log('🔄 Connecting to WebSocket server...');
            statusEl.textContent = 'Connecting...';
            statusEl.className = 'status connecting';
        });

        pusher.connection.bind('connected', function() {
            log('✅ Successfully connected to Reverb WebSocket server!');
            log('Connection ID: ' + pusher.connection.socket_id);
            updateStatus(true);
        });

        pusher.connection.bind('disconnected', function() {
            log('❌ Disconnected from WebSocket server');
            updateStatus(false);
        });

        pusher.connection.bind('failed', function() {
            log('❌ Failed to connect to WebSocket server');
            updateStatus(false);
        });

        pusher.connection.bind('error', function(err) {
            log('❌ WebSocket error: ' + JSON.stringify(err));
        });

        pusher.connection.bind('state_change', function(states) {
            log(`🔄 Connection state changed: ${states.previous} → ${states.current}`);
        });

        // Test subscribing to a public channel
        try {
            const testChannel = pusher.subscribe('test-channel');
            log('📡 Subscribed to test-channel');
            
            testChannel.bind('test-event', function(data) {
                log('📨 Received test event: ' + JSON.stringify(data));
            });
        } catch (error) {
            log('❌ Error subscribing to test channel: ' + error.message);
        }

        log('WebSocket test page loaded. Waiting for connection...');
    </script>
</body>
</html>
