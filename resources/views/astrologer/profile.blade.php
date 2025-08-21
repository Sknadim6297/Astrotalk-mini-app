<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $astrologer->user->name }} - Astrologer Profile</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
        }
        
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid white;
            object-fit: cover;
        }
        
        .status-indicator {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            border: 3px solid white;
        }
        .status-online { background-color: #28a745; }
        .status-offline { background-color: #dc3545; }
        .status-busy { background-color: #ffc107; }
        
        .availability-card {
            border: 1px solid #dee2e6;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .chat-button {
            font-size: 18px;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        
        .chat-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .rating-stars {
            color: #ffc107;
        }
        
        .specialization-tag {
            background: #e9ecef;
            color: #495057;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            margin: 2px;
            display: inline-block;
        }
        
        .time-slot {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px 15px;
            margin: 5px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .experience-badge {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
        }
        
        .price-tag {
            font-size: 24px;
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3 text-center">
                    <img src="https://via.placeholder.com/150x150/667eea/ffffff?text={{ substr($astrologer->user->name, 0, 2) }}" 
                         alt="{{ $astrologer->user->name }}" class="profile-image">
                </div>
                <div class="col-md-6">
                    <h1 class="mb-2">{{ $astrologer->user->name }}</h1>
                    <div class="mb-3">
                        <span class="status-indicator" id="status-indicator"></span>
                        <span id="status-text">Loading...</span>
                    </div>
                    <div class="rating-stars mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $astrologer->average_rating ? '' : 'text-muted' }}"></i>
                        @endfor
                        <span class="ms-2">({{ $astrologer->total_reviews }} reviews)</span>
                    </div>
                    <div class="experience-badge">
                        {{ $astrologer->experience }} years experience
                    </div>
                </div>
                <div class="col-md-3 text-center">
                    <div class="price-tag mb-3">
                        ₹{{ $astrologer->per_minute_rate }}/min
                    </div>
                    <button class="btn btn-success chat-button" id="chat-button" disabled>
                        <i class="fas fa-comments"></i> Loading...
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-8">
                <!-- About Section -->
                <div class="availability-card">
                    <h3>About {{ $astrologer->user->name }}</h3>
                    <p>{{ $astrologer->bio ?? 'No bio available.' }}</p>
                    
                    <h5>Specializations</h5>
                    <div class="mb-3">
                        @if($astrologer->specialization)
                            @foreach($astrologer->specialization as $spec)
                                <span class="specialization-tag">{{ $spec }}</span>
                            @endforeach
                        @else
                            <span class="text-muted">No specializations listed.</span>
                        @endif
                    </div>
                    
                    <h5>Languages</h5>
                    <div>
                        @if($astrologer->languages)
                            @foreach($astrologer->languages as $lang)
                                <span class="specialization-tag">{{ $lang }}</span>
                            @endforeach
                        @else
                            <span class="text-muted">No languages listed.</span>
                        @endif
                    </div>
                </div>

                <!-- Education & Certifications -->
                @if($astrologer->education || $astrologer->certifications)
                <div class="availability-card">
                    <h3>Qualifications</h3>
                    @if($astrologer->education)
                        <h5>Education</h5>
                        <p>{{ $astrologer->education }}</p>
                    @endif
                    
                    @if($astrologer->certifications)
                        <h5>Certifications</h5>
                        <p>{{ $astrologer->certifications }}</p>
                    @endif
                </div>
                @endif
            </div>
            
            <div class="col-md-4">
                <!-- Availability Status -->
                <div class="availability-card">
                    <h4>Availability</h4>
                    <div id="availability-info">
                        <p class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading availability...</p>
                    </div>
                </div>

                <!-- Today's Schedule -->
                <div class="availability-card" id="schedule-card" style="display: none;">
                    <h4>Today's Schedule</h4>
                    <div id="today-schedule">
                        <!-- Schedule will be loaded here -->
                    </div>
                </div>

                <!-- Recent Reviews -->
                <div class="availability-card">
                    <h4>Recent Reviews</h4>
                    <div class="text-center text-muted">
                        <i class="fas fa-star"></i>
                        <p>Reviews coming soon...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const astrologerId = {{ $astrologer->id }};
        const apiBase = '/for_testing/public/api';
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadAvailabilityStatus();
            setInterval(loadAvailabilityStatus, 30000); // Refresh every 30 seconds
        });
        
        // Load astrologer availability status
        async function loadAvailabilityStatus() {
            try {
                const response = await fetch(`${apiBase}/astrologers/${astrologerId}/availability-status`);
                
                if (response.ok) {
                    const data = await response.json();
                    updateAvailabilityUI(data.data);
                } else {
                    console.error('Failed to load availability status');
                }
            } catch (error) {
                console.error('Error loading availability status:', error);
            }
        }
        
        // Update UI with availability data
        function updateAvailabilityUI(data) {
            const statusIndicator = document.getElementById('status-indicator');
            const statusText = document.getElementById('status-text');
            const chatButton = document.getElementById('chat-button');
            const availabilityInfo = document.getElementById('availability-info');
            const scheduleCard = document.getElementById('schedule-card');
            const todaySchedule = document.getElementById('today-schedule');
            
            // Update status indicator
            statusIndicator.className = 'status-indicator';
            
            if (data.availability_status.status === 'available') {
                statusIndicator.classList.add('status-online');
                statusText.textContent = 'Available Now';
                chatButton.className = 'btn btn-success chat-button';
                chatButton.innerHTML = '<i class="fas fa-comments"></i> Chat Now';
                chatButton.disabled = false;
                chatButton.onclick = startChat;
            } else if (data.availability_status.status === 'busy') {
                statusIndicator.classList.add('status-busy');
                statusText.textContent = 'Online but Busy';
                chatButton.className = 'btn btn-warning chat-button';
                chatButton.innerHTML = '<i class="fas fa-clock"></i> Currently Busy';
                chatButton.disabled = true;
            } else if (data.availability_status.status === 'offline') {
                statusIndicator.classList.add('status-offline');
                statusText.textContent = 'Offline';
                chatButton.className = 'btn btn-secondary chat-button';
                chatButton.innerHTML = '<i class="fas fa-moon"></i> Offline';
                chatButton.disabled = true;
            } else {
                statusIndicator.classList.add('status-offline');
                statusText.textContent = 'Not Available';
                chatButton.className = 'btn btn-secondary chat-button';
                chatButton.innerHTML = '<i class="fas fa-ban"></i> Not Available';
                chatButton.disabled = true;
            }
            
            // Update availability info
            let availabilityHtml = `
                <div class="text-center mb-3">
                    <h5>${data.availability_status.message}</h5>
                </div>
            `;
            
            if (data.last_seen_at) {
                availabilityHtml += `
                    <p class="text-muted small text-center">
                        <i class="fas fa-clock"></i> Last seen: ${data.last_seen_at}
                    </p>
                `;
            }
            
            availabilityInfo.innerHTML = availabilityHtml;
            
            // Show today's schedule if available
            if (data.availability_status.today_slots && data.availability_status.today_slots.length > 0) {
                scheduleCard.style.display = 'block';
                let scheduleHtml = '';
                
                data.availability_status.today_slots.forEach(slot => {
                    scheduleHtml += `
                        <div class="time-slot">
                            <span><i class="fas fa-clock"></i> ${slot.start_time} - ${slot.end_time}</span>
                        </div>
                    `;
                });
                
                todaySchedule.innerHTML = scheduleHtml;
            } else {
                scheduleCard.style.display = 'none';
            }
        }
        
        // Start chat function
        function startChat() {
            // Check if user is logged in
            @auth
                // Redirect to booking or chat
                alert('Starting chat session...');
                // Here you would typically create a booking and redirect to chat
                // window.location.href = `/chat/astrologer/${astrologerId}`;
            @else
                // Redirect to login
                alert('Please login to start a chat session.');
                window.location.href = '/login';
            @endauth
        }
    </script>
</body>
</html>
