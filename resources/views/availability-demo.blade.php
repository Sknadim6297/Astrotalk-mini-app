<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Astrologer Availability System Demo</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .demo-section {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: #f8f9fa;
        }
        
        .status-indicator {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }
        .status-online { background-color: #28a745; }
        .status-offline { background-color: #dc3545; }
        .status-busy { background-color: #ffc107; }
        
        .feature-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .api-endpoint {
            background: #f1f3f4;
            border-left: 4px solid #007bff;
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 0 4px 4px 0;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <h1 class="display-4 mb-4">🔮 Astrologer Availability System</h1>
            <p class="lead">Real-time availability tracking, smart scheduling, and seamless chat integration</p>
            <div class="mt-4">
                <span class="badge bg-success fs-6 me-2">✅ Real-time Status</span>
                <span class="badge bg-info fs-6 me-2">📅 Smart Scheduling</span>
                <span class="badge bg-warning fs-6">💬 Chat Integration</span>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-8">
                <!-- System Overview -->
                <div class="feature-card">
                    <h2><i class="fas fa-cogs text-primary"></i> System Features</h2>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>✨ Availability Management</h5>
                            <ul>
                                <li>Real-time online/offline status</li>
                                <li>Daily availability scheduling</li>
                                <li>Quick status toggles</li>
                                <li>Auto-availability based on schedule</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>🚀 Smart Features</h5>
                            <ul>
                                <li>Chat Now button (only when available)</li>
                                <li>Today's schedule display</li>
                                <li>Last seen timestamps</li>
                                <li>Status indicators in chat</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Demo Links -->
                <div class="feature-card">
                    <h3><i class="fas fa-rocket text-success"></i> Try the System</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>For Astrologers:</h5>
                            <a href="/for_testing/public/availability-management" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-clock"></i> Availability Management
                            </a>
                            <p class="small text-muted">Manage your online status and daily schedule</p>
                        </div>
                        <div class="col-md-6">
                            <h5>For Users:</h5>
                            <button class="btn btn-success w-100 mb-2" onclick="loadAstrologerProfile()">
                                <i class="fas fa-user"></i> View Astrologer Profile
                            </button>
                            <p class="small text-muted">See real-time availability and chat options</p>
                        </div>
                    </div>
                </div>

                <!-- API Testing -->
                <div class="feature-card">
                    <h3><i class="fas fa-code text-info"></i> API Testing</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn btn-outline-primary w-100 mb-2" onclick="testAvailability()">
                                <i class="fas fa-flask"></i> Test Availability System
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-outline-success w-100 mb-2" onclick="checkStatus()">
                                <i class="fas fa-heartbeat"></i> Check Real-time Status
                            </button>
                        </div>
                    </div>
                    
                    <div id="api-results" class="mt-3" style="display: none;">
                        <h6>API Response:</h6>
                        <pre id="api-output" class="bg-dark text-light p-3 rounded"></pre>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Live Status -->
                <div class="feature-card">
                    <h4><i class="fas fa-satellite-dish text-warning"></i> Live Status</h4>
                    <div id="live-status">
                        <p class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</p>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary w-100" onclick="refreshStatus()">
                        <i class="fas fa-sync"></i> Refresh
                    </button>
                </div>

                <!-- API Endpoints -->
                <div class="feature-card">
                    <h4><i class="fas fa-network-wired text-primary"></i> API Endpoints</h4>
                    
                    <h6>Public Endpoints:</h6>
                    <div class="api-endpoint">
                        GET /api/astrologers/{id}/availability-status
                    </div>
                    
                    <h6>Astrologer Endpoints:</h6>
                    <div class="api-endpoint">
                        POST /api/astrologer/availability/toggle-online
                    </div>
                    <div class="api-endpoint">
                        POST /api/astrologer/availability/toggle-available-now
                    </div>
                    <div class="api-endpoint">
                        POST /api/astrologer/availability/set-today
                    </div>
                </div>

                <!-- Database Schema -->
                <div class="feature-card">
                    <h4><i class="fas fa-database text-secondary"></i> Database Fields</h4>
                    <small>
                        <strong>astrologers table:</strong><br>
                        • is_online (boolean)<br>
                        • is_available_now (boolean)<br>
                        • last_seen_at (timestamp)<br>
                        • today_availability (json)<br>
                        • weekly_availability (json)
                    </small>
                </div>
            </div>
        </div>
        
        <!-- Technical Implementation -->
        <div class="row">
            <div class="col-12">
                <div class="feature-card">
                    <h3><i class="fas fa-wrench text-dark"></i> Technical Implementation</h3>
                    <div class="row">
                        <div class="col-md-4">
                            <h5>🗄️ Backend</h5>
                            <ul class="small">
                                <li>Laravel Migration for availability fields</li>
                                <li>Astrologer model with availability methods</li>
                                <li>API routes for status management</li>
                                <li>Real-time status checking</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h5>🎨 Frontend</h5>
                            <ul class="small">
                                <li>Availability management dashboard</li>
                                <li>Real-time status indicators</li>
                                <li>Enhanced astrologer profiles</li>
                                <li>Smart chat integration</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h5>⚡ Features</h5>
                            <ul class="small">
                                <li>Auto status updates</li>
                                <li>Schedule-based availability</li>
                                <li>Chat now conditional display</li>
                                <li>Today's availability override</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const apiBase = '/for_testing/public/api';
        
        // Initialize demo
        document.addEventListener('DOMContentLoaded', function() {
            refreshStatus();
            setInterval(refreshStatus, 30000); // Auto-refresh every 30 seconds
        });
        
        // Test availability system
        async function testAvailability() {
            try {
                const response = await fetch('/for_testing/public/test-availability');
                const data = await response.json();
                
                document.getElementById('api-results').style.display = 'block';
                document.getElementById('api-output').textContent = JSON.stringify(data, null, 2);
                
                // Refresh live status
                await refreshStatus();
                
            } catch (error) {
                console.error('Error testing availability:', error);
                alert('Error testing availability system');
            }
        }
        
        // Check real-time status
        async function checkStatus() {
            try {
                // Get first astrologer's status
                const response = await fetch(`${apiBase}/astrologers/1/availability-status`);
                const data = await response.json();
                
                document.getElementById('api-results').style.display = 'block';
                document.getElementById('api-output').textContent = JSON.stringify(data, null, 2);
                
            } catch (error) {
                console.error('Error checking status:', error);
                alert('Error checking status');
            }
        }
        
        // Refresh live status
        async function refreshStatus() {
            try {
                const response = await fetch(`${apiBase}/astrologers/1/availability-status`);
                const data = await response.json();
                
                if (response.ok) {
                    updateLiveStatus(data.data);
                } else {
                    document.getElementById('live-status').innerHTML = '<p class="text-danger">Error loading status</p>';
                }
            } catch (error) {
                document.getElementById('live-status').innerHTML = '<p class="text-warning">Connection error</p>';
            }
        }
        
        // Update live status display
        function updateLiveStatus(data) {
            const statusHtml = `
                <div class="mb-3">
                    <h6>${data.name}</h6>
                    <div class="d-flex align-items-center mb-2">
                        <span class="status-indicator ${getStatusClass(data)}" style="width: 12px; height: 12px;"></span>
                        <span class="small">${getStatusText(data)}</span>
                    </div>
                    ${data.last_seen_at ? `<p class="small text-muted mb-0">Last seen: ${data.last_seen_at}</p>` : ''}
                </div>
                <div class="border-top pt-2">
                    <small><strong>Status:</strong> ${data.availability_status.status}</small><br>
                    <small><strong>Message:</strong> ${data.availability_status.message}</small><br>
                    <small><strong>Can Chat:</strong> ${data.availability_status.can_chat ? 'Yes' : 'No'}</small>
                </div>
            `;
            
            document.getElementById('live-status').innerHTML = statusHtml;
        }
        
        // Get status class for indicator
        function getStatusClass(data) {
            if (data.is_online && data.is_available_now) {
                return 'status-online';
            } else if (data.is_online) {
                return 'status-busy';
            } else {
                return 'status-offline';
            }
        }
        
        // Get status text
        function getStatusText(data) {
            if (data.is_online && data.is_available_now) {
                return 'Available Now';
            } else if (data.is_online) {
                return 'Online but Busy';
            } else {
                return 'Offline';
            }
        }
        
        // Load astrologer profile
        function loadAstrologerProfile() {
            window.open('/for_testing/public/astrologer/1/profile', '_blank');
        }
    </script>
</body>
</html>
