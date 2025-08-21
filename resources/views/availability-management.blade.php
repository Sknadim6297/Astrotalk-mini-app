<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Availability Management - Astrologer</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        .status-online { background-color: #28a745; }
        .status-offline { background-color: #dc3545; }
        .status-away { background-color: #ffc107; }
        
        .availability-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .time-slot {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            margin: 5px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: #2196F3;
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <!-- Sidebar -->
                <div class="bg-light p-3" style="min-height: 100vh;">
                    <h5>Astrologer Panel</h5>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="#"><i class="fas fa-clock"></i> Availability</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="fas fa-calendar"></i> Bookings</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="fas fa-comments"></i> Chat</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#"><i class="fas fa-user"></i> Profile</a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="col-md-9">
                <div class="p-4">
                    <h2>Availability Management</h2>
                    
                    <!-- Current Status -->
                    <div class="availability-card">
                        <h4>Current Status</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="status-indicator status-offline" id="online-indicator"></span>
                                    <span id="online-status-text">Offline</span>
                                </div>
                                
                                <label class="toggle-switch">
                                    <input type="checkbox" id="online-toggle">
                                    <span class="slider"></span>
                                </label>
                                <label for="online-toggle" class="ms-2">Online Status</label>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="status-indicator status-offline" id="available-indicator"></span>
                                    <span id="available-status-text">Not Available</span>
                                </div>
                                
                                <label class="toggle-switch">
                                    <input type="checkbox" id="available-toggle">
                                    <span class="slider"></span>
                                </label>
                                <label for="available-toggle" class="ms-2">Available Now</label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Today's Schedule -->
                    <div class="availability-card">
                        <h4>Today's Availability</h4>
                        <p class="text-muted">Set your availability for today. This will override your weekly schedule.</p>
                        
                        <div id="today-slots">
                            <!-- Time slots will be loaded here -->
                        </div>
                        
                        <button type="button" class="btn btn-primary" id="add-time-slot">
                            <i class="fas fa-plus"></i> Add Time Slot
                        </button>
                        
                        <button type="button" class="btn btn-success ms-2" id="save-today-schedule">
                            <i class="fas fa-save"></i> Save Today's Schedule
                        </button>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="availability-card">
                        <h4>Quick Actions</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <button class="btn btn-success w-100" id="quick-available">
                                    <i class="fas fa-play"></i> Go Online & Available
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-warning w-100" id="quick-busy">
                                    <i class="fas fa-pause"></i> Online but Busy
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-secondary w-100" id="quick-break">
                                    <i class="fas fa-coffee"></i> Take a Break (15m)
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-danger w-100" id="quick-offline">
                                    <i class="fas fa-stop"></i> Go Offline
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // CSRF token setup
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Base API URL
        const apiBase = '/for_testing/public/api';
        
        // Current status
        let currentStatus = {
            is_online: false,
            is_available_now: false,
            today_availability: []
        };
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadCurrentStatus();
            setupEventListeners();
        });
        
        // Load current availability status
        async function loadCurrentStatus() {
            try {
                const response = await fetch(`${apiBase}/astrologer/availability/status`, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    currentStatus = data.data;
                    updateUI();
                } else {
                    console.error('Failed to load status');
                }
            } catch (error) {
                console.error('Error loading status:', error);
            }
        }
        
        // Update UI with current status
        function updateUI() {
            // Update online status
            const onlineIndicator = document.getElementById('online-indicator');
            const onlineText = document.getElementById('online-status-text');
            const onlineToggle = document.getElementById('online-toggle');
            
            if (currentStatus.is_online) {
                onlineIndicator.className = 'status-indicator status-online';
                onlineText.textContent = 'Online';
                onlineToggle.checked = true;
            } else {
                onlineIndicator.className = 'status-indicator status-offline';
                onlineText.textContent = 'Offline';
                onlineToggle.checked = false;
            }
            
            // Update available status
            const availableIndicator = document.getElementById('available-indicator');
            const availableText = document.getElementById('available-status-text');
            const availableToggle = document.getElementById('available-toggle');
            
            if (currentStatus.is_available_now && currentStatus.is_online) {
                availableIndicator.className = 'status-indicator status-online';
                availableText.textContent = 'Available Now';
                availableToggle.checked = true;
            } else if (currentStatus.is_online) {
                availableIndicator.className = 'status-indicator status-away';
                availableText.textContent = 'Busy';
                availableToggle.checked = false;
            } else {
                availableIndicator.className = 'status-indicator status-offline';
                availableText.textContent = 'Not Available';
                availableToggle.checked = false;
            }
            
            // Update today's slots
            renderTodaySlots();
        }
        
        // Render today's time slots
        function renderTodaySlots() {
            const slotsContainer = document.getElementById('today-slots');
            const slots = currentStatus.today_availability || [];
            
            slotsContainer.innerHTML = '';
            
            if (slots.length === 0) {
                slotsContainer.innerHTML = '<p class="text-muted">No time slots set for today.</p>';
                return;
            }
            
            slots.forEach((slot, index) => {
                const slotDiv = document.createElement('div');
                slotDiv.className = 'time-slot';
                slotDiv.innerHTML = `
                    <span>${slot.start_time} - ${slot.end_time}</span>
                    <button class="btn btn-sm btn-danger" onclick="removeTimeSlot(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
                slotsContainer.appendChild(slotDiv);
            });
        }
        
        // Setup event listeners
        function setupEventListeners() {
            // Online toggle
            document.getElementById('online-toggle').addEventListener('change', async function() {
                await toggleOnlineStatus(this.checked);
            });
            
            // Available toggle
            document.getElementById('available-toggle').addEventListener('change', async function() {
                await toggleAvailableNow(this.checked);
            });
            
            // Quick actions
            document.getElementById('quick-available').addEventListener('click', async function() {
                await toggleOnlineStatus(true);
                await toggleAvailableNow(true);
            });
            
            document.getElementById('quick-busy').addEventListener('click', async function() {
                await toggleOnlineStatus(true);
                await toggleAvailableNow(false);
            });
            
            document.getElementById('quick-offline').addEventListener('click', async function() {
                await toggleOnlineStatus(false);
                await toggleAvailableNow(false);
            });
            
            // Add time slot
            document.getElementById('add-time-slot').addEventListener('click', addTimeSlot);
            
            // Save today's schedule
            document.getElementById('save-today-schedule').addEventListener('click', saveTodaySchedule);
        }
        
        // Toggle online status
        async function toggleOnlineStatus(isOnline) {
            try {
                const response = await fetch(`${apiBase}/astrologer/availability/toggle-online`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ is_online: isOnline })
                });
                
                if (response.ok) {
                    await loadCurrentStatus();
                } else {
                    console.error('Failed to toggle online status');
                }
            } catch (error) {
                console.error('Error toggling online status:', error);
            }
        }
        
        // Toggle available now status
        async function toggleAvailableNow(isAvailable) {
            try {
                const response = await fetch(`${apiBase}/astrologer/availability/toggle-available-now`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ is_available_now: isAvailable })
                });
                
                if (response.ok) {
                    await loadCurrentStatus();
                } else {
                    console.error('Failed to toggle available now status');
                }
            } catch (error) {
                console.error('Error toggling available now status:', error);
            }
        }
        
        // Add time slot
        function addTimeSlot() {
            const startTime = prompt('Enter start time (HH:MM format, e.g., 09:00):');
            const endTime = prompt('Enter end time (HH:MM format, e.g., 17:00):');
            
            if (startTime && endTime) {
                // Validate time format
                const timeRegex = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/;
                if (!timeRegex.test(startTime) || !timeRegex.test(endTime)) {
                    alert('Please enter time in HH:MM format (e.g., 09:00)');
                    return;
                }
                
                if (startTime >= endTime) {
                    alert('End time must be after start time');
                    return;
                }
                
                currentStatus.today_availability = currentStatus.today_availability || [];
                currentStatus.today_availability.push({
                    start_time: startTime,
                    end_time: endTime
                });
                
                renderTodaySlots();
            }
        }
        
        // Remove time slot
        function removeTimeSlot(index) {
            currentStatus.today_availability.splice(index, 1);
            renderTodaySlots();
        }
        
        // Save today's schedule
        async function saveTodaySchedule() {
            try {
                const response = await fetch(`${apiBase}/astrologer/availability/set-today`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        slots: currentStatus.today_availability || []
                    })
                });
                
                if (response.ok) {
                    alert('Today\'s schedule saved successfully!');
                    await loadCurrentStatus();
                } else {
                    console.error('Failed to save today\'s schedule');
                    alert('Failed to save schedule. Please try again.');
                }
            } catch (error) {
                console.error('Error saving today\'s schedule:', error);
                alert('Error saving schedule. Please try again.');
            }
        }
        
        // Auto-refresh status every 30 seconds
        setInterval(loadCurrentStatus, 30000);
    </script>
</body>
</html>
