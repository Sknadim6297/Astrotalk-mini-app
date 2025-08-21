<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Astrology Platform')</title>
    
    <!-- Poppins Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js for interactive components -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-shadow {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
      
    </style>
</head>
<body class="min-h-screen bg-gray-50 font-sans" x-data="navbar()">
    <!-- Navigation -->
    <nav class="gradient-bg star-pattern shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-2xl font-bold text-white"><i class="fas fa-star-and-crescent mr-2"></i>AstroConnect</h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ url('/') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium">Home</a>
                    <a href="{{ url('/astrologers') }}" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium">Find Astrologers</a>
                    
                    <!-- User Authentication State -->
                    <template x-if="!user">
                        <div class="flex items-center space-x-2">
                            <a href="{{ url('/auth/login') }}" class="bg-white text-purple-600 hover:bg-gray-100 px-4 py-2 rounded-md text-sm font-medium">Login</a>
                            <a href="{{ url('/auth/register') }}" class="bg-purple-800 text-white hover:bg-purple-900 px-4 py-2 rounded-md text-sm font-medium">Register</a>
                        </div>
                    </template>
                    
                    <!-- Logged In User Profile -->
                    <template x-if="user">
                        <div class="relative" x-data="{ showDropdown: false }">
                            <!-- Profile Display - Clickable -->
                            <div @click="showDropdown = !showDropdown" class="flex items-center space-x-3 cursor-pointer hover:bg-white/10 px-3 py-2 rounded-lg transition-colors">
                                <!-- Profile Photo -->
                                <img :src="user.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=7c3aed&color=fff&size=40'" 
                                     :alt="user.name" 
                                     class="w-10 h-10 rounded-full border-2 border-white/30">
                                
                                <!-- Name and Role -->
                                <div class="flex flex-col items-start">
                                    <span class="text-white font-medium text-sm" x-text="user.name"></span>
                                    <!-- Astrologer Badge -->
                                    <template x-if="user.role === 'astrologer'">
                                        <span class="text-yellow-300 text-xs font-medium flex items-center">
                                            <i class="fas fa-star mr-1"></i> Astrologer
                                        </span>
                                    </template>
                                    <!-- Admin Badge -->
                                    <template x-if="user.role === 'admin'">
                                        <span class="text-red-300 text-xs font-medium flex items-center">
                                            <i class="fas fa-bolt mr-1"></i> Administrator
                                        </span>
                                    </template>
                                    <!-- User Badge -->
                                    <template x-if="user.role === 'user'">
                                        <span class="text-blue-300 text-xs font-medium">
                                            <i class="fas fa-user mr-1"></i> User
                                        </span>
                                    </template>
                                </div>
                                
                                <!-- Dropdown Arrow -->
                                <svg class="w-4 h-4 text-white/70 transition-transform" 
                                     :class="showDropdown ? 'rotate-180' : ''" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>

                            <!-- Dropdown Menu -->
                            <div x-show="showDropdown" 
                                 @click.away="showDropdown = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                                
                                <!-- User Menu (Regular Users) -->
                                <template x-if="user.role === 'user'">
                                    <div>
                                        <a href="/profile" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-user w-5 h-5 mr-3"></i>
                                            Profile
                                        </a>
                                        <a href="/wallet/dashboard" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-wallet w-5 h-5 mr-3"></i>
                                            Wallet
                                        </a>
                                        <a href="/appointments" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-calendar-alt w-5 h-5 mr-3"></i>
                                            Appointments
                                        </a>
                                        <hr class="my-1 border-gray-200">
                                        <button @click="logout(); showDropdown = false" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i>
                                            Logout
                                        </button>
                                    </div>
                                </template>

                                <!-- Astrologer Menu -->
                                <template x-if="user.role === 'astrologer'">
                                    <div>
                                        <a href="/astrologer/dashboard" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-chart-line w-5 h-5 mr-3"></i>
                                            Dashboard
                                        </a>
                                        <a href="/astrologer/bookings" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-book-open w-5 h-5 mr-3"></i>
                                            Bookings
                                        </a>
                                        <a href="/astrologer/edit-profile" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-user w-5 h-5 mr-3"></i>
                                            Edit Profile
                                        </a>
                                        <a href="/astrologer/availability" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-clock w-5 h-5 mr-3"></i>
                                            Availability
                                        </a>
                                        <a href="/astrologer/reviews" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-star w-5 h-5 mr-3"></i>
                                            Reviews
                                        </a>
                                        <hr class="my-1 border-gray-200">
                                        <button @click="logout(); showDropdown = false" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i>
                                            Logout
                                        </button>
                                    </div>
                                </template>

                                <!-- Admin Menu -->
                                <template x-if="user.role === 'admin'">
                                    <div>
                                        <a href="/admin/dashboard" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-bolt w-5 h-5 mr-3"></i>
                                            Admin Panel
                                        </a>
                                        <a href="/admin/users" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-users w-5 h-5 mr-3"></i>
                                            Manage Users
                                        </a>
                                        <a href="/admin/astrologers" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            <i class="fas fa-star w-5 h-5 mr-3"></i>
                                            Manage Astrologers
                                        </a>
                                        <hr class="my-1 border-gray-200">
                                        <button @click="logout(); showDropdown = false" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i>
                                            Logout
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="gradient-bg text-white py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h3 class="text-lg font-semibold mb-2"><i class="fas fa-star-and-crescent mr-2"></i>AstroConnect</h3>
                <p class="text-gray-200 mb-4">Connect with verified astrologers for personalized guidance</p>
                <div class="flex justify-center space-x-6 text-sm">
                    <a href="#" class="hover:text-gray-300">About Us</a>
                    <a href="#" class="hover:text-gray-300">Contact</a>
                    <a href="#" class="hover:text-gray-300">Privacy Policy</a>
                    <a href="#" class="hover:text-gray-300">Terms of Service</a>
                </div>
                <div class="mt-4 text-gray-300 text-sm">
                    © 2024 AstroConnect. All rights reserved.
                </div>
            </div>
        </div>
    </footer>

    <script>
        function navbar() {
            return {
                user: null,

                init() {
                    // Check if user is logged in
                    this.checkAuthStatus();
                },

                checkAuthStatus() {
                    const token = localStorage.getItem('api_token');
                    console.log('Checking auth status, token:', token ? 'exists' : 'none');
                    
                    if (token) {
                        // Make API call to get user info
                        fetch('/api/auth/me', {
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => {
                            console.log('Auth API response status:', response.status);
                            return response.json();
                        })
                        .then(data => {
                            console.log('Auth API response data:', data);
                            if (data.status === 'success' && data.data && data.data.user) {
                                this.user = data.data.user;
                                console.log('User set:', this.user);
                            } else {
                                console.log('Auth check failed, removing token');
                                localStorage.removeItem('api_token');
                                this.user = null;
                            }
                        })
                        .catch(error => {
                            console.error('Auth check failed:', error);
                            localStorage.removeItem('api_token');
                            this.user = null;
                        });
                    }
                },

                async logout() {
                    const token = localStorage.getItem('api_token');
                    
                    if (token) {
                        try {
                            await fetch('/api/auth/logout', {
                                method: 'POST',
                                headers: {
                                    'Authorization': `Bearer ${token}`,
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });
                        } catch (error) {
                            console.error('Logout API call failed:', error);
                        }
                    }
                    
                    localStorage.removeItem('api_token');
                    this.user = null;
                    showToast('Logged out successfully!', 'success');
                    
                    // Redirect to home after logout
                    window.location.href = '/';
                }
            }
        }

        // Global function to refresh auth status (called after login)
        window.refreshAuthStatus = function() {
            const navElement = document.querySelector('[x-data="navbar()"]');
            if (navElement && navElement._x_dataStack && navElement._x_dataStack[0]) {
                navElement._x_dataStack[0].checkAuthStatus();
            }
        };

        // Show toast notifications
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 p-4 rounded-md text-white z-50 transition-opacity duration-300 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Helper function for API calls
        async function apiCall(endpoint, method = 'GET', data = null) {
            const options = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            };
            
            if (data) {
                options.body = JSON.stringify(data);
            }
            
            // Add auth token if exists
            const token = localStorage.getItem('api_token');
            if (token) {
                options.headers.Authorization = `Bearer ${token}`;
            }
            
            const response = await fetch('/api' + endpoint, options);
            return await response.json();
        }
    </script>
    
    @yield('scripts')
</body>
</html>
